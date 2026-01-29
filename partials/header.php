<header class="header">
    <div class="header-left">
        <button class="icon-button sidebar-toggle" id="sidebarMobileToggle" aria-label="Открыть меню">
            <i data-lucide="menu"></i>
        </button>
        <button class="icon-button sidebar-toggle" id="sidebarToggle" aria-label="Свернуть меню">
            <i data-lucide="panel-left"></i>
        </button>
        <div class="logo">Easy <span>склад</span></div>
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
        <a class="btn btn-primary" href="/app/income">+ Приход</a>
        <a class="btn btn-secondary" href="/app/orders">+ Заказ</a>
        <a class="btn btn-ghost" href="/app/pos">Касса</a>
    </div>
    <div class="header-right">
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
                    <a href="/app/profile">Профиль</a>
                    <a href="/logout">Выход</a>
                </div>
            </div>
        </div>
    </div>
</header>
