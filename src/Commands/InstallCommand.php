<?php

namespace Leaf\Redis\Commands;

use Aloe\Command;
use Leaf\FS;

class InstallCommand extends Command
{
    protected static $defaultName = "redis:install";
    public $description = "Install leaf redis config";
    public $help = "Install leaf redis config";

    protected function updateConfig()
    {
        $installablesDir = __DIR__ . "/stubs";
        $installables = FS::listFolders($installablesDir);

        foreach ($installables as $installableDir) {
            $dir = FS::listDir($installableDir);
            $trueDir = str_replace($installablesDir, "", $installableDir);

            if (!is_dir(\Aloe\Command\Config::rootpath("$trueDir"))) {
                FS::createFolder(\Aloe\Command\Config::rootpath("$trueDir"));
            }

            foreach ($dir as $installable) {
                FS::superCopy(
                    "$installableDir/$installable",
                    \Aloe\Command\Config::rootpath("$trueDir/$installable")
                );
            }
        }
    }

    protected function updateEnv()
    {
        $env = \Aloe\Command\Config::rootpath(".env");
        $envExample = \Aloe\Command\Config::rootpath(".env.example");

        $envData = "
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null";

        if (file_exists($env)) {
            $envContent = file_get_contents($env);
            file_put_contents($env, str_replace($envData, "", $envContent) . $envData);
        }

        if (file_exists($envExample)) {
            $envExampleContent = file_get_contents($envExample);
            file_put_contents($envExample, str_replace($envData, "", $envExampleContent) . $envData);
        }
    }

    protected function handle()
    {
        $this->updateConfig();
        $this->updateEnv();

        $this->comment("Leaf redis installed successfully!");
    }
}
