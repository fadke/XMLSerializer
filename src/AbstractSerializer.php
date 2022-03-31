<?php

declare(strict_types=1);

namespace Fadke\Serializers;

abstract class AbstractSerializer 
{
    /**
     * @return mixed
     */
    abstract public function serialize(array $params);
}
