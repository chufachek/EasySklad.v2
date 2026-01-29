<?php
$pageTitle = 'Easy склад · Дашборд';
$page = 'dashboard';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Дашборд</h1>
        <p class="muted">Ключевые показатели и последние действия.</p>
    </div>
    <button class="btn btn-primary" id="quickCreateOrder">Новый заказ</button>
</div>
<div class="grid cards">
    <div class="card metric">
        <h3>Выручка</h3>
        <div class="metric-value" id="metricRevenue">—</div>
        <span class="muted">за 7 дней</span>
    </div>
    <div class="card metric">
        <h3>Продажи</h3>
        <div class="metric-value" id="metricSales">—</div>
        <span class="muted">за сегодня</span>
    </div>
    <div class="card metric">
        <h3>Остатки</h3>
        <div class="metric-value" id="metricStock">—</div>
        <span class="muted">единиц на складах</span>
    </div>
    <div class="card metric">
        <h3>Последние операции</h3>
        <div class="metric-value" id="metricOps">—</div>
        <span class="muted">за 24 часа</span>
    </div>
</div>
<div class="grid two">
    <div class="card">
        <div class="card-header">
            <h2>Последние заказы</h2>
            <a class="link" href="/app/orders">Все заказы</a>
        </div>
        <div id="dashboardOrders" class="table-wrap"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Низкие остатки</h2>
            <a class="link" href="/app/products">К товарам</a>
        </div>
        <div id="dashboardLowStock" class="table-wrap"></div>
    </div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
