<?php
if (!empty($isAuth)) {
    return;
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title">Навигация</span>
        <button class="icon-button sidebar-close" id="sidebarClose" aria-label="Закрыть меню">
            <i data-lucide="x"></i>
        </button>
    </div>
    <nav>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Основное</span>
            <a href="<?php echo base_url('/app/dashboard'); ?>" data-page="dashboard" title="Дашборд">
                <i data-lucide="layout-dashboard"></i>
                <span>Дашборд</span>
            </a>
            <a href="<?php echo base_url('/app/pos'); ?>" data-page="pos" title="Касса">
                <i data-lucide="scan-line"></i>
                <span>Касса</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Операции</span>
            <a href="<?php echo base_url('/app/income'); ?>" data-page="income" title="Приход">
                <i data-lucide="package-plus"></i>
                <span>Приход</span>
            </a>
            <a href="<?php echo base_url('/app/orders'); ?>" data-page="orders" title="Заказы">
                <i data-lucide="clipboard-list"></i>
                <span>Заказы</span>
            </a>
            <a href="<?php echo base_url('/app/services'); ?>" data-page="services" title="Услуги">
                <i data-lucide="briefcase"></i>
                <span>Услуги</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Справочники</span>
            <a href="<?php echo base_url('/app/company'); ?>" data-page="company" title="Компания">
                <i data-lucide="building-2"></i>
                <span>Компания</span>
            </a>
            <a href="<?php echo base_url('/app/warehouses'); ?>" data-page="warehouses" title="Склады">
                <i data-lucide="warehouse"></i>
                <span>Склады</span>
            </a>
            <a href="<?php echo base_url('/app/products'); ?>" data-page="products" title="Товары">
                <i data-lucide="boxes"></i>
                <span>Товары</span>
            </a>
            <a href="<?php echo base_url('/app/categories'); ?>" data-page="categories" title="Категории">
                <i data-lucide="tag"></i>
                <span>Категории</span>
            </a>
            <a href="<?php echo base_url('/app/profile'); ?>" data-page="profile" title="Профиль">
                <i data-lucide="user"></i>
                <span>Профиль</span>
            </a>
        </div>
    </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<main class="main">
    <div class="breadcrumbs" id="breadcrumbs"></div>
    <section class="content">
