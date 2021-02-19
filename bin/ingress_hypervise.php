<?php

namespace App;
require __DIR__ . "/../vendor/autoload.php";

use Rudl\IngressNginx\Template;
use Rudl\LibGitDb\RudlGitDbClient;
use Rudl\LibGitDb\Type\Ingress\T_IngressConfig;

$gitDb = new RudlGitDbClient(GITDB_URL);

$gitDb->syncObjects(CERT_SCOPE, CERT_STORE_DIR);



$ingressObj = $gitDb->listObjects(CLUSTER_SCOPE)->getObject("ingress.nginx.yml");
if ($ingressObj === null)
    throw new \InvalidArgumentException("Ingress config 'ingress.nginx.yml' not found in scope: " . CLUSTER_SCOPE);

$ingressConfig = phore_hydrate(yaml_parse($ingressObj->content), T_IngressConfig::class);
assert($ingressConfig instanceof T_IngressConfig);

$template = new Template(VHOST_TEMPLATE_FILE, VHOST_TARGET_FILE);

if ($template->parse($ingressConfig)) {

    $gitDb->logOk("nginx config changed - reloading server");
    try {
        phore_exec("service nginx reload");
    } catch (\Exception $e) {
        $gitDb->logError("reload failed: " . $e->getMessage() . "\n" . phore_exec("nginx -t"));
    }
}

try {
    phore_http_request("http://localhost/rudl-cf-selftest")->send();
} catch (\Exception $ex) {
    $gitDb->logError("Nginx not running - restarting");
    try {
        phore_exec("service nginx restart");
    } catch (\Exception $e) {
        $gitDb->logError("Cant restart nginx: " . $e->getMessage() . "\n" . phore_exec("nginx -t"));
        sleep(10);
    }

}
