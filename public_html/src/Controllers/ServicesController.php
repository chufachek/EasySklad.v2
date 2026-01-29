<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Models\CompanyModel;
use Models\ServiceModel;

class ServicesController
{
    public static function index($request)
    {
        $user = $request->getUser();
        $companyId = $request->getParam('companyId');
        $company = CompanyModel::findByIdForUser($companyId, $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $services = ServiceModel::forCompany($companyId);
        Response::success($services);
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
        if (!Validator::required(isset($data['price']) ? $data['price'] : null) || !Validator::nonNegativeNumber($data['price'])) {
            $errors['price'] = 'Price must be >= 0';
        }
        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }
        $serviceId = ServiceModel::create($companyId, $data['name'], $data['price'], isset($data['description']) ? $data['description'] : null);
        Response::success(array('id' => $serviceId, 'company_id' => $companyId, 'name' => $data['name']), 201);
    }

    public static function update($request)
    {
        $user = $request->getUser();
        $serviceId = $request->getParam('id');
        $service = ServiceModel::findById($serviceId);
        if (!$service) {
            Response::error('NOT_FOUND', 'Service not found', 404);
        }
        $company = CompanyModel::findByIdForUser($service['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $data = $request->getBody();
        $errors = array();
        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }
        if (!Validator::required(isset($data['price']) ? $data['price'] : null) || !Validator::nonNegativeNumber($data['price'])) {
            $errors['price'] = 'Price must be >= 0';
        }
        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }
        ServiceModel::update($serviceId, $data['name'], $data['price'], isset($data['description']) ? $data['description'] : null);
        $service = ServiceModel::findById($serviceId);
        Response::success($service);
    }

    public static function delete($request)
    {
        $user = $request->getUser();
        $serviceId = $request->getParam('id');
        $service = ServiceModel::findById($serviceId);
        if (!$service) {
            Response::error('NOT_FOUND', 'Service not found', 404);
        }
        $company = CompanyModel::findByIdForUser($service['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        ServiceModel::delete($serviceId);
        Response::success(array('deleted' => true));
    }
}
