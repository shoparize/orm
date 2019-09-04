<?php
namespace Example\BlogApp\Controllers\Base;

use Gone\AppCore\Abstracts\CrudController as AbstractCrudController;
use \Example\BlogApp\Services;

/********************************************************
 *             ___                         __           *
 *            / _ \___ ____  ___ ____ ____/ /           *
 *           / // / _ `/ _ \/ _ `/ -_) __/_/            *
 *          /____/\_,_/_//_/\_, /\__/_/ (_)             *
 *                         /___/                        *
 *                                                      *
 * Anything in this file is prone to being overwritten! *
 *                                                      *
 * This file was programatically generated. To modify   *
 * this classes behaviours, do so in the class that     *
 * extends this, or modify the Zenderator Template!     *
 ********************************************************/
abstract class BaseUsersController extends AbstractCrudController
{
    const RESPONSIBLE_MODEL = 'Users';

    /**
     * @param Services\UsersService $users
     */
    public function __construct(
        Services\UsersService $users
    )
    {
        $this->service = $users;
    }

    /**
     * @returns Services\UsersService
     */
    public function getService() : Services\UsersService
    {
        return parent::getService();
    }
}
