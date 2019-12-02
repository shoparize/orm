<?php
$exampleExistingObjectFindFunction = function()
{
    $DIContainer = \⌬\Config\⌬\⌬::Instance()->getContainer();
    $tableGateway = $DIContainer->get(\Example\BlogApp\TableGateways\PostsTableGateway::class);

    /** @var \Example\BlogApp\TableGateways\PostsTableGateway $exampleExistingObjectTableGateway */
    $exampleExistingObjectTableGateway = $DIContainer->get(\Example\BlogApp\TableGateways\PostsTableGateway::class);

    /** @var \Example\BlogApp\Models\PostsModel $exampleExistingObject */
    $exampleExistingObject = $exampleExistingObjectTableGateway->getNewMockModelInstance();
    if(method_exists($exampleExistingObject, 'setId')){
        $exampleExistingObject->setId(rand(1000000,9999999));
    }

    return $exampleExistingObject;
};

// Router proper begins
$router = \⌬\Router\Router::Instance()
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Posts List')
            ->setCallback(\Example\BlogApp\Controllers\PostsController::class . ':listRequest')
            ->setSDKClass('Posts')
            ->setSDKFunction('list')
            ->setSDKTemplate('list')
            ->setRouterPattern('/v1/posts')
            ->setHttpEndpoint( '/v1/posts')
            ->setHttpMethod('GET')
            ->setSingular('Posts')
            ->setPlural('Posts')
            ->setProperties([
                'Id',
                'Title',
                'Content',
                'AuthorId',
                'CreatedDate',
                'PublishedDate',
                'Deleted',
            ])
            ->setPropertyOptions([
                'Deleted' => [
                    "Yes",
                    "No",
                ],
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Posts Create')
            ->setCallback(\Example\BlogApp\Controllers\PostsController::class . ':createRequest')
            ->setSDKClass('Posts')
            ->setSDKFunction('create')
            ->setSDKTemplate('create')
            ->setRouterPattern('/v1/posts')
            ->setHttpEndpoint( '/v1/posts')
            ->setHttpMethod('PUT')
            ->setSingular('Posts')
            ->setPlural('Posts')
            ->setProperties([
                'Id',
                'Title',
                'Content',
                'AuthorId',
                'CreatedDate',
                'PublishedDate',
                'Deleted',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Posts Get')
            ->setCallback(\Example\BlogApp\Controllers\PostsController::class . ':getRequest')
            ->setSDKClass('Posts')
            ->setSDKFunction('get')
            ->setSDKTemplate('get')
            ->setRouterPattern('/v1/posts/{id}')
            ->setHttpEndpoint( '/v1/posts/id')
            ->setHttpMethod('GET')
            ->setSingular('Posts')
            ->setPlural('Posts')
            ->setProperties([
                'Id',
                'Title',
                'Content',
                'AuthorId',
                'CreatedDate',
                'PublishedDate',
                'Deleted',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Posts Delete')
            ->setCallback(\Example\BlogApp\Controllers\PostsController::class . ':deleteRequest')
            ->setSDKClass('Posts')
            ->setSDKFunction('delete')
            ->setSDKTemplate('delete')
            ->setRouterPattern('/v1/posts/{id}')
            ->setHttpEndpoint( '/v1/posts/id')
            ->setHttpMethod('DELETE')
            ->setSingular('Posts')
            ->setPlural('Posts')
            ->setProperties([
                'Id',
                'Title',
                'Content',
                'AuthorId',
                'CreatedDate',
                'PublishedDate',
                'Deleted',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
    );


