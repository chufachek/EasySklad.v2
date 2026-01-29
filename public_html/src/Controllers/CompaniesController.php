<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Models\CompanyModel;

class CompaniesController
{
    public static function index($request)
    {
        $user = $request->getUser();
        $companies = CompanyModel::forUser($user['id']);
        Response::success($companies);
    }

    public static function store($request)
    {
        $user = $request->getUser();
        $data = $request->getBody();
        $errors = array();

        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }

        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }

        $count = CompanyModel::countForUser($user['id']);
        if ($count >= config('app.max_companies_per_owner')) {
            Response::error('LIMIT_REACHED', 'Company limit reached', 409);
        }

        $companyId = CompanyModel::create($data['name'], isset($data['inn']) ? $data['inn'] : null, isset($data['address']) ? $data['address'] : null);
        CompanyModel::attachUser($companyId, $user['id'], 'owner');

        Response::success(array('id' => $companyId, 'name' => $data['name'], 'inn' => isset($data['inn']) ? $data['inn'] : null, 'address' => isset($data['address']) ? $data['address'] : null), 201);
    }

    public static function update($request)
    {
        $user = $request->getUser();
        $companyId = $request->getParam('id');
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
        CompanyModel::update($companyId, $data['name'], isset($data['inn']) ? $data['inn'] : null, isset($data['address']) ? $data['address'] : null);
        $company = CompanyModel::findByIdForUser($companyId, $user['id']);
        Response::success($company);
    }
}
