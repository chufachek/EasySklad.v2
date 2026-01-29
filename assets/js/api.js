window.__MOCK__ = true;

const Api = (() => {
    const storageKey = 'easySkladMockDb';
    const delay = 200;
    const apiBase = '';

    const seed = {
        companies: [
            { id: 'c1', name: 'ООО «Зелёный Маркет»', inn: '7701234567', city: 'Москва', tariff: 'Free' },
            { id: 'c2', name: 'ИП «Тёплый Дом»', inn: '7845123456', city: 'Санкт-Петербург', tariff: 'Free' }
        ],
        warehouses: [
            { id: 'w1', companyId: 'c1', name: 'Основной склад', address: 'Москва, Ленина 10', stock: 420 },
            { id: 'w2', companyId: 'c1', name: 'Точка на Павелецкой', address: 'Москва, Павелецкая 5', stock: 180 },
            { id: 'w3', companyId: 'c2', name: 'Северный', address: 'СПб, Невский 80', stock: 260 }
        ],
        products: [
            { id: 'p1', warehouseId: 'w1', sku: 'APL-01', name: 'Яблоки Гала', stock: 120, price: 90 },
            { id: 'p2', warehouseId: 'w1', sku: 'BAN-02', name: 'Бананы Эквадор', stock: 40, price: 110 },
            { id: 'p3', warehouseId: 'w2', sku: 'CRM-12', name: 'Крем для рук', stock: 24, price: 360 },
            { id: 'p4', warehouseId: 'w3', sku: 'SHM-07', name: 'Шампунь 300мл', stock: 60, price: 290 }
        ],
        services: [
            { id: 's1', name: 'Доставка по городу', price: 350 },
            { id: 's2', name: 'Сборка заказа', price: 150 }
        ],
        orders: [
            { id: 'o1', number: '0001', date: '2024-03-12', sum: 3400, status: 'Оплачен', items: ['Яблоки Гала', 'Доставка по городу'] },
            { id: 'o2', number: '0002', date: '2024-03-13', sum: 1200, status: 'В работе', items: ['Крем для рук'] },
            { id: 'o3', number: '0003', date: '2024-03-14', sum: 890, status: 'Новый', items: ['Бананы Эквадор'] }
        ],
        me: {
            id: 1,
            email: 'test@example.com',
            first_name: 'Анна',
            last_name: 'Смирнова',
            username: 'anna_sklad',
            tariff: 'Free',
            balance: 0
        }
    };

    const loadDb = () => {
        const stored = localStorage.getItem(storageKey);
        if (stored) {
            return JSON.parse(stored);
        }
        localStorage.setItem(storageKey, JSON.stringify(seed));
        return { ...seed };
    };

    const saveDb = (db) => {
        localStorage.setItem(storageKey, JSON.stringify(db));
    };

    const respond = (data) => $.Deferred((defer) => {
        setTimeout(() => defer.resolve(data), delay);
    }).promise();

    const listCompanies = () => respond(loadDb().companies);

    const listWarehouses = (companyId) => {
        const db = loadDb();
        return respond(db.warehouses.filter((w) => w.companyId === companyId));
    };

    const listProducts = (warehouseId) => {
        const db = loadDb();
        return respond(db.products.filter((p) => p.warehouseId === warehouseId));
    };

    const listServices = () => respond(loadDb().services);

    const listOrders = () => respond(loadDb().orders);

    const saveProduct = (payload) => {
        const db = loadDb();
        const existing = db.products.find((item) => item.id === payload.id);
        if (existing) {
            Object.assign(existing, payload);
        } else {
            db.products.push({ ...payload, id: `p${Date.now()}` });
        }
        saveDb(db);
        return respond({ success: true });
    };

    const saveService = (payload) => {
        const db = loadDb();
        const existing = db.services.find((item) => item.id === payload.id);
        if (existing) {
            Object.assign(existing, payload);
        } else {
            db.services.push({ ...payload, id: `s${Date.now()}` });
        }
        saveDb(db);
        return respond({ success: true });
    };

    const saveWarehouse = (payload) => {
        const db = loadDb();
        const existing = db.warehouses.find((item) => item.id === payload.id);
        if (existing) {
            Object.assign(existing, payload);
        } else {
            db.warehouses.push({ ...payload, id: `w${Date.now()}` });
        }
        saveDb(db);
        return respond({ success: true });
    };

    const saveCompany = (payload) => {
        const db = loadDb();
        const existing = db.companies.find((item) => item.id === payload.id);
        if (existing) {
            Object.assign(existing, payload);
        } else {
            db.companies.push({ ...payload, id: `c${Date.now()}` });
        }
        saveDb(db);
        return respond({ success: true });
    };

    const request = (url, options = {}) => $.ajax({
        url: `${apiBase}${url}`,
        method: options.method || 'GET',
        data: options.data ? JSON.stringify(options.data) : undefined,
        contentType: options.data ? 'application/json' : undefined,
        dataType: 'json'
    }).then((response) => response.data);

    const getMe = () => request('/api/me').catch(() => respond(loadDb().me));

    const updateMe = (payload) => request('/api/me', { method: 'PUT', data: payload })
        .catch(() => {
            const db = loadDb();
            db.me = { ...db.me, ...payload };
            saveDb(db);
            return respond(db.me);
        });

    return {
        listCompanies,
        listWarehouses,
        listProducts,
        listServices,
        listOrders,
        saveProduct,
        saveService,
        saveWarehouse,
        saveCompany,
        getMe,
        updateMe
    };
})();
