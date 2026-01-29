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
        <div class="metric-header">
            <h3>Выручка</h3>
            <span class="badge badge-success" id="metricRevenueTrend">+0%</span>
        </div>
        <div class="metric-value" id="metricRevenue">—</div>
        <span class="muted metric-sub" id="metricRevenueSub">за 7 дней</span>
    </div>
    <div class="card metric">
        <div class="metric-header">
            <h3>Продажи</h3>
            <span class="badge badge-soft" id="metricOrdersPaid">Оплачено</span>
        </div>
        <div class="metric-value" id="metricSales">—</div>
        <span class="muted metric-sub" id="metricSalesSub">средний чек —</span>
    </div>
    <div class="card metric">
        <div class="metric-header">
            <h3>Остатки</h3>
            <span class="badge badge-warning" id="metricStockAlert">Низкий остаток</span>
        </div>
        <div class="metric-value" id="metricStock">—</div>
        <span class="muted metric-sub" id="metricStockSub">SKU на складах</span>
    </div>
    <div class="card metric">
        <div class="metric-header">
            <h3>Последние операции</h3>
            <span class="badge badge-neutral" id="metricOpsTrend">24ч</span>
        </div>
        <div class="metric-value" id="metricOps">—</div>
        <span class="muted metric-sub">за последние сутки</span>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-main">
        <div class="card">
            <div class="card-header">
                <h2>Выручка по дням</h2>
                <div class="segmented" id="revenueRange">
                    <button type="button" data-range="7d" class="active">7 дней</button>
                    <button type="button" data-range="30d">30 дней</button>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="grid two">
            <div class="card">
                <div class="card-header">
                    <h2>Структура продаж</h2>
                    <span class="pill">Категории</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="salesPieChart"></canvas>
                </div>
                <div class="chart-legend" id="salesPieLegend"></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2>Последние операции</h2>
                    <span class="pill">24 часа</span>
                </div>
                <div class="activity-list" id="dashboardOps"></div>
            </div>
        </div>
    </div>
    <div class="dashboard-side">
        <div class="card">
            <div class="card-header">
                <h2>Последние заказы</h2>
                <a class="link" href="/app/orders">Все заказы</a>
            </div>
            <div id="dashboardOrders" class="table-wrap"></div>
        </div>
    </div>
</div>

<div class="grid three">
    <div class="card">
        <div class="card-header">
            <h2>Топ товаров</h2>
            <a class="link" href="/app/products">В каталог</a>
        </div>
        <div id="dashboardTopProducts" class="table-wrap"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Низкие остатки</h2>
            <a class="link" href="/app/products">К товарам</a>
        </div>
        <div id="dashboardLowStock" class="table-wrap"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Активность</h2>
            <span class="pill">Онлайн</span>
        </div>
        <div class="activity-list" id="dashboardActivity"></div>
    </div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
