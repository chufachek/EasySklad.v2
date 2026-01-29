window.PageHandlers = window.PageHandlers || {};

PageHandlers.orders = async () => {
    const renderDetails = (order) => {
        if (!order) {
            $('#orderDetails').html('<div class="empty-state">Выберите заказ</div>');
            $('#orderStatus').text('—');
            return;
        }
        $('#orderStatus').text(order.status);
        $('#orderDetails').html(`
            <div class="info-list">
                <div>
                    <span class="label">Номер</span>
                    <strong>#${order.number}</strong>
                </div>
                <div>
                    <span class="label">Дата</span>
                    <strong>${order.date}</strong>
                </div>
                <div>
                    <span class="label">Сумма</span>
                    <strong>${order.sum.toLocaleString('ru-RU')} ₽</strong>
                </div>
                <div>
                    <span class="label">Позиции</span>
                    <strong>${order.items.join(', ')}</strong>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary">Оплатить</button>
                <button class="btn btn-ghost">Сохранить</button>
            </div>
        `);
    };

    const render = async () => {
        $('#ordersTable').html('<div class="empty-state">Загрузка...</div>');
        const orders = await Api.listOrders();
        $('#ordersTable').html(Table.render({
            columns: [
                { key: 'number', label: 'Номер' },
                { key: 'date', label: 'Дата' },
                { key: 'sum', label: 'Сумма' },
                { key: 'status', label: 'Статус' }
            ],
            rows: orders.map((order) => ({
                number: `<button class="btn btn-ghost" data-order="${order.id}">#${order.number}</button>`,
                date: order.date,
                sum: `${order.sum.toLocaleString('ru-RU')} ₽`,
                status: order.status
            }))
        }));
        renderDetails(orders[0]);
    };

    $('#ordersTable').on('click', '[data-order]', async (event) => {
        const orders = await Api.listOrders();
        const order = orders.find((item) => item.id === $(event.target).data('order'));
        renderDetails(order);
    });

    $('#createOrderBtn').on('click', () => Toast.show('Заказ создан в черновике'));
    render();
};
