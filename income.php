<?php
$pageTitle = 'Easy склад · Приход';
$page = 'income';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Приход</h1>
        <p class="muted">Фиксируйте поставки и пополнение складов.</p>
    </div>
    <button class="btn btn-primary" id="saveIncomeBtn">Сохранить</button>
</div>
<div class="grid two">
    <div class="card">
        <h2>Данные прихода</h2>
        <div class="form-grid">
            <label class="field">
                <span>Склад</span>
                <select id="incomeWarehouse" class="choice-select"></select>
            </label>
            <label class="field">
                <span>Дата</span>
                <input type="date" id="incomeDate">
            </label>
            <label class="field">
                <span>Поставщик</span>
                <input type="text" id="incomeSupplier" placeholder="ООО Поставщик">
            </label>
        </div>
    </div>
    <div class="card">
        <h2>Итого</h2>
        <div class="summary" id="incomeSummary"></div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h2>Товары</h2>
        <button class="btn btn-secondary" id="addIncomeRow">Добавить позицию</button>
    </div>
    <div id="incomeTable" class="table-wrap"></div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
