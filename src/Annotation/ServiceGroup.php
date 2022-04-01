<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Annotation;

use Attribute;
use PeibinLaravel\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS)]
class ServiceGroup extends AbstractAnnotation
{
    public $value;
}
