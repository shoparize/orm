<?php

namespace Benzine\ORM\Entities;

class Table extends AbstractEntity
{
    protected string $tableName;
    protected Column $columns;

    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     *
     * @return Table
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }
}
