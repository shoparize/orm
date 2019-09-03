<?php
$exampleExistingObjectFindFunction = function()
{
    $DIContainer = \⌬\Config\⌬\⌬::Instance()->getContainer();
    $tableGateway = $DIContainer->get(\Example\BlogApp\TableGateways\UsersTableGateway::class);

    /** @var \Example\BlogApp\TableGateways\UsersTableGateway $exampleExistingObjectTableGateway */
    $exampleExistingObjectTableGateway = $DIContainer->get(\Example\BlogApp\TableGateways\UsersTableGateway::class);

    /** @var \Example\BlogApp\Models\UsersModel $exampleExistingObject */
    $exampleExistingObject = $exampleExistingObjectTableGateway->getNewMockModelInstance();
    if(method_exists($exampleExistingObject, 'setId')){
        $exampleExistingObject->setId(rand(1000000,9999999));
    }

    return $exampleExistingObject;
};

// Router proper begins
$router = \Gone\AppCore\Router\Router::Instance()
    ->addRoute(
        \Gone\AppCore\Router\Route::Factory()
            ->setName('Users List')
            ->setCallback(\Example\BlogApp\Controllers\UsersController::class . ':listRequest')
            ->setSDKClass('Users')
            ->setSDKFunction('list')
            ->setSDKTemplate('list')
            ->setRouterPattern('/v1/users')
            ->setHttpEndpoint( '/v1/users')
            ->setHttpMethod('GET')
            ->setSingular('Users')
            ->setPlural('Users')
            ->setProperties([
                'Id',
                'DisplayName',
                'UserName',
                'Email',
                'Password',
            ])
            ->setPropertyOptions([
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \Gone\AppCore\Router\Route::Factory()
            ->setName('Users Create')
            ->setCallback(\Example\BlogApp\Controllers\UsersController::class . ':createRequest')
            ->setSDKClass('Users')
            ->setSDKFunction('create')
            ->setSDKTemplate('create')
            ->setRouterPattern('/v1/users')
            ->setHttpEndpoint( '/v1/users')
            ->setHttpMethod('PUT')
            ->setSingular('Users')
            ->setPlural('Users')
            ->setProperties([
                'Id',
                'DisplayName',
                'UserName',
                'Email',
                'Password',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \Gone\AppCore\Router\Route::Factory()
            ->setName('Users Get')
            ->setCallback(\Example\BlogApp\Controllers\UsersController::class . ':getRequest')
            ->setSDKClass('Users')
            ->setSDKFunction('get')
            ->setSDKTemplate('get')
            ->setRouterPattern('/v1/users/{id}')
            ->setHttpEndpoint( '/v1/users/id')
            ->setHttpMethod('GET')
            ->setSingular('Users')
            ->setPlural('Users')
            ->setProperties([
                'Id',
                'DisplayName',
                'UserName',
                'Email',
                'Password',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
            ->setExampleEntityFindFunction($exampleExistingObjectFindFunction)
    )
    ->addRoute(
        \Gone\AppCore\Router\Route::Factory()
            ->setName('Users Delete')
            ->setCallback(\Example\BlogApp\Controllers\UsersController::class . ':deleteRequest')
            ->setSDKClass('Users')
            ->setSDKFunction('delete')
            ->setSDKTemplate('delete')
            ->setRouterPattern('/v1/users/{id}')
            ->setHttpEndpoint( '/v1/users/id')
            ->setHttpMethod('DELETE')
            ->setSingular('Users')
            ->setPlural('Users')
            ->setProperties([
                'Id',
                'DisplayName',
                'UserName',
                'Email',
                'Password',
            ])
            ->setAccess(DEFAULT_ROUTE_ACCESS_MODE)
    );


