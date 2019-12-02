<?php
namespace Example\BlogApp\Controllers\Base;

use ⌬\Controllers\Abstracts\CrudController as AbstractCrudController;
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
abstract class BasePostsController extends AbstractCrudController
{
    const RESPONSIBLE_MODEL = 'Posts';

    /**
     * @param Services\PostsService $posts
     */
    public function __construct(
        Services\PostsService $posts
    )
    {
        $this->service = $posts;
    }

    /**
     * @returns Services\PostsService
     */
    public function getService() : Services\PostsService
    {
        return parent::getService();
    }
}
