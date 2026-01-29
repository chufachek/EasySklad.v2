window.PageHandlers = window.PageHandlers || {};

PageHandlers.products = () => {
    const render = async () => {
        const warehouseId = State.get('activeWarehouseId');
        const companyId = State.get('activeCompanyId');
        if (!warehouseId) {
            $('#productsTable').html('<div class="empty-state">Выберите склад</div>');
            return;
        }
        $('#productsTable').html('<div class="empty-state">Загрузка...</div>');
        const query = $('#productSearch').val().toLowerCase();
        const filter = $('#productFilter').val();
        const categoryFilter = $('#categoryFilter').val();
        const [products, categories] = await Promise.all([
            Api.listProducts(warehouseId),
            companyId ? Api.listCategories(companyId) : []
        ]);
        const categorySelect = $('#categoryFilter');
        if (categorySelect.data('loaded') !== companyId) {
            const currentValue = categorySelect.val() || 'all';
            categorySelect.empty().append('<option value="all">Все категории</option>');
            categories.forEach((category) => {
                categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
            });
            categorySelect.val(currentValue);
            categorySelect.data('loaded', companyId || '');
            Selects.initSelect(categorySelect);
        }
        const filtered = products.filter((item) => {
            const matches = item.name.toLowerCase().includes(query) || item.sku.toLowerCase().includes(query);
            const stockOk = filter === 'all' || (filter === 'low' ? item.stock < 50 : item.stock >= 50);
            const categoryOk = categoryFilter === 'all' || String(item.categoryId || item.category_id || '') === String(categoryFilter);
            return matches && stockOk && categoryOk;
        });

        $('#productsTable').html(Table.render({
            columns: [
                { key: 'sku', label: 'Артикул' },
                { key: 'name', label: 'Название' },
                { key: 'category', label: 'Категория' },
                { key: 'stock', label: 'Остаток' },
                { key: 'price', label: 'Цена' },
                { key: 'actions', label: 'Действия' }
            ],
            rows: filtered.map((item) => ({
                sku: item.sku,
                name: item.name,
                category: item.categoryName || item.category_name || 'Без категории',
                stock: `${item.stock ?? item.qty ?? 0} шт.`,
                price: `${item.price} ₽`,
                actions: `<button class="btn btn-ghost" data-edit="${item.id}">Редактировать</button>`
            }))
        }));
    };

    const openModal = (product = {}) => {
        Modal.open({
            title: product.id ? 'Редактировать товар' : 'Новый товар',
            content: `
                <label class="field">
                    <span>Артикул</span>
                    <input id="productSku" type="text" value="${product.sku || ''}" />
                </label>
                <label class="field">
                    <span>Название</span>
                    <input id="productName" type="text" value="${product.name || ''}" />
                </label>
                <label class="field">
                    <span>Категория</span>
                    <select id="productCategory" class="choice-select"></select>
                </label>
                <label class="field">
                    <span>Остаток</span>
                    <input id="productStock" type="number" value="${product.stock ?? product.qty ?? 0}" />
                </label>
                <label class="field">
                    <span>Цена</span>
                    <input id="productPrice" type="number" value="${product.price || 0}" />
                </label>
            `,
            onOpen: async (backdrop) => {
                const companyId = State.get('activeCompanyId');
                const categories = companyId ? await Api.listCategories(companyId) : [];
                const select = backdrop.find('#productCategory');
                select.append('<option value="">Без категории</option>');
                categories.forEach((category) => {
                    select.append(`<option value="${category.id}">${category.name}</option>`);
                });
                select.val(product.categoryId || product.category_id || '');
                Selects.initSelect(select);
            },
            onSubmit: async () => {
                await Api.saveProduct({
                    id: product.id,
                    warehouseId: State.get('activeWarehouseId'),
                    sku: $('#productSku').val(),
                    name: $('#productName').val(),
                    stock: Number($('#productStock').val()),
                    price: Number($('#productPrice').val()),
                    categoryId: $('#productCategory').val() || null
                });
                Modal.close();
                Toast.show('Товар сохранён');
                render();
            }
        });
    };

    $('#addProductBtn').on('click', () => openModal());
    $('#productSearch, #productFilter, #categoryFilter').on('input change', render);

    $('#productsTable').on('click', '[data-edit]', async (event) => {
        const warehouseId = State.get('activeWarehouseId');
        const products = await Api.listProducts(warehouseId);
        const product = products.find((item) => item.id === $(event.target).data('edit'));
        openModal(product);
    });

    $(document).on('warehouseChanged companyChanged', render);
    render();
};
