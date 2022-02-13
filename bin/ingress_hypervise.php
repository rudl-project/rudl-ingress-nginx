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

$runner = new UpdateRunner($gitdb);

// Update all 5 runs to update newly deployed Vhosts
$runner->run(new IngressUpdater($gitdb), 5);
