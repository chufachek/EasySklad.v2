$(async () => {
    const initTheme = () => {
        const theme = State.get('theme') || 'light';
        if (theme === 'dark') {
            $('body').addClass('theme-dark');
        }
        $('#themeToggle').on('click', () => {
            $('body').toggleClass('theme-dark');
            State.set('theme', $('body').hasClass('theme-dark') ? 'dark' : 'light');
            $(document).trigger('themeChanged');
        });
    };

    const initProfileMenu = () => {
        $('#profileTrigger').on('click', () => {
            $('.profile-menu').toggleClass('open');
        });
        $(document).on('click', (event) => {
            if (!$(event.target).closest('.profile-menu').length) {
                $('.profile-menu').removeClass('open');
            }
        });
    };

    const initSidebar = () => {
        const applyCollapsed = (collapsed) => {
            $('body').toggleClass('sidebar-collapsed', collapsed);
            State.set('sidebarCollapsed', collapsed);
        };

        applyCollapsed(Boolean(State.get('sidebarCollapsed')));

        $('#sidebarToggle').on('click', () => {
            applyCollapsed(!$('body').hasClass('sidebar-collapsed'));
        });

        $('#sidebarMobileToggle').on('click', () => {
            $('body').addClass('sidebar-open');
        });

        $('#sidebarClose, #sidebarOverlay').on('click', () => {
            $('body').removeClass('sidebar-open');
        });
    };

    const applyProfileData = (user) => {
        if (!user) return;
        const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim() || user.username || user.email;
        const initials = (user.first_name?.[0] || user.username?.[0] || 'U').toUpperCase();
        $('#profileName').text(user.first_name || user.username || 'Пользователь');
        $('#profileFullName').text(fullName);
        $('#profileUserId').text(`#${user.id}`);
        $('#profileTariff').text(user.tariff || 'Free');
        $('#profileBalance').text(`${Number(user.balance || 0).toLocaleString('ru-RU')} ₽`);
        $('.avatar').text(initials);
    };

    const initProfileData = async () => {
        try {
            const user = await Api.getMe();
            applyProfileData(user);
        } catch (error) {
            // ignore if not authenticated
        }
    };

    const initNavigation = () => {
        const page = $('body').data('page');
        if (page) {
            State.set('lastPage', page);
            $(`.sidebar a[data-page="${page}"]`).addClass('active');
        }
    };

    const initQuickActions = () => {
        const buildProductRow = (label, placeholder = 'Начните ввод') => $(`
            <div class="modal-row" data-row>
                <label class="field">
                    <span>${label}</span>
                    <div class="autocomplete">
                        <input type="text" class="modal-autocomplete" placeholder="${placeholder}">
                        <div class="suggestions" data-suggestions></div>
                    </div>
                </label>
                <label class="field">
                    <span>Количество</span>
                    <input type="number" min="1" value="1" class="js-qty">
                </label>
                <label class="field">
                    <span>Цена</span>
                    <input type="number" min="0" value="0" class="js-price">
                </label>
            </div>
        `);

        const attachAutocomplete = (row, list) => {
            Autocomplete.bind({
                input: row.find('.modal-autocomplete'),
                list,
                onSelect: (item) => {
                    row.find('.modal-autocomplete').val(item.label);
                }
            });
        };

        const calculateTotal = (wrapper, discountInput) => {
            let total = 0;
            wrapper.find('[data-row]').each((_, row) => {
                const qty = Number($(row).find('.js-qty').val()) || 0;
                const price = Number($(row).find('.js-price').val()) || 0;
                total += qty * price;
            });
            const discount = Number(discountInput?.val() || 0);
            const finalTotal = Math.max(0, total - discount);
            return { total, finalTotal };
        };

        const openIncomeModal = async () => {
            Modal.open({
                title: 'Создать приход',
                size: 'lg',
                content: `
                    <div class="modal-grid">
                        <label class="field">
                            <span>Склад</span>
                            <select class="choice-select" id="incomeWarehouse"></select>
                        </label>
                        <label class="field">
                            <span>Поставщик</span>
                            <input type="text" id="incomeSupplier" placeholder="ООО Поставщик" />
                        </label>
                        <label class="field">
                            <span>Дата</span>
                            <input type="date" id="incomeDate" />
                        </label>
                    </div>
                    <div class="modal-section">
                        <div class="modal-table" id="incomeItems"></div>
                        <div class="modal-row-actions">
                            <button class="btn btn-ghost" type="button" id="incomeAddRow">+ Добавить позицию</button>
                        </div>
                    </div>
                    <div class="modal-summary">
                        <span>Итого</span>
                        <span id="incomeTotal">0 ₽</span>
                    </div>
                `,
                onSubmit: () => {
                    Toast.show('Приход создан');
                    Modal.close();
                },
                onOpen: async (backdrop) => {
                    const companyId = State.get('activeCompanyId');
                    const warehouses = companyId ? await Api.listWarehouses(companyId) : [];
                    const warehouseSelect = backdrop.find('#incomeWarehouse');
                    if (!warehouses.length) {
                        warehouseSelect.append('<option value="">Нет складов</option>');
                    } else {
                        warehouses.forEach((warehouse) => {
                            warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name}</option>`);
                        });
                    }
                    Selects.initSelect(warehouseSelect);
                    backdrop.find('#incomeDate').val(new Date().toISOString().slice(0, 10));

                    const warehouseId = State.get('activeWarehouseId') || warehouses[0]?.id;
                    const products = warehouseId ? await Api.listProducts(warehouseId) : [];
                    const list = products.map((item) => ({ id: item.id, label: item.name }));
                    const itemsWrap = backdrop.find('#incomeItems');
                    const addRow = () => {
                        const row = buildProductRow('Товар');
                        itemsWrap.append(row);
                        attachAutocomplete(row, list);
                        row.find('input').on('input', () => {
                            const totals = calculateTotal(itemsWrap);
                            backdrop.find('#incomeTotal').text(`${totals.total.toLocaleString('ru-RU')} ₽`);
                        });
                    };
                    addRow();
                    backdrop.find('#incomeAddRow').on('click', addRow);
                }
            });
        };

        const openOrderModal = async () => {
            Modal.open({
                title: 'Создать заказ',
                size: 'xl',
                content: `
                    <div class="modal-grid">
                        <label class="field">
                            <span>Клиент</span>
                            <input type="text" id="orderCustomer" placeholder="Имя клиента" />
                        </label>
                        <label class="field">
                            <span>Склад</span>
                            <select class="choice-select" id="orderWarehouse"></select>
                        </label>
                        <label class="field">
                            <span>Оплата</span>
                            <select class="choice-select" id="orderPayment">
                                <option value="cash">Наличные</option>
                                <option value="card">Карта</option>
                                <option value="transfer">Перевод</option>
                            </select>
                        </label>
                        <label class="field">
                            <span>Скидка</span>
                            <input type="number" min="0" value="0" id="orderDiscount" />
                        </label>
                    </div>
                    <div class="modal-section">
                        <strong>Товары</strong>
                        <div class="modal-table" id="orderItems"></div>
                        <div class="modal-row-actions">
                            <button class="btn btn-ghost" type="button" id="orderAddItem">+ Добавить товар</button>
                        </div>
                    </div>
                    <div class="modal-section">
                        <strong>Услуги</strong>
                        <div class="modal-table" id="orderServices"></div>
                        <div class="modal-row-actions">
                            <button class="btn btn-ghost" type="button" id="orderAddService">+ Добавить услугу</button>
                        </div>
                    </div>
                    <div class="modal-summary">
                        <span>Итого</span>
                        <span id="orderTotal">0 ₽</span>
                    </div>
                `,
                footer: `
                    <button class="btn btn-ghost" data-close>Отмена</button>
                    <button class="btn btn-secondary" data-draft>Черновик</button>
                    <button class="btn btn-primary" data-pay>Оплатить</button>
                `,
                onOpen: async (backdrop) => {
                    const companyId = State.get('activeCompanyId');
                    const warehouses = companyId ? await Api.listWarehouses(companyId) : [];
                    const warehouseSelect = backdrop.find('#orderWarehouse');
                    if (!warehouses.length) {
                        warehouseSelect.append('<option value="">Нет складов</option>');
                    } else {
                        warehouses.forEach((warehouse) => {
                            warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name}</option>`);
                        });
                    }
                    Selects.initSelect(warehouseSelect);
                    Selects.initSelect(backdrop.find('#orderPayment'));

                    const warehouseId = State.get('activeWarehouseId') || warehouses[0]?.id;
                    const products = warehouseId ? await Api.listProducts(warehouseId) : [];
                    const services = await Api.listServices();
                    const productList = products.map((item) => ({ id: item.id, label: item.name }));
                    const serviceList = services.map((item) => ({ id: item.id, label: item.name }));

                    const itemsWrap = backdrop.find('#orderItems');
                    const servicesWrap = backdrop.find('#orderServices');
                    const updateTotals = () => {
                        const totals = calculateTotal(itemsWrap);
                        const servicesTotals = calculateTotal(servicesWrap);
                        const discountInput = backdrop.find('#orderDiscount');
                        const discount = Number(discountInput.val() || 0);
                        const total = totals.total + servicesTotals.total - discount;
                        backdrop.find('#orderTotal').text(`${Math.max(0, total).toLocaleString('ru-RU')} ₽`);
                    };

                    const addItemRow = () => {
                        const row = buildProductRow('Товар');
                        itemsWrap.append(row);
                        attachAutocomplete(row, productList);
                        row.find('input').on('input', updateTotals);
                    };

                    const addServiceRow = () => {
                        const row = buildProductRow('Услуга', 'Выберите услугу');
                        servicesWrap.append(row);
                        attachAutocomplete(row, serviceList);
                        row.find('input').on('input', updateTotals);
                    };

                    addItemRow();
                    addServiceRow();
                    backdrop.find('#orderAddItem').on('click', addItemRow);
                    backdrop.find('#orderAddService').on('click', addServiceRow);
                    backdrop.find('#orderDiscount').on('input', updateTotals);

                    backdrop.on('click', '[data-draft]', () => {
                        Toast.show('Заказ сохранён как черновик');
                        Modal.close();
                    });
                    backdrop.on('click', '[data-pay]', () => {
                        Toast.show('Заказ оплачен');
                        Modal.close();
                    });
                }
            });
        };

        const openPosModal = () => {
            Modal.open({
                title: 'Кассовый режим',
                size: 'fullscreen',
                content: `
                    <div class="pos-layout">
                        <div class="card">
                            <div class="card-header">
                                <h2>Сканирование</h2>
                                <span class="pill">Готов к работе</span>
                            </div>
                            <div class="pos-search">
                                <input type="text" placeholder="Найти товар или услугу" />
                            </div>
                            <div class="pos-catalog">
                                <div class="pos-item"><strong>Товар A</strong><span class="muted">120 ₽</span></div>
                                <div class="pos-item"><strong>Товар B</strong><span class="muted">80 ₽</span></div>
                                <div class="pos-item"><strong>Услуга</strong><span class="muted">350 ₽</span></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h2>Чек</h2>
                                <span class="badge badge-success">Оплата</span>
                            </div>
                            <div class="pos-receipt">
                                <div class="receipt-item"><span>Товар A ×1</span><strong>120 ₽</strong></div>
                                <div class="receipt-item"><span>Услуга ×1</span><strong>350 ₽</strong></div>
                            </div>
                            <div class="pos-summary">
                                <div class="receipt-item"><span>Итого</span><strong>470 ₽</strong></div>
                            </div>
                            <div class="pos-buttons">
                                <button class="btn btn-primary">Оплатить</button>
                                <button class="btn btn-ghost">Черновик</button>
                            </div>
                        </div>
                    </div>
                `,
                footer: `
                    <button class="btn btn-ghost" data-close>Закрыть</button>
                    <a class="btn btn-primary" href="/app/pos">Открыть полную кассу</a>
                `
            });
        };

        $('[data-quick="income"]').on('click', openIncomeModal);
        $('[data-quick="order"]').on('click', openOrderModal);
        $('[data-quick="pos"]').on('click', openPosModal);
    };

    await Selects.init();
    initTheme();
    initProfileMenu();
    initSidebar();
    initNavigation();
    initProfileData();
    initQuickActions();
    Breadcrumbs.render();

    $(document).on('profileUpdated', (_, user) => {
        applyProfileData(user);
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }

    $(document).on('companyChanged warehouseChanged', () => {
        const page = $('body').data('page');
        if (page) {
            $(document).trigger(`page:${page}`);
        }
    });

    if (window.PageHandlers) {
        const page = $('body').data('page');
        PageHandlers[page]?.();
    }
});
