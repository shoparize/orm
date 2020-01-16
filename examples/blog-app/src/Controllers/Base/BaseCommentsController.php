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
 * extends this, or modify the Laminator Template!     *
 ********************************************************/
abstract class BaseCommentsController extends AbstractCrudController
{
    const RESPONSIBLE_MODEL = 'Comments';

    /**
     * @param Services\CommentsService $comments
     */
    public function __construct(
        Services\CommentsService $comments
    )
    {
        $this->service = $comments;
    }

    /**
     * @returns Services\CommentsService
     */
    public function getService() : Services\CommentsService
    {
        return parent::getService();
    }
}
