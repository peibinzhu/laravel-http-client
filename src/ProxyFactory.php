<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use PeibinLaravel\HttpClient\Proxy\Ast;
use PeibinLaravel\HttpClient\Proxy\CodeLoader;
use PeibinLaravel\HttpClient\Utils\Filesystem;
use PeibinLaravel\Utils\Traits\Container;

class ProxyFactory
{
    use Container;

    /**
     * @var Ast
     */
    protected $ast;

    /**
     * @var CodeLoader
     */
    protected $codeLoader;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct()
    {
        $this->ast = new Ast();
        $this->codeLoader = new CodeLoader();
        $this->filesystem = new Filesystem();
    }

    public function createProxy($serviceClass): string
    {
        if (self::has($serviceClass)) {
            return (string)self::get($serviceClass);
        }

        $dir = base_path('runtime/container/proxy/');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $proxyFileName = str_replace('\\', '_', $serviceClass);
        $proxyClassName = $serviceClass . '_' . md5($this->codeLoader->getCodeByClassName($serviceClass));
        $path = $dir . $proxyFileName . '.http-client.proxy.php';
        $key = md5($path);
        if ($this->isModified($serviceClass, $path)) {
            $targetPath = $path . '.' . uniqid();
            $code = $this->ast->proxy($serviceClass, $proxyClassName);
            file_put_contents($targetPath, $code);
            rename($targetPath, $path);
        }

        include_once $path;
        self::set($serviceClass, $proxyClassName);
        return $proxyClassName;
    }

    protected function isModified(string $interface, string $path): bool
    {
        if (!$this->filesystem->exists($path)) {
            return true;
        }

        $time = $this->filesystem->lastModified(
            $this->codeLoader->getPathByClassName($interface)
        );

        return $time >= $this->filesystem->lastModified($path);
    }
}
