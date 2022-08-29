<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Proxy;

use PeibinLaravel\Utils\Composer;

class CodeLoader
{
    public function getCodeByClassName(string $className): string
    {
        $file = Composer::getLoader()->findFile($className);
        if (!$file) {
            return '';
        }
        return file_get_contents($file);
    }

    public function getPathByClassName(string $className): string
    {
        return Composer::getLoader()->findFile($className);
    }
}
