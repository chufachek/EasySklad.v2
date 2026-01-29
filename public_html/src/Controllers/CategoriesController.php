<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Models\CategoryModel;
use Models\CompanyModel;

class CategoriesController
{
    public static function index($request)
    {
        $user = $request->getUser();
        $companyId = $request->getParam('companyId');
        $company = CompanyModel::findByIdForUser($companyId, $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $categories = CategoryModel::forCompany($companyId);
        Response::success($categories);
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
        $name = isset($data['name']) ? trim($data['name']) : '';
        if (!Validator::required($name)) {
            Response::error('VALIDATION_ERROR', 'Name is required', 400, array('name' => 'Name is required'));
        }
        if (CategoryModel::findByName($companyId, $name)) {
            Response::error('CONFLICT', 'Category already exists', 409);
        }
        $id = CategoryModel::create($companyId, $name);
        Response::success(array('id' => $id, 'name' => $name), 201);
    }

    public static function update($request)
    {
        $user = $request->getUser();
        $categoryId = $request->getParam('id');
        $category = CategoryModel::findById($categoryId);
        if (!$category) {
            Response::error('NOT_FOUND', 'Category not found', 404);
        }
        $company = CompanyModel::findByIdForUser($category['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $data = $request->getBody();
        $name = isset($data['name']) ? trim($data['name']) : '';
        if (!Validator::required($name)) {
            Response::error('VALIDATION_ERROR', 'Name is required', 400, array('name' => 'Name is required'));
        }
        $existing = CategoryModel::findByName($company['id'], $name);
        if ($existing && intval($existing['id']) !== intval($categoryId)) {
            Response::error('CONFLICT', 'Category already exists', 409);
        }
        CategoryModel::update($categoryId, $name);
        Response::success(array('id' => $categoryId, 'name' => $name));
    }

    public static function delete($request)
    {
        $user = $request->getUser();
        $categoryId = $request->getParam('id');
        $category = CategoryModel::findById($categoryId);
        if (!$category) {
            Response::error('NOT_FOUND', 'Category not found', 404);
        }
        $company = CompanyModel::findByIdForUser($category['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        CategoryModel::delete($categoryId);
        Response::success(array('id' => $categoryId));
    }
}
