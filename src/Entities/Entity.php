<?php

namespace Benzine\ORM\Entities;

use Benzine\Migrator\Traits\Support;

abstract class Entity
{
    /** @var Support */
    protected $container;

    public function getContainer(): Support
    {
        return $this->container;
    }

    public function setContainer(Support $container): Entity
    {
        $this->container = $container;

        return $this;
    }
}
