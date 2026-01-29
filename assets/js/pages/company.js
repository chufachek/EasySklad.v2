window.PageHandlers = window.PageHandlers || {};

PageHandlers.company = async () => {
    const render = async () => {
        const companyId = State.get('activeCompanyId');
        const companies = await Api.listCompanies();
        const company = companies.find((item) => item.id === companyId) || companies[0];
        if (!company) {
            $('#companyInfo').html('<div class="empty-state">Нет компании</div>');
            return;
        }
        $('#companyInfo').html(`
            <div>
                <span class="label">Название</span>
                <strong>${company.name}</strong>
            </div>
            <div>
                <span class="label">ИНН</span>
                <strong>${company.inn}</strong>
            </div>
            <div>
                <span class="label">Город</span>
                <strong>${company.city}</strong>
            </div>
            <div>
                <span class="label">Тариф</span>
                <strong>${company.tariff}</strong>
            </div>
        `);
        const warehouses = await Api.listWarehouses(company.id);
        $('#companyWarehouses').html(`
            <ul>
                ${warehouses.map((warehouse) => `<li>${warehouse.name} · ${warehouse.address}</li>`).join('')}
            </ul>
        `);
    };

    $('#createWarehouseBtn').on('click', () => {
        Modal.open({
            title: 'Создать склад',
            content: `
                <label class="field">
                    <span>Название</span>
                    <input id="warehouseName" type="text" placeholder="Основной" />
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
    });

    $(document).on('companyChanged', render);
    render();
};
