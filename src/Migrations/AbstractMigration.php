<?php

namespace Benzine\ORM\Migrations;

abstract class AbstractMigration extends \Phinx\Migration\AbstractMigration
{
    protected array $defaultRelationshipOptions = [
        'delete' => 'NO_ACTION',
        'update' => 'NO_ACTION',
    ];

    protected array $enumYesNoOptions = [
        'values' => ['Yes', 'No'],
        'default' => 'No',
    ];
}
