<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Core\Helpers;
use Models\CompanyModel;
use Models\WarehouseModel;
use Models\ServiceModel;
use Models\OrderModel;
use Services\OrderService;

class OrdersController
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
        $status = isset($query['status']) ? $query['status'] : null;
        $orders = OrderModel::listForWarehouse($warehouseId, $status, $limit, $offset);
        Response::success($orders, 200, array('page' => $page, 'limit' => $limit));
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
        $services = isset($data['services']) ? $data['services'] : array();
        $paymentMethod = isset($data['payment_method']) ? $data['payment_method'] : null;

        if (!$items && !$services) {
            $errors['items'] = 'Items or services are required';
        }
        if (!$paymentMethod || !in_array($paymentMethod, array('cash', 'card', 'transfer'), true)) {
            $errors['payment_method'] = 'Invalid payment method';
        }
        if (isset($data['discount']) && !Validator::nonNegativeNumber($data['discount'])) {
            $errors['discount'] = 'Discount must be >= 0';
        }

        if (is_array($items)) {
            foreach ($items as $index => $item) {
                if (!isset($item['product_id']) || !Validator::integer($item['product_id'])) {
                    $errors['items.' . $index . '.product_id'] = 'Product is required';
                }
                if (!isset($item['qty']) || !Validator::positiveNumber($item['qty'])) {
                    $errors['items.' . $index . '.qty'] = 'Qty must be > 0';
                }
                if (!isset($item['price']) || !Validator::nonNegativeNumber($item['price'])) {
                    $errors['items.' . $index . '.price'] = 'Price must be >= 0';
                }
            }
        }

        if (is_array($services)) {
            foreach ($services as $index => $service) {
                if (!isset($service['service_id']) || !Validator::integer($service['service_id'])) {
                    $errors['services.' . $index . '.service_id'] = 'Service is required';
                }
                if (!isset($service['qty']) || !Validator::positiveNumber($service['qty'])) {
                    $errors['services.' . $index . '.qty'] = 'Qty must be > 0';
                }
                if (!isset($service['price']) || !Validator::nonNegativeNumber($service['price'])) {
                    $errors['services.' . $index . '.price'] = 'Price must be >= 0';
                }
            }
        }

        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }

        if (is_array($services)) {
            foreach ($services as $service) {
                $serviceRow = ServiceModel::findById($service['service_id']);
                if (!$serviceRow || intval($serviceRow['company_id']) !== intval($warehouse['company_id'])) {
                    Response::error('FORBIDDEN', 'Service not available', 403);
                }
            }
        }

        try {
            $order = OrderService::createOrder($warehouseId, $warehouse['company_id'], $user['id'], array(
                'customer_name' => isset($data['customer_name']) ? $data['customer_name'] : null,
                'payment_method' => $paymentMethod,
                'discount' => isset($data['discount']) ? $data['discount'] : 0,
                'items' => is_array($items) ? $items : array(),
                'services' => is_array($services) ? $services : array(),
            ));
        } catch (\Exception $e) {
            if ($e->getMessage() === 'PRODUCT_NOT_FOUND') {
                Response::error('NOT_FOUND', 'Product not found', 404);
            }
            if ($e->getMessage() === 'INSUFFICIENT_STOCK') {
                Response::error('CONFLICT', 'Insufficient stock', 409);
            }
            Response::error('SERVER_ERROR', 'Server error', 500);
        }

        Response::success($order, 201);
    }

    public static function updateStatus($request)
    {
        $user = $request->getUser();
        $orderId = $request->getParam('id');
        $order = OrderModel::findById($orderId);
        if (!$order) {
            Response::error('NOT_FOUND', 'Order not found', 404);
        }
        $company = CompanyModel::findByIdForUser($order['company_id'], $user['id']);
        if (!$company) {
            Response::error('FORBIDDEN', 'No access to company', 403);
        }
        $data = $request->getBody();
        $status = isset($data['status']) ? $data['status'] : null;
        if (!in_array($status, array('draft', 'paid', 'canceled'), true)) {
            Response::error('VALIDATION_ERROR', 'Invalid status', 400, array('status' => 'Invalid status'));
        }
        OrderModel::updateStatus($orderId, $status);
        $order = OrderModel::findById($orderId);
        Response::success($order);
    }
}
