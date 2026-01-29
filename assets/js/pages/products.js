window.PageHandlers = window.PageHandlers || {};

PageHandlers.products = () => {
    const render = async () => {
        const warehouseId = State.get('activeWarehouseId');
        if (!warehouseId) {
            $('#productsTable').html('<div class="empty-state">Выберите склад</div>');
            return;
        }
        $('#productsTable').html('<div class="empty-state">Загрузка...</div>');
        const query = $('#productSearch').val().toLowerCase();
        const filter = $('#productFilter').val();
        const products = await Api.listProducts(warehouseId);
        const filtered = products.filter((item) => {
            const matches = item.name.toLowerCase().includes(query) || item.sku.toLowerCase().includes(query);
            const stockOk = filter === 'all' || (filter === 'low' ? item.stock < 50 : item.stock >= 50);
            return matches && stockOk;
        });

        $('#productsTable').html(Table.render({
            columns: [
                { key: 'sku', label: 'Артикул' },
                { key: 'name', label: 'Название' },
                { key: 'stock', label: 'Остаток' },
                { key: 'price', label: 'Цена' },
                { key: 'actions', label: 'Действия' }
            ],
            rows: filtered.map((item) => ({
                sku: item.sku,
                name: item.name,
                stock: `${item.stock} шт.`,
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
                    <span>Остаток</span>
                    <input id="productStock" type="number" value="${product.stock || 0}" />
                </label>
                <label class="field">
                    <span>Цена</span>
                    <input id="productPrice" type="number" value="${product.price || 0}" />
                </label>
            `,
            onSubmit: async () => {
                await Api.saveProduct({
                    id: product.id,
                    warehouseId: State.get('activeWarehouseId'),
                    sku: $('#productSku').val(),
                    name: $('#productName').val(),
                    stock: Number($('#productStock').val()),
                    price: Number($('#productPrice').val())
                });
                Modal.close();
                Toast.show('Товар сохранён');
                render();
            }
        });
    };

    $('#addProductBtn').on('click', () => openModal());
    $('#productSearch, #productFilter').on('input change', render);

    $('#productsTable').on('click', '[data-edit]', async (event) => {
        const warehouseId = State.get('activeWarehouseId');
        const products = await Api.listProducts(warehouseId);
        const product = products.find((item) => item.id === $(event.target).data('edit'));
        openModal(product);
    });

    $(document).on('warehouseChanged', render);
    render();
};
