<?php

namespace ⌬\Database\Components;

use Camel\CaseTransformer;
use Camel\Format;
use ⌬\Database\Laminator;

class Entity
{
    protected CaseTransformer $transSnake2Studly;
    protected CaseTransformer $transStudly2Camel;
    protected CaseTransformer $transStudly2Studly;
    protected CaseTransformer $transCamel2Camel;
    protected CaseTransformer $transCamel2Studly;
    protected CaseTransformer $transSnake2Camel;
    protected CaseTransformer $transSnake2Spinal;
    protected CaseTransformer $transCamel2Snake;
    protected CaseTransformer $transField2Property;
    protected CaseTransformer $transCamel2ScreamingSnake;
    private Laminator $Laminator;

    public function __construct()
    {
        $this->transSnake2Studly = new CaseTransformer(new Format\SnakeCase(), new Format\StudlyCaps());
        $this->transStudly2Camel = new CaseTransformer(new Format\StudlyCaps(), new Format\CamelCase());
        $this->transStudly2Studly = new CaseTransformer(new Format\StudlyCaps(), new Format\StudlyCaps());
        $this->transCamel2Camel = new CaseTransformer(new Format\CamelCase(), new Format\CamelCase());
        $this->transCamel2Studly = new CaseTransformer(new Format\CamelCase(), new Format\StudlyCaps());
        $this->transSnake2Camel = new CaseTransformer(new Format\SnakeCase(), new Format\CamelCase());
        $this->transSnake2Spinal = new CaseTransformer(new Format\SnakeCase(), new Format\SpinalCase());
        $this->transCamel2Snake = new CaseTransformer(new Format\CamelCase(), new Format\SnakeCase());
        $this->transCamel2ScreamingSnake = new CaseTransformer(new Format\CamelCase(), new Format\ScreamingSnakeCase());

        $this->transField2Property = $this->transCamel2Camel;
    }

    /**
     * @return self
     */
    public static function Factory(Laminator $Laminator)
    {
        $class = get_called_class();
        /** @var self $instance */
        $instance = new $class();
        $instance->setLaminator($Laminator);

        return $instance;
    }

    protected function getLaminator(): Laminator
    {
        return $this->Laminator;
    }

    protected function setLaminator(Laminator $Laminator): Entity
    {
        $this->Laminator = $Laminator;

        return $this;
    }
}
