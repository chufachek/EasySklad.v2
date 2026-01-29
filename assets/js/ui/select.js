const Selects = (() => {
    const initCompanySelect = async () => {
        const companies = await Api.listCompanies();
        const select = $('#companySelect');
        if (select.length === 0) return;
        select.empty();
        companies.forEach((company) => {
            select.append(`<option value="${company.id}">${company.name}</option>`);
        });
        const active = State.get('activeCompanyId') || companies[0]?.id || '';
        select.val(active);
        State.set('activeCompanyId', active);
    };

    const initWarehouseSelect = async () => {
        const companyId = State.get('activeCompanyId');
        const select = $('#warehouseSelect');
        if (select.length === 0) return;
        select.empty();
        if (!companyId) {
            select.append('<option value="">Нет складов</option>');
            return;
        }
        const warehouses = await Api.listWarehouses(companyId);
        if (warehouses.length === 0) {
            select.append('<option value="">Нет складов</option>');
            State.set('activeWarehouseId', null);
            return;
        }
        warehouses.forEach((warehouse) => {
            select.append(`<option value="${warehouse.id}">${warehouse.name}</option>`);
        });
        const activeWarehouse = State.get('activeWarehouseId') || warehouses[0].id;
        select.val(activeWarehouse);
        State.set('activeWarehouseId', activeWarehouse);
    };

    const bind = () => {
        $('#companySelect').on('change', async (event) => {
            State.set('activeCompanyId', event.target.value);
            State.set('activeWarehouseId', null);
            await initWarehouseSelect();
            Breadcrumbs.render();
            $(document).trigger('companyChanged');
        });

        $('#warehouseSelect').on('change', (event) => {
            State.set('activeWarehouseId', event.target.value);
            Breadcrumbs.render();
            $(document).trigger('warehouseChanged');
        });
    };

    const init = async () => {
        await initCompanySelect();
        await initWarehouseSelect();
        bind();
    };

    return { init };
})();
