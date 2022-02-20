<?php
namespace Tpl;

use Rudl\IngressNginx\HostValidator;
use Rudl\LibGitDb\Type\Ingress\T_IngressConfig;

$certIssuerUrl = HostValidator::convertUrlToHostAddr(SSL_CERT_ISSUER_URL);

?>


<?php if ($certIssuerUrl !== null): ?>
location /.well-known/acme-challenge {
    limit_req zone=acme_limit burst=10 nodelay;
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