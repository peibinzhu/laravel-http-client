<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Proxy;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;

class Ast
{
    /**
     * @var Parser
     */
    protected $astParser;

    /**
     * @var PrettyPrinterAbstract
     */
    protected $printer;

    /**
     * @var CodeLoader
     */
    protected $codeLoader;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->astParser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $this->printer = new Standard();
        $this->codeLoader = new CodeLoader();
    }

    public function proxy(string $className, string $proxyClassName)
    {
        if (!interface_exists($className)) {
            throw new \InvalidArgumentException("'{$className}' should be an interface name");
        }

        if (str_contains($proxyClassName, '\\')) {
            $exploded = explode('\\', $proxyClassName);
            $proxyClassName = end($exploded);
        }

        $code = $this->codeLoader->getCodeByClassName($className);
        $stmts = $this->astParser->parse($code);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new ProxyCallVisitor($proxyClassName));
        $modifiedStmts = $traverser->traverse($stmts);
        return $this->printer->prettyPrintFile($modifiedStmts);
    }
}
