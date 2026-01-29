window.PageHandlers = window.PageHandlers || {};

PageHandlers.warehouses = async () => {
    const render = async () => {
        const companyId = State.get('activeCompanyId');
        if (!companyId) {
            $('#warehousesTable').html('<div class="empty-state">Выберите компанию</div>');
            return;
        }
        $('#warehousesTable').html('<div class="empty-state">Загрузка...</div>');
        const warehouses = await Api.listWarehouses(companyId);
        $('#warehousesTable').html(Table.render({
            columns: [
                { key: 'name', label: 'Название' },
                { key: 'address', label: 'Адрес' },
                { key: 'stock', label: 'Кол-во товаров' },
                { key: 'actions', label: 'Действия' }
            ],
            rows: warehouses.map((item) => ({
                name: item.name,
                address: item.address,
                stock: `${item.stock} шт.`,
                actions: '<button class="btn btn-ghost" data-edit>Редактировать</button>'
            }))
        }));
    };

    const openModal = () => {
        Modal.open({
            title: 'Новый склад',
            content: `
                <label class="field">
                    <span>Название</span>
                    <input id="warehouseName" type="text" placeholder="Склад" />
                </label>
                <label class="field">
                    <span>Адрес</span>
                    <input id="warehouseAddress" type="text" placeholder="Город, улица" />
                </label>
            `,
            onSubmit: async () => {
                await Api.saveWarehouse({
                    companyId: State.get('activeCompanyId'),
                    name: $('#warehouseName').val(),
                    address: $('#warehouseAddress').val(),
                    stock: 0
                });
                Modal.close();
                Toast.show('Склад создан');
                render();
            }
        });
    };

    $('#addWarehouseBtn').on('click', openModal);
    $(document).on('companyChanged', render);
    render();
};
