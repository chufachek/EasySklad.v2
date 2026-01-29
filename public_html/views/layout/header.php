<?php
if (!empty($isAuth)) {
    return;
}
?>
<header class="header">
    <div class="header-left">
        <div class="logo">Easy. <span>склад</span></div>
        <?php if (defined('DEBUG') && DEBUG) : ?>
            <div class="routing-indicator" style="margin-left:12px;font-size:12px;opacity:0.7;">
                Routing: <?php echo strtoupper(htmlspecialchars(routing_mode())); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="header-selects">
        <label class="field">
            <span>Компания</span>
            <select id="companySelect" class="choice-select"></select>
        </label>
        <label class="field">
            <span>Склад</span>
            <select id="warehouseSelect" class="choice-select"></select>
        </label>
    </div>
    <div class="header-actions">
        <button class="btn btn-primary" type="button" data-quick="income">+ Приход</button>
        <button class="btn btn-secondary" type="button" data-quick="order">+ Заказ</button>
        <button class="btn btn-ghost" type="button" data-quick="pos">Касса</button>
    </div>
    <div class="header-right">
        <div class="header-controls">
            <button class="icon-button sidebar-toggle" id="sidebarMobileToggle" aria-label="Открыть меню">
                <i data-lucide="menu"></i>
            </button>
            <button class="icon-button sidebar-toggle" id="sidebarToggle" aria-label="Свернуть меню">
                <i data-lucide="panel-left"></i>
            </button>
        </div>
        <button class="theme-toggle" id="themeToggle" aria-label="Переключить тему">
            <span class="theme-icon">🌙</span>
        </button>
        <div class="profile-menu">
            <button class="profile-trigger" id="profileTrigger">
                <span class="avatar">AS</span>
                <span class="profile-name" id="profileName">Пользователь</span>
                <span class="caret">▾</span>
            </button>
            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-meta">
                    <div class="profile-meta-name" id="profileFullName">—</div>
                    <div class="profile-meta-row">User ID: <strong id="profileUserId">—</strong></div>
                    <div class="profile-meta-row">Тариф: <strong id="profileTariff">—</strong></div>
                    <div class="profile-meta-row">Баланс: <strong id="profileBalance">—</strong></div>
                </div>
                <div class="profile-links">
                    <a href="<?php echo base_url('/app/profile'); ?>">Профиль</a>
                    <a href="<?php echo base_url('/logout'); ?>">Выход</a>
                </div>
            </div>
        </div>
    </div>
</header>
