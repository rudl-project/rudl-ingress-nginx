<?php


namespace Rudl\IngressNginx;


use Rudl\LibGitDb\RudlGitDbClient;
use Rudl\LibGitDb\Type\Ingress\T_IngressConfig;

class IngressUpdater
{

    public function __construct(
        private RudlGitDbClient $gitdb
    ){}


    private function syncSslCerts(string $sourceScope, string $targetDir)
    {
        $this->gitdb->syncObjects($sourceScope, $targetDir);
    }

    private function loadIngressConfig(string $scope, string $ingressObjectName) : T_IngressConfig
    {
        $ingressObj = $this->gitdb->listObjects($scope)->getObject($ingressObjectName);
        if ($ingressObj === null)
            throw new \InvalidArgumentException("Ingress config object '$ingressObjectName' not found in scope '$scope'");

        $ingressConfig = phore_hydrate(yaml_parse($ingressObj->content), T_IngressConfig::class);
        assert($ingressConfig instanceof T_IngressConfig);
        return $ingressConfig;

    }

    private function parseTemplate (string $templateFile, string $targetFile, T_IngressConfig $config) : bool
    {
        $template = new Template($templateFile, $targetFile);
        return $template->parse($config);
    }


    private function checkNginxIsRunning()
    {
        try {
            phore_http_request("http://localhost/rudl-cf-selftest")->send();
        } catch (\Exception $ex) {
            $this->gitdb->logError("Nginx not running - restarting");
            try {
                phore_exec("service nginx restart");
            } catch (\Exception $e) {
                $this->gitdb->logError("Cant restart nginx: " . $e->getMessage() . "\n" . phore_exec("nginx -t"));
                sleep(10);
            }

        }
    }


    public function __invoke()
    {
        $this->syncSslCerts(SSL_CERT_SCOPE, CERT_STORE_DIR);
        $ingressConfig = $this->loadIngressConfig(INGRESS_SCOPE, INGRESS_OBJECT_NAME);

        $updateRequired = $this->parseTemplate(VHOST_TEMPLATE_FILE, VHOST_TARGET_FILE, $ingressConfig);
        if ($updateRequired) {
            try {
                phore_exec("service nginx reload");
                $this->gitdb->logOk("Nginx reloaded after config change");
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }


}