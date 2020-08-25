<?php

namespace Benzine\ORM\TabularData;

use Benzine\ORM\Abstracts\Model;
use Benzine\ORM\Abstracts\Service;

class TableRow
{
    private array $data = [];
    /** @var Model[] */
    private array $related;

    public function __construct(Model $model)
    {
        foreach ($model->getPropertyMeta() as $field => $options) {
            $this->data[$field] = $model->__get($field);

            if (isset($options['service'])) {
                /** @var Service $service */
                $service = $options['service'];
                /** @var Model $relatedEntity */
                $relatedEntity = $service->getByField($field, $this->data[$field]);
                $this->related[$field] = $relatedEntity;
            }
        }
    }

    public function getData(): array
    {
        $output = [];
        foreach ($this->data as $field => $value) {
            //!\Kint::dump($field, $this->related[$field]);
            $output[$field] = isset($this->related[$field]) ? $this->related[$field]->label() : $value;
        }
        //!\Kint::dump($output);
        //exit;

        return $output;
    }
}
