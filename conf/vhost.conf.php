<?php

namespace Tpl;
use Rudl\IngressNginx\HostValidator;
use Rudl\LibGitDb\Type\Ingress\T_IngressConfig;

assert($__CONF instanceof T_IngressConfig);

?>

limit_req_zone $binary_remote_addr zone=global_limit:10m rate=200r/s;
limit_req_zone $binary_remote_addr zone=manager_limit:10m rate=5r/s;

<?php
foreach ($__CONF->vhosts as $vhost) {
    $sslCertFile = null;
    $certIssuerUrl = HostValidator::convertUrlToHostAddr(CERT_ISSUER_URL);
    if (strlen($vhost->ssl_cert) > 1) {
        $sslCertFile = CERT_STORE_DIR . "/" . $vhost->ssl_cert;
        $sslMode = true;
        require __DIR__ . "/vhost_nossl.php";
    }

    $sslMode = false;
    require __DIR__ . "/vhost_nossl.php";
}
?>

