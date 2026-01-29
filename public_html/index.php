<?php
require_once __DIR__ . '/config/config.php';

use Core\Request;
use Core\Router;
use Core\ErrorHandler;
use Middleware\JsonMiddleware;
use Middleware\AuthMiddleware;
use Controllers\AuthController;
use Controllers\MeController;
use Controllers\CompaniesController;
use Controllers\WarehousesController;
use Controllers\ProductsController;
use Controllers\IncomeController;
use Controllers\OrdersController;
use Controllers\ServicesController;

ErrorHandler::register();

$request = new Request();
$router = new Router();
$router->addGlobalMiddleware(new JsonMiddleware());

$router->add('POST', '/api/auth/register', array('Controllers\\AuthController', 'register'));
$router->add('POST', '/api/auth/login', array('Controllers\\AuthController', 'login'));
$router->add('POST', '/api/auth/logout', array('Controllers\\AuthController', 'logout'), array(new AuthMiddleware()));
$router->add('GET', '/api/me', array('Controllers\\MeController', 'show'), array(new AuthMiddleware()));

$router->add('GET', '/api/companies', array('Controllers\\CompaniesController', 'index'), array(new AuthMiddleware()));
$router->add('POST', '/api/companies', array('Controllers\\CompaniesController', 'store'), array(new AuthMiddleware()));
$router->add('PUT', '/api/companies/:id', array('Controllers\\CompaniesController', 'update'), array(new AuthMiddleware()));

$router->add('GET', '/api/companies/:companyId/warehouses', array('Controllers\\WarehousesController', 'index'), array(new AuthMiddleware()));
$router->add('POST', '/api/companies/:companyId/warehouses', array('Controllers\\WarehousesController', 'store'), array(new AuthMiddleware()));
$router->add('PUT', '/api/warehouses/:id', array('Controllers\\WarehousesController', 'update'), array(new AuthMiddleware()));
$router->add('DELETE', '/api/warehouses/:id', array('Controllers\\WarehousesController', 'delete'), array(new AuthMiddleware()));

$router->add('GET', '/api/warehouses/:warehouseId/products', array('Controllers\\ProductsController', 'index'), array(new AuthMiddleware()));
$router->add('POST', '/api/warehouses/:warehouseId/products', array('Controllers\\ProductsController', 'store'), array(new AuthMiddleware()));
$router->add('PUT', '/api/products/:id', array('Controllers\\ProductsController', 'update'), array(new AuthMiddleware()));
$router->add('GET', '/api/products/search', array('Controllers\\ProductsController', 'search'), array(new AuthMiddleware()));

$router->add('GET', '/api/warehouses/:warehouseId/income', array('Controllers\\IncomeController', 'index'), array(new AuthMiddleware()));
$router->add('POST', '/api/warehouses/:warehouseId/income', array('Controllers\\IncomeController', 'store'), array(new AuthMiddleware()));

$router->add('GET', '/api/warehouses/:warehouseId/orders', array('Controllers\\OrdersController', 'index'), array(new AuthMiddleware()));
$router->add('POST', '/api/warehouses/:warehouseId/orders', array('Controllers\\OrdersController', 'store'), array(new AuthMiddleware()));
$router->add('PUT', '/api/orders/:id/status', array('Controllers\\OrdersController', 'updateStatus'), array(new AuthMiddleware()));

$router->add('GET', '/api/companies/:companyId/services', array('Controllers\\ServicesController', 'index'), array(new AuthMiddleware()));
$router->add('POST', '/api/companies/:companyId/services', array('Controllers\\ServicesController', 'store'), array(new AuthMiddleware()));
$router->add('PUT', '/api/services/:id', array('Controllers\\ServicesController', 'update'), array(new AuthMiddleware()));
$router->add('DELETE', '/api/services/:id', array('Controllers\\ServicesController', 'delete'), array(new AuthMiddleware()));

$router->dispatch($request);
