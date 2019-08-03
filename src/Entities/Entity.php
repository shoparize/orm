<?php

namespace ⌬\Database\Entities;

use ⌬\Migrator\Traits\Support;

abstract class Entity
{
    /** @var Support */
    protected $container;

    /**
     * @return Support
     */
    public function getContainer(): Support
    {
        return $this->container;
    }

    /**
     * @param Support $container
     *
     * @return Entity
     */
    public function setContainer(Support $container): Entity
    {
        $this->container = $container;

        return $this;
    }
}
