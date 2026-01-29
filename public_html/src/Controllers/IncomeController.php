<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Core\Helpers;
use Models\CompanyModel;
use Models\WarehouseModel;
use Models\StockModel;
use Services\IncomeService;

class IncomeController
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
        $query = $request->getQuery();
        list($page, $limit, $offset) = Helpers::paginate(isset($query['page']) ? $query['page'] : null, isset($query['limit']) ? $query['limit'] : null);
        $income = StockModel::incomeList($warehouseId, $limit, $offset);
        Response::success($income, 200, array('page' => $page, 'limit' => $limit));
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
        $items = isset($data['items']) ? $data['items'] : array();
        if (!is_array($items) || count($items) === 0) {
            $errors['items'] = 'Items are required';
        } else {
            foreach ($items as $index => $item) {
                if (!isset($item['product_id']) || !Validator::integer($item['product_id'])) {
                    $errors['items.' . $index . '.product_id'] = 'Product is required';
                }
                if (!isset($item['qty']) || !Validator::positiveNumber($item['qty'])) {
                    $errors['items.' . $index . '.qty'] = 'Qty must be > 0';
                }
                if (!isset($item['cost']) || !Validator::nonNegativeNumber($item['cost'])) {
                    $errors['items.' . $index . '.cost'] = 'Cost must be >= 0';
                }
            }
        }

        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }

        try {
            IncomeService::createIncome(
                $warehouseId,
                isset($data['supplier']) ? $data['supplier'] : null,
                isset($data['date']) ? $data['date'] : date('Y-m-d'),
                $items
            );
        } catch (\Exception $e) {
            if ($e->getMessage() === 'PRODUCT_NOT_FOUND') {
                Response::error('NOT_FOUND', 'Product not found', 404);
            }
            Response::error('SERVER_ERROR', 'Server error', 500);
        }

        Response::success(array('message' => 'Income recorded'), 201);
    }
}
