<?php

namespace LaravelCascade\ComposerInstaller\Exceptions;

use Exception;

class InstallerException extends Exception
{
    public static function fromInvalidPackage(string $invalidPackageName): self
    {
        return new self(
            "Ensure your package's name ({$invalidPackageName}) is in the format <vendor>/<name>-<module>"
        );
    }
}