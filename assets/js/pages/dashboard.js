window.PageHandlers = window.PageHandlers || {};

PageHandlers.dashboard = async () => {
    $('#dashboardOrders').html('<div class="empty-state">Загрузка...</div>');
    $('#dashboardLowStock').html('<div class="empty-state">Загрузка...</div>');
    const warehouseId = State.get('activeWarehouseId');
    const [orders, products] = await Promise.all([
        Api.listOrders(),
        warehouseId ? Api.listProducts(warehouseId) : []
    ]);

    const revenue = orders.reduce((sum, order) => sum + order.sum, 0);
    $('#metricRevenue').text(`${revenue.toLocaleString('ru-RU')} ₽`);
    $('#metricSales').text(`${orders.length}`);
    const stockCount = products.reduce((sum, item) => sum + item.stock, 0);
    $('#metricStock').text(stockCount);
    $('#metricOps').text(Math.max(orders.length, 4));

    const ordersRows = orders.slice(0, 3).map((order) => ({
        number: `#${order.number}`,
        date: order.date,
        sum: `${order.sum.toLocaleString('ru-RU')} ₽`,
        status: order.status
    }));

    $('#dashboardOrders').html(Table.render({
        columns: [
            { key: 'number', label: 'Номер' },
            { key: 'date', label: 'Дата' },
            { key: 'sum', label: 'Сумма' },
            { key: 'status', label: 'Статус' }
        ],
        rows: ordersRows
    }));

    const lowStock = products.filter((item) => item.stock < 50);
    $('#dashboardLowStock').html(Table.render({
        columns: [
            { key: 'name', label: 'Товар' },
            { key: 'stock', label: 'Остаток' },
            { key: 'price', label: 'Цена' }
        ],
        rows: lowStock.map((item) => ({
            name: item.name,
            stock: `${item.stock} шт.`,
            price: `${item.price} ₽`
        }))
    }));

    $('#quickCreateOrder').on('click', () => {
        Toast.show('Открыта форма нового заказа');
    });
};

$(document).on('warehouseChanged', () => {
    if ($('body').data('page') === 'dashboard') {
        PageHandlers.dashboard();
    }
});
