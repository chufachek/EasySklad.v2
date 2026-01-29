<?php
$pageTitle = 'Easy склад · Заказы';
$page = 'orders';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Заказы</h1>
        <p class="muted">Список продаж и статусы оплат.</p>
    </div>
    <button class="btn btn-primary" id="createOrderBtn">Создать заказ</button>
</div>
<div class="grid two">
    <div class="card">
        <div id="ordersTable" class="table-wrap"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Детали заказа</h2>
            <span class="pill" id="orderStatus">—</span>
        </div>
        <div id="orderDetails"></div>
    </div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
