<?php

namespace GeminiLabs\SiteReviews\Addon\Forms;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Upgrader
{
    /**
     * @return void
     */
    public function run()
    {
        $files = $this->getUpgraderFiles();
        array_walk($files, function ($file) {
            $className = str_replace('.php', '', $file);
            $version = str_replace(['Upgrade_', '_'], ['', '.'], $className);
            $versionSuffix = preg_replace('/[\d.]+(.+)?/', '${1}', Application::load()->version); // allow alpha/beta versions
            if (version_compare($this->currentVersion(), $version.$versionSuffix, '>=')) {
                return;
            }
            glsr(__NAMESPACE__.'\Upgrades\\'.$className);
            glsr_log()->info('Completed Upgrade for '.Application::load()->name.' v'.$version.$versionSuffix);
        });
        $this->updateVersion();
    }

    /**
     * @return void
     */
    public function updateVersion()
    {
        $versionPath = 'addons.'.Application::ID.'.version';
        $versionUpgradedFromPath = $versionPath.'_upgraded_from';
        $currentVersion = $this->currentVersion();
        if ($currentVersion != Application::load()->version) {
            glsr(OptionManager::class)->set($versionPath, Application::load()->version);
            if ('0.0.0' !== $currentVersion) {
                glsr(OptionManager::class)->set($versionUpgradedFromPath, $currentVersion);
            }
        }
    }

    /**
     * @return string
     */
    protected function currentVersion()
    {
        return glsr(OptionManager::class)->get('addons.'.Application::ID.'.version', '0.0.0');
    }

    /**
     * @return array
     */
    protected function getUpgraderFiles()
    {
        $files = [];
        $upgradeDir = dirname(__FILE__).'/Upgrades';
        if (is_dir($upgradeDir)) {
            $iterator = new DirectoryIterator($upgradeDir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $files[] = $fileinfo->getFilename();
                }
            }
            natsort($files);
        }
        return $files;
    }
}
