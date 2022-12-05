<?php

namespace LaravelCascade\ComposerInstaller;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Joshbrw\LaravelModuleInstaller\Exceptions\LaravelModuleInstallerException;
use LaravelCascade\ComposerInstaller\Exceptions\InstallerException;

class PackageManager extends LibraryInstaller
{
    const DEFAULT_ROOT = "Modules";
    const DEFAULT_THEMES_ROOT = "Themes";
    const DEFAULT_PLUGINS_ROOT = "Plugins";

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->getBaseInstallationPath() . '/' . $this->getModuleName($package);
    }

    /**
     * Get the base path that the module should be installed into.
     * Defaults to Modules/ and can be overridden in the module's composer.json.
     *
     * @return string
     */
    protected function getBaseInstallationPath()
    {
        if (!$this->composer || !$this->composer->getPackage()) {
            return self::DEFAULT_ROOT;
        }

        $extra = $this->composer->getPackage()->getExtra();
        if (!$extra || empty($extra['module-dir'])) {
            $type = $this->type;
            $path = self::DEFAULT_ROOT;
            switch($type)
            {
                case 'laravel-theme' : $path = self::DEFAULT_THEMES_ROOT; break;
                case 'laravel-plugin' : $path = self::DEFAULT_PLUGINS_ROOT; break;
            }
            return  $path;
        }
        
        return $extra['module-dir'];
    }

    /**
     * Get the module name, i.e. "joshbrw/something-module" will be transformed into "Something"
     *
     * @param PackageInterface $package Compose Package Interface
     *
     * @return string Module Name
     *
     * @throws LaravelModuleInstallerException
     */
    protected function getModuleName(PackageInterface $package)
    {
        $name = $package->getPrettyName();
        $split = explode("/", $name);

        if (count($split) !== 2) {
            throw InstallerException::fromInvalidPackage($name);
        }

        $splitNameToUse = explode("-", $split[1]);

        if (count($splitNameToUse) < 2) {
            throw InstallerException::fromInvalidPackage($name);
        }
        $extensions = ['module', 'theme', 'plugin'];
        if (!in_array(array_pop($splitNameToUse), $extensions)) {
            throw InstallerException::fromInvalidPackage($name);
        }

        return implode('', array_map('ucfirst', $splitNameToUse));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return in_array($packageType, ['laravel-module', 'laravel-theme', 'laravel-plugin']);
    }
}