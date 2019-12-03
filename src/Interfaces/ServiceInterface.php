<?php

namespace ⌬\Database\Interfaces;

use Zend\Db\Sql\Expression;

interface ServiceInterface
{
    /**
     * @param string|Expression| null $order
     *
     * @return ModelInterface[]
     */
    public function getAll(
        int $limit = null,
        int $offset = null,
        array $wheres = null,
        $order = null,
        string $orderDirection = null
    );

    public function getById(int $id);

    public function getByField(string $field, $value);

    public function getRandom();
}
