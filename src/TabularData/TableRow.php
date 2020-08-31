<?php

namespace Benzine\ORM\TabularData;

use Benzine\ORM\Abstracts\AbstractModel;
use Benzine\ORM\Abstracts\AbstractService;

class TableRow
{
    private array $data = [];
    /** @var AbstractModel[] */
    private array $related;

    public function __construct(AbstractModel $model)
    {
        foreach ($model->getPropertyMeta() as $field => $options) {
            $this->data[$field] = $model->__get($field);

            if (isset($options['service'])) {
                /** @var AbstractService $service */
                $service = $options['service'];
                /** @var AbstractModel $relatedEntity */
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
