<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Proxy;

use InvalidArgumentException;
use PeibinLaravel\CodeParser\PhpParser;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeVisitorAbstract;

class ProxyCallVisitor extends NodeVisitorAbstract
{
    protected array $nodes = [];

    public function __construct(private string $classname, private array $parentStmts)
    {
    }

    public function beforeTraverse(array $nodes)
    {
        $this->nodes = $nodes;

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Interface_) {
            $node->stmts = $this->generateStmts();
            return new Node\Stmt\Class_($this->classname, [
                'stmts'      => $node->stmts,
                'extends'    => new Node\Name\FullyQualified(AbstractProxyService::class),
                'implements' => [
                    $node->name,
                ],
            ]);
        }
        return null;
    }

    public function generateStmts(): array
    {
        $methods = PhpParser::getInstance()->getAllMethodsFromStmts($this->nodes);
        $stmts = [];
        foreach ($methods as $method) {
            $stmts[] = $this->overrideMethod($method);
        }

        foreach ($this->parentStmts as $stmt) {
            $methods = PhpParser::getInstance()->getAllMethodsFromStmts($stmt);
            foreach ($methods as $method) {
                $stmts[] = $this->overrideMethod($method);
            }
        }

        return $stmts;
    }

    protected function overrideMethod(Node\FunctionLike $stmt): Node\Stmt\ClassMethod
    {
        if (!$stmt instanceof Node\Stmt\ClassMethod) {
            throw new InvalidArgumentException('stmt must instanceof Node\Stmt\ClassMethod');
        }
        $stmt->stmts = value(function () use ($stmt) {
            $methodCall = new Node\Expr\MethodCall(
                new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), new Node\Identifier('client')),
                new Node\Identifier('__call'),
                [
                    new Node\Arg(new Node\Scalar\MagicConst\Function_()),
                    new Node\Arg(new Node\Expr\FuncCall(new Node\Name('func_get_args'))),
                ]
            );
            if ($this->shouldReturn($stmt)) {
                return [new Node\Stmt\Return_($methodCall)];
            }
            return [new Node\Stmt\Expression($methodCall)];
        });

        return $stmt;
    }

    protected function shouldReturn(Node\Stmt\ClassMethod $stmt): bool
    {
        return $stmt->getReturnType() instanceof Node\NullableType
            || $stmt->getReturnType() instanceof Node\UnionType
            || ((string)$stmt->getReturnType()) !== 'void';
    }
}
