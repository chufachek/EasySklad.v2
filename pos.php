<?php
$pageTitle = 'Easy склад · Касса';
$page = 'pos';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Касса</h1>
        <p class="muted">Быстрые продажи: минимум кликов и максимум скорости.</p>
    </div>
    <div class="hotkeys">
        <span>/ поиск</span>
        <span>Enter добавить</span>
        <span>Ctrl+Enter оплата</span>
    </div>
</div>
<div class="pos-layout">
    <div class="card pos-panel">
        <div class="pos-search">
            <input type="text" id="posSearch" placeholder="Найти товар или услугу (/)" autocomplete="off">
            <div id="posSuggestions" class="suggestions"></div>
        </div>
        <div class="pos-actions">
            <button class="btn btn-secondary" id="posAddService">Добавить услугу</button>
            <button class="btn btn-ghost" id="posClear">Очистить</button>
        </div>
        <div id="posCatalog" class="pos-catalog"></div>
    </div>
    <div class="card pos-panel">
        <div class="card-header">
            <h2>Чек</h2>
            <span class="pill" id="posItemsCount">0 позиций</span>
        </div>
        <div id="posReceipt" class="pos-receipt"></div>
        <div class="pos-summary" id="posSummary"></div>
        <div class="pos-buttons">
            <button class="btn btn-primary" id="posPay">Оплатить</button>
            <button class="btn btn-secondary" id="posSave">Сохранить</button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
