window.PageHandlers = window.PageHandlers || {};

PageHandlers.pos = async () => {
    let receipt = [];

    const [services, products] = await Promise.all([
        Api.listServices(),
        State.get('activeWarehouseId') ? Api.listProducts(State.get('activeWarehouseId')) : []
    ]);

    const catalog = [
        ...products.map((item) => ({ id: item.id, label: item.name, price: item.price, type: 'product' })),
        ...services.map((item) => ({ id: item.id, label: item.name, price: item.price, type: 'service' }))
    ];

    const renderCatalog = () => {
        $('#posCatalog').html(catalog.map((item) => `
            <div class="pos-item" data-id="${item.id}">
                <strong>${item.label}</strong>
                <span>${item.price} ₽</span>
            </div>
        `).join(''));
    };

    const renderReceipt = () => {
        const total = receipt.reduce((sum, item) => sum + item.price * item.qty, 0);
        $('#posReceipt').html(receipt.map((item) => `
            <div class="receipt-item">
                <div>
                    <strong>${item.label}</strong>
                    <div class="muted">${item.qty} × ${item.price} ₽</div>
                </div>
                <div><strong>${(item.qty * item.price).toLocaleString('ru-RU')} ₽</strong></div>
            </div>
        `).join(''));
        $('#posSummary').html(`
            <div><strong>Итого:</strong> ${total.toLocaleString('ru-RU')} ₽</div>
            <div class="muted">Оплата: карта / наличные</div>
        `);
        $('#posItemsCount').text(`${receipt.length} позиций`);
    };

    const addItem = (item) => {
        const existing = receipt.find((entry) => entry.id === item.id && entry.type === item.type);
        if (existing) {
            existing.qty += 1;
        } else {
            receipt.push({ ...item, qty: 1 });
        }
        renderReceipt();
    };

    Autocomplete.bind({
        input: '#posSearch',
        list: catalog,
        onSelect: addItem
    });

    $('#posCatalog').on('click', '.pos-item', (event) => {
        const item = catalog.find((entry) => entry.id === $(event.currentTarget).data('id'));
        if (item) addItem(item);
    });

    $('#posClear').on('click', () => {
        receipt = [];
        renderReceipt();
    });

    $('#posPay').on('click', () => Toast.show('Оплата принята'));
    $('#posSave').on('click', () => Toast.show('Чек сохранён'));

    $('#posAddService').on('click', () => {
        const service = services[0];
        if (service) {
            addItem({ id: service.id, label: service.name, price: service.price, type: 'service' });
        }
    });

    $(document).on('keydown', (event) => {
        if (event.key === '/' && document.activeElement !== $('#posSearch')[0]) {
            event.preventDefault();
            $('#posSearch').focus();
        }
        if (event.key === 'Enter' && document.activeElement === $('#posSearch')[0]) {
            const first = catalog[0];
            if (first) addItem(first);
        }
        if (event.ctrlKey && event.key === 'Enter') {
            Toast.show('Заказ оформлен');
        }
    });

    $(document).on('warehouseChanged', () => location.reload());

    renderCatalog();
    renderReceipt();
};
