<?php
$exampleExistingObjectFindFunction = function()
{
    $DIContainer = \⌬\Config\⌬\⌬::Instance()->getContainer();
    $tableGateway = $DIContainer->get(\Example\BlogApp\TableGateways\CommentsTableGateway::class);

    /** @var \Example\BlogApp\TableGateways\CommentsTableGateway $exampleExistingObjectTableGateway */
    $exampleExistingObjectTableGateway = $DIContainer->get(\Example\BlogApp\TableGateways\CommentsTableGateway::class);

    /** @var \Example\BlogApp\Models\CommentsModel $exampleExistingObject */
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
            ->setName('Comments List')
            ->setCallback(\Example\BlogApp\Controllers\CommentsController::class . ':listRequest')
            ->setSDKClass('Comments')
            ->setSDKFunction('list')
            ->setSDKTemplate('list')
            ->setRouterPattern('/v1/comments')
            ->setHttpEndpoint( '/v1/comments')
            ->setHttpMethod('GET')
            ->setSingular('Comments')
            ->setPlural('Comments')
            ->setProperties([
                'Id',
                'Comment',
                'AuthorId',
                'PublishedDate',
            ])
            ->setPropertyOptions([
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Comments Create')
            ->setCallback(\Example\BlogApp\Controllers\CommentsController::class . ':createRequest')
            ->setSDKClass('Comments')
            ->setSDKFunction('create')
            ->setSDKTemplate('create')
            ->setRouterPattern('/v1/comments')
            ->setHttpEndpoint( '/v1/comments')
            ->setHttpMethod('PUT')
            ->setSingular('Comments')
            ->setPlural('Comments')
            ->setProperties([
                'Id',
                'Comment',
                'AuthorId',
                'PublishedDate',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Comments Get')
            ->setCallback(\Example\BlogApp\Controllers\CommentsController::class . ':getRequest')
            ->setSDKClass('Comments')
            ->setSDKFunction('get')
            ->setSDKTemplate('get')
            ->setRouterPattern('/v1/comments/{id}')
            ->setHttpEndpoint( '/v1/comments/id')
            ->setHttpMethod('GET')
            ->setSingular('Comments')
            ->setPlural('Comments')
            ->setProperties([
                'Id',
                'Comment',
                'AuthorId',
                'PublishedDate',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \⌬\Router\Route::Factory()
            ->setName('Comments Delete')
            ->setCallback(\Example\BlogApp\Controllers\CommentsController::class . ':deleteRequest')
            ->setSDKClass('Comments')
            ->setSDKFunction('delete')
            ->setSDKTemplate('delete')
            ->setRouterPattern('/v1/comments/{id}')
            ->setHttpEndpoint( '/v1/comments/id')
            ->setHttpMethod('DELETE')
            ->setSingular('Comments')
            ->setPlural('Comments')
            ->setProperties([
                'Id',
                'Comment',
                'AuthorId',
                'PublishedDate',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
    );


