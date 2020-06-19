<?php

namespace Benzine\ORM\Entities;

class Table extends Entity
{
    /** @var string */
    protected $tableName;
    /** @var Column */
    protected $columns;

    /**
     * @return mixed
     */
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

    public function addColumn(string $name, array $options): self
    {
        $this->columns[] = $column = (new Column())
            ->setOptions($options)
        ;

        return $this;
    }
}
