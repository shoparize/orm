<?php

namespace Benzine\ORM\Components;

use Benzine\ORM\Connection\Database;
use Benzine\ORM\Laminator;
use Gone\Inflection\Inflect;

class RelatedModel extends Entity
{
    protected string $schema;
    protected string $localTable;
    protected string $remoteTable;
    protected string $localBoundSchema;
    protected string $localBoundColumn;
    protected string $remoteBoundSchema;
    protected string $remoteBoundColumn;
    protected bool $hasClassConflict = false;
    protected Model $localRelatedModel;
    protected Model $remoteRelatedModel;
    protected Database $database;

    protected Model $relatedModel;

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function setDatabase(Database $database): RelatedModel
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return self
     */
    public static function Factory(Laminator $Laminator)
    {
        return parent::Factory($Laminator);
    }

    public function markClassConflict(bool $conflict)
    {
        //echo "  > Marked {$this->getLocalClass()}/{$this->getRemoteClass()} in conflict.\n";
        $this->hasClassConflict = $conflict;

        return $this;
    }

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function setSchema(string $schema): RelatedModel
    {
        $this->schema = $schema;

        return $this;
    }

    public function getRemoteVariable(): string
    {
        return $this->transStudly2Camel->transform(
            $this->getRemoteClassPrefix().
            $this->transCamel2Studly->transform($this->getRemoteTableSanitised())
        );
    }

    public function getRemoteBoundSchema(): string
    {
        return $this->remoteBoundSchema;
    }

    public function setRemoteBoundSchema(string $remoteBoundSchema): RelatedModel
    {
        $this->remoteBoundSchema = $remoteBoundSchema;

        return $this;
    }

    public function getRemoteTableSanitised(): string
    {
        return $this->getLaminator()->sanitiseTableName($this->getRemoteTable(), $this->getDatabase());
    }

    public function getRemoteTable(): string
    {
        return $this->remoteTable;
    }

    public function setRemoteTable(string $remoteTable): RelatedModel
    {
        $this->remoteTable = $remoteTable;

        return $this;
    }

    public function getLocalVariable(): string
    {
        return  $this->transStudly2Camel->transform(
            $this->getLocalClassPrefix().
            $this->transCamel2Studly->transform($this->getLocalTableSanitised())
        );
    }

    public function getLocalBoundSchema(): string
    {
        return $this->localBoundSchema;
    }

    public function setLocalBoundSchema(string $localBoundSchema): RelatedModel
    {
        $this->localBoundSchema = $localBoundSchema;

        return $this;
    }

    public function getLocalTableSanitised(): string
    {
        return $this->getLaminator()->sanitiseTableName($this->getLocalTable(), $this->getDatabase());
    }

    public function getLocalBoundColumnAsConstant(): string
    {
        return 'FIELD_'.str_replace('_', '', $this->transCamel2ScreamingSnake->transform($this->getLocalBoundColumn()));
    }

    public function getLocalTable(): string
    {
        return $this->localTable;
    }

    public function setLocalTable(string $localTable): RelatedModel
    {
        $this->localTable = $localTable;

        return $this;
    }

    public function getLocalTableGatewayName(): string
    {
        return $this->transCamel2Studly->transform(
            $this->getLocalClass()
            .'TableGateway'
        );
    }

    public function getRemoteTableGatewayName(): string
    {
        return $this->transCamel2Studly->transform(
            $this->getRemoteClass()
            .'TableGateway'
        );
    }

    public function getLocalModelName(): string
    {
        return $this->transCamel2Studly->transform(
            $this->getLocalClass()
            .'Model'
        );
    }

    public function getRemoteModelName(): string
    {
        return $this->transCamel2Studly->transform(
            $this->getRemoteClass()
            .'Model'
        );
    }

    public function getLocalFunctionName(): string
    {
        if ($this->hasClassConflict()) {
            return
                self::singulariseCamelCaseSentence($this->getLocalClass()).
                'By'.
                $this->transCamel2Studly->transform($this->getLocalBoundColumn())
            ;
        }

        return $this->transCamel2Studly->transform(
            $this->getLocalClass()
        );
    }

