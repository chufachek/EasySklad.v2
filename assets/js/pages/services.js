window.PageHandlers = window.PageHandlers || {};

PageHandlers.services = () => {
    const render = async () => {
        $('#servicesTable').html('<div class="empty-state">Загрузка...</div>');
        const services = await Api.listServices();
        $('#servicesTable').html(Table.render({
            columns: [
                { key: 'name', label: 'Услуга' },
                { key: 'price', label: 'Цена' },
                { key: 'actions', label: 'Действия' }
            ],
            rows: services.map((item) => ({
                name: item.name,
                price: `${item.price} ₽`,
                actions: `<button class="btn btn-ghost" data-edit="${item.id}">Редактировать</button>`
            }))
        }));
    };

    const openModal = (service = {}) => {
        Modal.open({
            title: service.id ? 'Редактировать услугу' : 'Новая услуга',
            content: `
                <label class="field">
                    <span>Название</span>
                    <input id="serviceName" type="text" value="${service.name || ''}" />
                </label>
                <label class="field">
                    <span>Цена</span>
                    <input id="servicePrice" type="number" value="${service.price || 0}" />
                </label>
            `,
            onSubmit: async () => {
                await Api.saveService({
                    id: service.id,
                    name: $('#serviceName').val(),
                    price: Number($('#servicePrice').val())
                });
                Modal.close();
                Toast.show('Услуга сохранена');
                render();
            }
        });
    };

    $('#servicesTable').on('click', '[data-edit]', async (event) => {
        const services = await Api.listServices();
        const service = services.find((item) => item.id === $(event.target).data('edit'));
        openModal(service);
    });

    $('#addServiceBtn').on('click', () => openModal());
    render();
};
