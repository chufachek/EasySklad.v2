<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Models\CompanyModel;
use Models\WarehouseModel;

class WarehousesController
{
    public static function index($request)
    {
        $user = $request->getUser();
        $companyId = $request->getParam('companyId');
        $company = CompanyModel::findByIdForUser($companyId, $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $warehouses = WarehouseModel::forCompany($companyId);
        Response::success($warehouses);
    }

    public static function store($request)
    {
        $user = $request->getUser();
        $companyId = $request->getParam('companyId');
        $company = CompanyModel::findByIdForUser($companyId, $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $data = $request->getBody();
        $errors = array();
        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }
        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }
        $warehouseId = WarehouseModel::create($companyId, $data['name'], isset($data['address']) ? $data['address'] : null);
        Response::success(array('id' => $warehouseId, 'company_id' => $companyId, 'name' => $data['name'], 'address' => isset($data['address']) ? $data['address'] : null), 201);
    }

    public static function update($request)
    {
        $user = $request->getUser();
        $warehouseId = $request->getParam('id');
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
        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }
        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }
        WarehouseModel::update($warehouseId, $data['name'], isset($data['address']) ? $data['address'] : null);
        $warehouse = WarehouseModel::findById($warehouseId);
        Response::success($warehouse);
    }

    public static function delete($request)
    {
        $user = $request->getUser();
        $warehouseId = $request->getParam('id');
        $warehouse = WarehouseModel::findById($warehouseId);
        if (!$warehouse) {
            Response::error('NOT_FOUND', 'Warehouse not found', 404);
        }
        $company = CompanyModel::findByIdForUser($warehouse['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        WarehouseModel::delete($warehouseId);
        Response::success(array('deleted' => true));
    }
}