    public function getRemoteFunctionName(): string
    {
        if ($this->hasClassConflict()) {
            return
                self::singulariseCamelCaseSentence($this->getRemoteClass()).
                'By'.
                $this->transCamel2Studly->transform($this->getLocalBoundColumn());
        }

        return
                self::singulariseCamelCaseSentence(
                    $this->getRemoteClass()
                );
    }

    public function hasClassConflict(): bool
    {
        return $this->hasClassConflict;
    }

    public function getLocalClass(): string
    {
        return $this->getLocalClassPrefix().
                $this->transCamel2Studly->transform($this->getLocalTableSanitised());
    }

    public function getLocalBoundColumn(): string
    {
        return $this->localBoundColumn;
    }

    public function getLocalBoundColumnSanitised(): string
    {
        return $this->getLaminator()->sanitiseTableName($this->getLocalBoundColumn(), $this->getDatabase());
    }

    public function setLocalBoundColumn(string $localBoundColumn): RelatedModel
    {
        $this->localBoundColumn = $localBoundColumn;

        return $this;
    }

    public function getRemoteClass(): string
    {
        return $this->getRemoteClassPrefix().
            $this->transCamel2Studly->transform($this->getRemoteTableSanitised());
    }

    public function getLocalBoundColumnGetter(): string
    {
        return 'get'.$this->transCamel2Studly->transform($this->getLocalBoundColumn());
    }

    public function getRemoteBoundColumnGetter(): string
    {
        return 'get'.$this->transCamel2Studly->transform($this->getRemoteBoundColumn());
    }

    public function getLocalBoundColumnSetter(): string
    {
        return 'set'.$this->transCamel2Studly->transform($this->getLocalBoundColumn());
    }

    public function getRemoteBoundColumnSetter(): string
    {
        return 'set'.$this->transCamel2Studly->transform($this->getRemoteBoundColumn());
    }

    public function getRemoteBoundColumn(): string
    {
        return $this->remoteBoundColumn;
    }

    public function getRemoteBoundColumnSanitised(): string
    {
        return $this->getLaminator()->sanitiseTableName($this->getRemoteBoundColumn(), $this->getDatabase());
    }

    public function getRemoteBoundColumnAsConstant(): string
    {
        return 'FIELD_'.str_replace('_', '', $this->transCamel2ScreamingSnake->transform($this->getRemoteBoundColumn()));
    }

    public function setRemoteBoundColumn(string $remoteBoundColumn): RelatedModel
    {
        $this->remoteBoundColumn = $remoteBoundColumn;

        return $this;
    }

    public function setBindings(
        string $localSchema,
        string $localColumn,
        string $remoteSchema,
        string $remoteColumn
    ): RelatedModel {
        return $this
            ->setLocalBoundSchema($localSchema)
            ->setLocalBoundColumn($localColumn)
            ->setRemoteBoundSchema($remoteSchema)
            ->setRemoteBoundColumn($remoteColumn)
        ;
    }

    public function getLocalClassPrefix(): ?string
    {
        return $this->getLocalRelatedModel()->getClassPrefix();
    }

    public function getRemoteClassPrefix(): ?string
    {
        return $this->getRemoteRelatedModel()->getClassPrefix();
    }

    /**
     * Alias of getRemoteClassPrefix.
     */
    public function getRelatedClassPrefix(): ?string
    {
        return $this->getRemoteClassPrefix();
    }

    public function getLocalRelatedModel(): Model
    {
        return $this->relatedModel;
    }

    public function setLocalRelatedModel(Model $localRelatedModel): RelatedModel
    {
        $this->relatedModel = $localRelatedModel;

        return $this;
    }

    public function getRemoteRelatedModel(): Model
    {
        return $this->remoteRelatedModel;
    }

    public function setRemoteRelatedModel(Model $remoteRelatedModel): RelatedModel
    {
        $this->remoteRelatedModel = $remoteRelatedModel;

        return $this;
    }

    public function hasField(string $fieldName): bool
    {
        return $this->relatedModel->hasColumn($fieldName);
    }

    /**
     * Singularise the very last word of a camelcase sentence: bigSmellyHorses => bigSmellyHorse.
     */
    private function singulariseCamelCaseSentence(string $camel): string
    {
        $snake = explode('_', $this->transCamel2Snake->transform($camel));
        $snake[count($snake) - 1] = Inflect::singularize($snake[count($snake) - 1]);

        return $this->transSnake2Camel->transform(implode('_', $snake));
    }
}
