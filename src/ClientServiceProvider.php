<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use PeibinLaravel\Di\Annotation\AnnotationCollector;
use PeibinLaravel\Di\Annotation\ReflectionManager;
use PeibinLaravel\HttpClient\Annotation\Service;
use PeibinLaravel\HttpClient\Annotation\ServiceGroup;

class ClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerProxy();
    }

    private function registerProxy()
    {
        $app = $this->app;
        $consumers = config('services.consumers', []);
        if ($consumers) {
            $annotationData = $this->collectAnnotationData(AnnotationCollector::list());
            $serviceFactory = $this->app->make(ProxyFactory::class);

            foreach ($consumers as $group => $consumerItems) {
                foreach ($annotationData[$group] ?? [] as $serviceClass => $methodData) {
                    if (!interface_exists($serviceClass)) {
                        continue;
                    }

                    $option = $consumerItems;
                    $proxyClass = $serviceFactory->createProxy($serviceClass);
                    $app->bind(
                        $serviceClass,
                        function () use ($proxyClass, $methodData, $option) {
                            return new $proxyClass(
                                fn () => Container::getInstance(),
                                $methodData,
                                $option
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
