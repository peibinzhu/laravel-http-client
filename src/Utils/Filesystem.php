<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Utils;

class Filesystem
{
    /**
     * Determine if a file or directory exists.
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Get the file's last modification time.
     */
    public function lastModified(string $path): int
    {
        return filemtime($path);
    }
}
