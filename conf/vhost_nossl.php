<?php
namespace Tpl;

use Rudl\IngressNginx\HostValidator;

/* @var $vhost \Rudl\LibGitDb\Type\Ingress\T_IngressConfig_VHost */
/* @var $sslCertFile null|string */
?>

server {

    server_name <?php echo implode(" ", $vhost->server_names) ?>;
    add_header X-Request-Id $request_id always;

    <?php if ($sslMode === true): ?>

        listen 443 ssl; listen [::]:443 ssl;
        ssl_certificate       <?php echo $sslCertFile; ?>;
        ssl_certificate_key   <?php echo $sslCertFile; ?>;

    <?php else: ?>

        listen 80; listen [::]:80;

    <?php endif; ?>

    <?php if ($sslMode === false && $certIssuerUrl !== null): ?>
    location /.well-known/acme-challenge {
        limit_req zone=manager_limit burst=50 nodelay;
        proxy_set_header Host $host;
        proxy_set_header X-Request-Id $request_id;

        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_pass <?php echo $certIssuerUrl ?>/.well-known/acme-challenge;
        limit_except GET {
            deny all;
        }
    }
    <?php else: ?>
        location /.well-known/acme-challenge {
            return 500 'ERROR 500: IssuerURL not configured. CF#05';
        }
    <?php endif; ?>

    <?php foreach ($vhost->locations as $location): ?>

        <?php
        $proxyPassIp = HostValidator::convertUrlToHostAddr($location->proxy_pass);

        if ($proxyPassIp === null): ?>
            location <?php echo $location->location; ?> {
                return 404 'ERROR 404: Upstream service missing "$scheme://$host$request_uri"\n\ncloudfront req_id: $request_id\nhost: $hostname\n\nCode: CF#02';
            }
        <?php else: ?>


            location <?php echo $location->location; ?> {
                limit_req zone=global_limit burst=200 nodelay;

                proxy_set_header Host $host;
                proxy_set_header X-Request-Id $request_id;
                proxy_set_header X-Real-IP $remote_addr;
                proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                proxy_set_header X-Forwarded-Proto http;

                <?php if ($sslCertFile !== null && $vhost->enforce_ssl && $sslMode === false): ?>

                    if ($request_method = GET) {
                        return 301 https://$host$request_uri;
                    }
                    return 403 'ERROR 403: SSL Encryption required: Unencrypted http request to encypted endpoint. "$scheme://$host$request_uri"\n\ncloudfront req_id: $request_id\nhost: $hostname\n\nCode: CF#02';

                <?php else: ?>

                    proxy_pass <?php echo $proxyPassIp ?>;

                <?php endif; ?>

            }

        <?php endif; ?>

    <?php endforeach; ?>

}