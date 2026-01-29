<?php

use Controllers\AuthController;
use Controllers\MeController;
use Controllers\CompaniesController;
use Controllers\WarehousesController;
use Controllers\ProductsController;
use Controllers\IncomeController;
use Controllers\OrdersController;
use Controllers\ServicesController;
use Controllers\CategoriesController;
use Controllers\DashboardController;
use Middleware\AuthMiddleware;
use Middleware\JsonMiddleware;

$applyJson = function () use ($request) {
    $middleware = new JsonMiddleware();
    $middleware->handle($request, function () {
    });
};

$applyAuth = function () use ($request) {
    $middleware = new AuthMiddleware();
    $middleware->handle($request, function () {
    });
};

$dispatch = function ($handler, $params = array()) use ($request) {
    $request->setParams($params);
    call_user_func($handler, $request);
};

$router->before('GET|POST|PUT|DELETE|OPTIONS', '/api/.*', function () use ($applyJson) {
    $applyJson();
});

$router->post('/api/auth/register', function () use ($dispatch) {
    $dispatch(array(AuthController::class, 'register'));
});

$router->post('/api/auth/login', function () use ($dispatch) {
    $dispatch(array(AuthController::class, 'login'));
});

$router->post('/api/auth/logout', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(AuthController::class, 'logout'));
});

$router->get('/api/me', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(MeController::class, 'show'));
});

$router->put('/api/me', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(MeController::class, 'update'));
});

$router->get('/api/companies', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CompaniesController::class, 'index'));
});

$router->post('/api/companies', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CompaniesController::class, 'store'));
});

$router->put('/api/companies/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CompaniesController::class, 'update'), array('id' => $id));
});

$router->get('/api/companies/(\d+)/warehouses', function ($companyId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(WarehousesController::class, 'index'), array('companyId' => $companyId));
});

$router->post('/api/companies/(\d+)/warehouses', function ($companyId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(WarehousesController::class, 'store'), array('companyId' => $companyId));
});

$router->put('/api/warehouses/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(WarehousesController::class, 'update'), array('id' => $id));
});

$router->delete('/api/warehouses/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(WarehousesController::class, 'delete'), array('id' => $id));
});

$router->get('/api/warehouses/(\d+)/products', function ($warehouseId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ProductsController::class, 'index'), array('warehouseId' => $warehouseId));
});

$router->post('/api/warehouses/(\d+)/products', function ($warehouseId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ProductsController::class, 'store'), array('warehouseId' => $warehouseId));
});

$router->put('/api/products/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ProductsController::class, 'update'), array('id' => $id));
});

$router->get('/api/products/search', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ProductsController::class, 'search'));
});

$router->get('/api/warehouses/(\d+)/income', function ($warehouseId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(IncomeController::class, 'index'), array('warehouseId' => $warehouseId));
});

$router->post('/api/warehouses/(\d+)/income', function ($warehouseId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(IncomeController::class, 'store'), array('warehouseId' => $warehouseId));
});

$router->get('/api/warehouses/(\d+)/orders', function ($warehouseId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(OrdersController::class, 'index'), array('warehouseId' => $warehouseId));
});

$router->post('/api/warehouses/(\d+)/orders', function ($warehouseId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(OrdersController::class, 'store'), array('warehouseId' => $warehouseId));
});

$router->put('/api/orders/(\d+)/status', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(OrdersController::class, 'updateStatus'), array('id' => $id));
});

$router->get('/api/companies/(\d+)/services', function ($companyId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ServicesController::class, 'index'), array('companyId' => $companyId));
});

$router->post('/api/companies/(\d+)/services', function ($companyId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ServicesController::class, 'store'), array('companyId' => $companyId));
});

$router->put('/api/services/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ServicesController::class, 'update'), array('id' => $id));
});

$router->delete('/api/services/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(ServicesController::class, 'delete'), array('id' => $id));
});

$router->get('/api/companies/(\d+)/categories', function ($companyId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CategoriesController::class, 'index'), array('companyId' => $companyId));
});

$router->post('/api/companies/(\d+)/categories', function ($companyId) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CategoriesController::class, 'store'), array('companyId' => $companyId));
});

$router->put('/api/categories/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CategoriesController::class, 'update'), array('id' => $id));
});

$router->delete('/api/categories/(\d+)', function ($id) use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(CategoriesController::class, 'delete'), array('id' => $id));
});

$router->get('/api/dashboard', function () use ($applyAuth, $dispatch) {
    $applyAuth();
    $dispatch(array(DashboardController::class, 'show'));
});
