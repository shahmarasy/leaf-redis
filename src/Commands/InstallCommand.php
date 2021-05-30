<?php

namespace Leaf\Redis\Commands;

use Aloe\Command;
use Leaf\FS;

class InstallCommand extends Command
{
    protected static $defaultName = "redis:install";
    public $description = "Install leaf redis config";
    public $help = "Install leaf redis config";

    protected function getStubs()
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

    protected function handle()
    {
        $this->getStubs();
        $this->comment("Leaf redis installed successfully!");
    }
}
