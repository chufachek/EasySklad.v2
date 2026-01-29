<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Core\Helpers;
use Models\CompanyModel;
use Models\WarehouseModel;
use Models\ProductModel;

class ProductsController
{
    public static function index($request)
    {
        $user = $request->getUser();
        $warehouseId = $request->getParam('warehouseId');
        $warehouse = WarehouseModel::findById($warehouseId);
        if (!$warehouse) {
            Response::error('NOT_FOUND', 'Warehouse not found', 404);
        }
        $company = CompanyModel::findByIdForUser($warehouse['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        list($page, $limit, $offset) = Helpers::paginate(isset($request->getQuery()['page']) ? $request->getQuery()['page'] : null, isset($request->getQuery()['limit']) ? $request->getQuery()['limit'] : null);
        $search = isset($request->getQuery()['search']) ? $request->getQuery()['search'] : null;
        $products = ProductModel::forWarehouse($warehouseId, $search, $limit, $offset);
        Response::success($products, 200, array('page' => $page, 'limit' => $limit));
    }

    public static function search($request)
    {
        $user = $request->getUser();
        $query = isset($request->getQuery()['q']) ? $request->getQuery()['q'] : null;
        $warehouseId = isset($request->getQuery()['warehouseId']) ? $request->getQuery()['warehouseId'] : null;
        if (!$warehouseId) {
            Response::error('VALIDATION_ERROR', 'warehouseId is required', 400, array('warehouseId' => 'warehouseId is required'));
        }
        $warehouse = WarehouseModel::findById($warehouseId);
        if (!$warehouse) {
            Response::error('NOT_FOUND', 'Warehouse not found', 404);
        }
        $company = CompanyModel::findByIdForUser($warehouse['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $products = ProductModel::search($warehouseId, $query ? $query : '');
        Response::success($products);
    }

    public static function store($request)
    {
        $user = $request->getUser();
        $warehouseId = $request->getParam('warehouseId');
        $warehouse = WarehouseModel::findById($warehouseId);
        if (!$warehouse) {
            Response::error('NOT_FOUND', 'Warehouse not found', 404);
        }
        $company = CompanyModel::findByIdForUser($warehouse['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $data = $request->getBody();
        $errors = array();
        if (!Validator::required(isset($data['sku']) ? $data['sku'] : null)) {
            $errors['sku'] = 'SKU is required';
        }
        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }
        if (!Validator::required(isset($data['price']) ? $data['price'] : null) || !Validator::nonNegativeNumber($data['price'])) {
            $errors['price'] = 'Price must be >= 0';
        }
        if (isset($data['cost']) && !Validator::nonNegativeNumber($data['cost'])) {
            $errors['cost'] = 'Cost must be >= 0';
        }
        if (isset($data['min_stock']) && !Validator::nonNegativeNumber($data['min_stock'])) {
            $errors['min_stock'] = 'Min stock must be >= 0';
        }
        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }
        if (ProductModel::findBySku($warehouseId, $data['sku'])) {
            Response::error('CONFLICT', 'SKU already exists', 409);
        }
        $productId = ProductModel::create(
            $warehouseId,
            $data['sku'],
            $data['name'],
            $data['price'],
            isset($data['cost']) ? $data['cost'] : null,
            isset($data['unit']) ? $data['unit'] : null,
            isset($data['min_stock']) ? $data['min_stock'] : 0
        );
        Response::success(array('id' => $productId, 'warehouse_id' => $warehouseId, 'sku' => $data['sku'], 'name' => $data['name']), 201);
    }

    public static function update($request)
    {
        $user = $request->getUser();
        $productId = $request->getParam('id');
        $product = ProductModel::findById($productId);
        if (!$product) {
            Response::error('NOT_FOUND', 'Product not found', 404);
        }
        $warehouse = WarehouseModel::findById($product['warehouse_id']);
        if (!$warehouse) {
            Response::error('NOT_FOUND', 'Warehouse not found', 404);
        }
        $company = CompanyModel::findByIdForUser($warehouse['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $data = $request->getBody();
        $errors = array();
        if (!Validator::required(isset($data['sku']) ? $data['sku'] : null)) {
            $errors['sku'] = 'SKU is required';
        }
        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }
        if (!Validator::required(isset($data['price']) ? $data['price'] : null) || !Validator::nonNegativeNumber($data['price'])) {
            $errors['price'] = 'Price must be >= 0';
        }
        if (isset($data['cost']) && !Validator::nonNegativeNumber($data['cost'])) {
            $errors['cost'] = 'Cost must be >= 0';
        }
        if (isset($data['min_stock']) && !Validator::nonNegativeNumber($data['min_stock'])) {
            $errors['min_stock'] = 'Min stock must be >= 0';
        }
        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }
        $existing = ProductModel::findBySku($warehouse['id'], $data['sku']);
        if ($existing && intval($existing['id']) !== intval($productId)) {
            Response::error('CONFLICT', 'SKU already exists', 409);
        }
        ProductModel::update(
            $productId,
            $data['sku'],
            $data['name'],
            $data['price'],
            isset($data['cost']) ? $data['cost'] : null,
            isset($data['unit']) ? $data['unit'] : null,
            isset($data['min_stock']) ? $data['min_stock'] : 0
        );
        $product = ProductModel::findById($productId);
        Response::success($product);
    }
}
