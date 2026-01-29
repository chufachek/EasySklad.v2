const Breadcrumbs = (() => {
    const pages = {
        dashboard: { label: 'Дашборд' },
        profile: { label: 'Профиль', parents: ['Компания'] },
        company: { label: 'Компания' },
        warehouses: { label: 'Склады', parents: ['Компания'] },
        products: { label: 'Товары', parents: ['Компания'] },
        income: { label: 'Приход', parents: ['Компания'] },
        orders: { label: 'Заказы', parents: ['Компания'] },
        services: { label: 'Услуги', parents: ['Компания'] },
        pos: { label: 'Касса', parents: ['Компания'] }
    };

    const build = async (page) => {
        const companyId = State.get('activeCompanyId');
        const warehouseId = State.get('activeWarehouseId');
        const companies = await Api.listCompanies();
        const company = companies.find((c) => c.id === companyId);
        let warehouse = null;
        if (companyId) {
            const warehouses = await Api.listWarehouses(companyId);
            warehouse = warehouses.find((w) => w.id === warehouseId);
        }

        const items = [
            { label: 'Easy склад', href: '/app/dashboard' }
        ];

        if (company) {
            items.push({ label: company.name, href: '/app/company' });
        }

        const config = pages[page] || { label: '' };
        if (config.parents?.includes('Компания') && !company) {
            items.push({ label: 'Компания', href: '/app/company' });
        }

        if (config.label && !(page === 'company' && company)) {
            items.push({ label: config.label, href: `/app/${page}` });
        }

        if (page === 'warehouses' && warehouse) {
            items.push({ label: `Склад «${warehouse.name}»`, href: '#' });
        }

        if (page === 'products' && warehouse) {
            items.push({ label: `Склад «${warehouse.name}»`, href: '#' });
        }

        return items;
    };

    const render = async () => {
        const page = $('body').data('page');
        const target = $('#breadcrumbs');
        if (!page || target.length === 0) {
            return;
        }
        const items = await build(page);
        const html = items.map((item, index) => {
            const isLast = index === items.length - 1;
            return isLast ? `<span>${item.label}</span>` : `<a href="${item.href}">${item.label}</a>`;
        }).join(' / ');
        target.html(html);
    };

    return { render };
})();
