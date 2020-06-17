<?php

namespace Benzine\ORM\Migrations;

use Phinx\Migration\AbstractMigration;

abstract class Migration extends AbstractMigration
{

    protected array $defaultRelationshipOptions = [
        'delete'=> 'NO_ACTION',
        'update'=> 'NO_ACTION',
    ];

    protected array $enumYesNoOptions = [
        'values' => ['Yes', 'No'],
        'default' => 'No',
    ];

}