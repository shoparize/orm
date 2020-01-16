<?php

namespace ⌬\Database;

use Laminas\Db\Adapter\AdapterInterface;

class LaminatorSql extends \Zend\Db\Sql\Sql
{
    public function __construct(AdapterInterface $adapter, $table = null, $sqlPlatform = null)
    {
        parent::__construct($adapter, $table, $sqlPlatform);
    }
}
