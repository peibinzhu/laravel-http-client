<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Listeners;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use InvalidArgumentException;
use PeibinLaravel\Di\Annotation\AnnotationCollector;
use PeibinLaravel\Di\ReflectionManager;
use PeibinLaravel\HttpClient\Annotation\Service;
use PeibinLaravel\HttpClient\Annotation\ServiceGroup;
use PeibinLaravel\HttpClient\ProxyFactory;

class AddConsumerDefinitionListener
{
    public function __construct(protected Container $container)
    {
    }

    public function handle(object $event): void
    {
        $container = $this->container;
        $consumers = $container->get(Repository::class)->get('services.consumers', []);
        if ($consumers) {
            $annotationData = $this->collectAnnotationData(AnnotationCollector::list());
            $serviceFactory = $container->get(ProxyFactory::class);

            foreach ($consumers as $serviceName => $consumerItems) {
                foreach ($annotationData[$serviceName] ?? [] as $serviceClass => $methodData) {
                    if (!interface_exists($serviceClass)) {
                        continue;
                    }

                    $proxyClass = $serviceFactory->createProxy($serviceClass);
                    $container->bind(
                        $serviceClass,
                        function () use ($proxyClass, $serviceName, $methodData) {
                            return new $proxyClass(
                                fn () => Container::getInstance(),
                                $serviceName,
                                $methodData
                            );
                        }
                    );
                }
            }
        }
    }

    /**
     * Collect annotation data.
     * @param array $collector
     * @return array
     */
    private function collectAnnotationData(array $collector): array
    {
        $data = [];
        foreach ($collector as $className => $metadata) {
            /** @var ServiceGroup $groupAnnotation */
            if ($groupAnnotation = $metadata['_c'][ServiceGroup::class] ?? null) {
                $reflectionMethods = ReflectionManager::reflectClass($className)->getMethods();

                $group = $groupAnnotation->value;
                $data[$group] = $data[$group] ?? [];

                $methodMetadata = $metadata['_m'] ?? [];
                foreach ($reflectionMethods as $reflectionMethod) {
                    $method = $reflectionMethod->getName();
                    $position = $className . '::' . $method;
                    if (!isset($methodMetadata[$method])) {
                        $message = "Annotation parameter not configured for {$position}.";
                        throw new InvalidArgumentException($message);
                    }
                    if (!($annotation = $methodMetadata[$method][Service::class] ?? null)) {
                        $message = "Service annotation parameter not configured for {$position}.";
                        throw new InvalidArgumentException($message);
                    }

                    $data[$group] = $data[$group] ?? [];
                    $data[$group][$className] = $data[$group][$className] ?? [];
                    $data[$group][$className][$method] = $annotation;
                }
            }
        }
        return $data;
    }
}
