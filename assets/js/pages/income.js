window.PageHandlers = window.PageHandlers || {};

PageHandlers.income = () => {
    let rows = [
        { name: 'Яблоки Гала', qty: 20, price: 85 },
        { name: 'Бананы Эквадор', qty: 10, price: 95 }
    ];

    const renderSummary = () => {
        const total = rows.reduce((sum, row) => sum + row.qty * row.price, 0);
        $('#incomeSummary').html(`
            <div><strong>Позиций:</strong> ${rows.length}</div>
            <div><strong>Сумма:</strong> ${total.toLocaleString('ru-RU')} ₽</div>
        `);
    };

    const renderTable = () => {
        $('#incomeTable').html(Table.render({
            columns: [
                { key: 'name', label: 'Товар' },
                { key: 'qty', label: 'Количество' },
                { key: 'price', label: 'Цена' }
            ],
            rows: rows.map((row) => ({
                name: row.name,
                qty: `${row.qty} шт.`,
                price: `${row.price} ₽`
            }))
        }));
    };

    const initSelect = async () => {
        const companyId = State.get('activeCompanyId');
        const select = $('#incomeWarehouse');
        if (!companyId) {
            select.empty().append('<option value=\"\">Нет складов</option>');
            Selects.initSelect(select);
            return;
        }
        const warehouses = await Api.listWarehouses(companyId);
        select.empty();
        warehouses.forEach((item) => select.append(`<option value="${item.id}">${item.name}</option>`));
        select.val(State.get('activeWarehouseId'));
        Selects.initSelect(select);
    };

    $('#addIncomeRow').on('click', () => {
        rows.push({ name: 'Новая позиция', qty: 1, price: 0 });
        renderTable();
        renderSummary();
    });

    $('#saveIncomeBtn').on('click', () => Toast.show('Приход сохранён'));

    $(document).on('companyChanged warehouseChanged', initSelect);
    initSelect();
    renderTable();
    renderSummary();
};
