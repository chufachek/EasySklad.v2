<?php
namespace Controllers;

use Core\Response;
use Models\CompanyModel;
use Models\OrderModel;
use Models\ProductModel;
use Models\WarehouseModel;

class DashboardController
{
    public static function show($request)
    {
        $user = $request->getUser();
        $query = $request->getQuery();
        $companyId = isset($query['companyId']) ? $query['companyId'] : null;
        $warehouseId = isset($query['warehouseId']) ? $query['warehouseId'] : null;
        $range = isset($query['range']) ? $query['range'] : '7d';
        $days = $range === '30d' ? 30 : 7;

        if ($companyId) {
            $company = CompanyModel::findByIdForUser($companyId, $user['id']);
            if (!$company) {
                Response::error('FORBIDDEN', 'No access to company', 403);
            }
        }

        if ($warehouseId) {
            $warehouse = WarehouseModel::findById($warehouseId);
            if (!$warehouse) {
                Response::error('NOT_FOUND', 'Warehouse not found', 404);
            }
        }

        $orders = array();
        if ($warehouseId) {
            $orders = OrderModel::listForWarehouse($warehouseId, null, 50, 0);
        }

        $series = array();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $series[] = array('date' => $date, 'value' => rand(1200, 6200));
        }

        $products = array();
        if ($warehouseId) {
            $products = ProductModel::forWarehouse($warehouseId, null, 100, 0);
        }

        $payload = array(
            'revenue_series' => $series,
            'revenue_total' => array_sum(array_map(function ($item) {
                return $item['value'];
            }, $series)),
            'orders_count' => count($orders),
            'avg_check' => count($orders) ? round(array_sum(array_map(function ($order) {
                return $order['total'];
            }, $orders)) / count($orders), 2) : 0,
            'stock_low' => array_slice(array_values(array_filter($products, function ($item) {
                return isset($item['qty']) && $item['qty'] <= 10;
            })), 0, 5),
            'stock_out' => array_slice(array_values(array_filter($products, function ($item) {
                return isset($item['qty']) && $item['qty'] <= 0;
            })), 0, 5),
            'last_orders' => array_slice($orders, 0, 5),
            'last_ops' => array(
                array('label' => 'Приход', 'value' => rand(2, 8), 'time' => 'Сегодня'),
                array('label' => 'Продажи', 'value' => rand(3, 12), 'time' => 'Сегодня')
            ),
            'pie_series' => array(
                array('label' => 'Товары', 'value' => rand(60, 80)),
                array('label' => 'Услуги', 'value' => rand(20, 40))
            )
        );

        Response::success($payload);
    }
}
