<?php

namespace App;
require __DIR__ . "/../vendor/autoload.php";

use Rudl\IngressNginx\IngressUpdater;
use Rudl\LibGitDb\RudlGitDbClient;
use Rudl\LibGitDb\UpdateRunner;

$gitdb = new RudlGitDbClient();
try {
    $gitdb->loadClientConfigFromEnv();
} catch (\Exception $e) {
    echo "\n\nEMERGENCY! EMERGENCY! EMERGENCY! EMERGENCY! EMERGENCY! EMERGENCY !EMERGENCY! EMERGENCY! EMERGENCY! \n\n";
    echo "LoadSystemConfig failed: " . $e->getMessage() . "\n";
    echo "\nThis is a permananent configuration error! Please correct environment and redeploy!\n\n";
    echo "\nThis system will shutdown in 30sec\n";
    sleep(30);
    throw $e;
}

echo "Startup configuration: " . json_encode([
    "DEV_MODE" => DEV_MODE,
    "RUDL_GITDB_URL" => RUDL_GITDB_URL,
    "RUDL_GITDB_CLIENT_ID" => RUDL_GITDB_CLIENT_ID,
    "INGRESS_SCOPE" => INGRESS_SCOPE,
    "INGRESS_OBJECT_NAME" => INGRESS_OBJECT_NAME,
    "SSL_CERT_SCOPE" => SSL_CERT_SCOPE,
    "SSL_CERT_ISSUER_URL" => SSL_CERT_ISSUER_URL,
    "CERT_STORE_DIR" => CERT_STORE_DIR,
    "VHOST_TEMPLATE_FILE" => VHOST_TEMPLATE_FILE,
    "VHOST_TARGET_FILE" => VHOST_TARGET_FILE,
    "ACME_TEMPLATE_FILE" => ACME_TEMPLATE_FILE,
    "ACME_TARGET_FILE" => ACME_TARGET_FILE,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";

$runner = new UpdateRunner($gitdb);

// Update all 5 runs to update newly deployed Vhosts
$runner->run(new IngressUpdater($gitdb), 5);
