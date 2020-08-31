<?php

namespace Benzine\ORM\TabularData;

use Benzine\ORM\Abstracts\AbstractService;
use Laminas\Db\Sql\Where;

class Table
{
    protected AbstractService $service;
    protected array $data;
    protected string $name;
    protected int $page = 0;
    protected int $perPage = 25;

    protected array $colums = [];
    protected array $rows = [];

    public function __construct(AbstractService $service)
    {
        $this->service = $service;
        $this->setName(get_class($service));
        $this->reload();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Table
    {
        $this->name = $name;

        return $this;
    }

    public function getColumns(): array
    {
        if (count($this->colums) == 0) {
            $instance = $this->service->getNewModelInstance();
            foreach ($instance->getPropertyMeta() as $name => $options) {
                $this->colums[] = $name;
            }
        }

        return $this->colums;
    }

    /**
     * @return TableRow[]
     */
    public function getRows(): array
    {
        if (count($this->rows) == 0) {
            $instance = $this->service->getNewModelInstance();

            $limit = $this->perPage;

            $offset = ($this->page) * $this->perPage;

            $where = (new Where());

            if (method_exists($instance, 'getDeleted')) {
                $where->equalTo('deleted', 'No');
            }

            $resultSet = $this->service->search(
                $where,
                $limit,
                $offset
            );
            foreach ($resultSet as $model) {
                $this->rows[] = new TableRow($model);
            }
        }

        return $this->rows;
    }

    public function reload(): void
    {
        $this->colums = $this->rows = [];
        $this->getColumns();
        $this->getRows();
    }
}
