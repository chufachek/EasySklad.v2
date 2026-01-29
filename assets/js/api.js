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
        categories: [
            { id: 'cat1', companyId: 'c1', name: 'Продукты' },
            { id: 'cat2', companyId: 'c1', name: 'Косметика' },
            { id: 'cat3', companyId: 'c2', name: 'Техника' }
        ],
        products: [
            { id: 'p1', warehouseId: 'w1', categoryId: 'cat1', sku: 'APL-01', name: 'Яблоки Гала', stock: 120, price: 90 },
            { id: 'p2', warehouseId: 'w1', categoryId: 'cat1', sku: 'BAN-02', name: 'Бананы Эквадор', stock: 40, price: 110 },
            { id: 'p3', warehouseId: 'w2', categoryId: 'cat2', sku: 'CRM-12', name: 'Крем для рук', stock: 24, price: 360 },
            { id: 'p4', warehouseId: 'w3', categoryId: 'cat3', sku: 'SHM-07', name: 'Шампунь 300мл', stock: 60, price: 290 }
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
            const parsed = JSON.parse(stored);
            parsed.categories = parsed.categories || [];
            parsed.products = parsed.products || [];
            parsed.warehouses = parsed.warehouses || [];
            parsed.companies = parsed.companies || [];
            parsed.services = parsed.services || [];
            parsed.orders = parsed.orders || [];
            return parsed;
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
        const categories = db.categories || [];
        return respond(db.products.filter((p) => p.warehouseId === warehouseId).map((item) => {
            const category = categories.find((cat) => cat.id === item.categoryId);
            return { ...item, categoryName: category ? category.name : null };
        }));
    };

    const listCategories = (companyId) => {
        const db = loadDb();
        const categories = db.categories.filter((cat) => cat.companyId === companyId);
        const products = db.products;
        return respond(categories.map((category) => ({
            ...category,
            productsCount: products.filter((item) => item.categoryId === category.id).length
        })));
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

    const createCategory = ({ companyId, name }) => {
        const db = loadDb();
        db.categories.push({ id: `cat${Date.now()}`, companyId, name });
        saveDb(db);
        return respond({ success: true });
    };

    const updateCategory = ({ id, name }) => {
        const db = loadDb();
        const existing = db.categories.find((category) => category.id === id);
        if (existing) {
            existing.name = name;
        }
        saveDb(db);
        return respond({ success: true });
    };

    const deleteCategory = ({ id }) => {
        const db = loadDb();
        db.categories = db.categories.filter((category) => category.id !== id);
        db.products = db.products.map((item) => item.categoryId === id ? { ...item, categoryId: null } : item);
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

    const getDashboard = ({ companyId, warehouseId, range }) => request(`/api/dashboard?companyId=${companyId || ''}&warehouseId=${warehouseId || ''}&range=${range || '7d'}`)
        .catch(() => {
            const db = loadDb();
            const days = range === '30d' ? 30 : 7;
            const revenue_series = Array.from({ length: days }).map((_, index) => ({
                date: new Date(Date.now() - (days - index - 1) * 86400000).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' }),
                value: Math.floor(1500 + Math.random() * 5000)
            }));
            const orders = db.orders;
            const products = warehouseId ? db.products.filter((item) => item.warehouseId === warehouseId) : db.products;
            const revenue_total = revenue_series.reduce((sum, item) => sum + item.value, 0);
            const avg_check = orders.length ? Math.round((orders.reduce((sum, order) => sum + order.sum, 0) / orders.length)) : 0;
            const pie_series = [
                { label: 'Товары', value: 68 },
                { label: 'Услуги', value: 32 }
            ];
            return respond({
                revenue_series,
                revenue_total,
                orders_count: orders.length,
                avg_check,
                stock_low: products.filter((item) => item.stock <= 10),
                stock_out: products.filter((item) => item.stock === 0),
                last_orders: orders.slice(0, 6),
                last_ops: [
                    { label: 'Приход', value: 6, time: 'Сегодня' },
                    { label: 'Продажи', value: 12, time: 'Сегодня' },
                    { label: 'Возвраты', value: 1, time: 'Вчера' }
                ],
                pie_series
            });
        });

    return {
        listCompanies,
        listWarehouses,
        listProducts,
        listCategories,
        listServices,
        listOrders,
        saveProduct,
        createCategory,
        updateCategory,
        deleteCategory,
        saveService,
        saveWarehouse,
        saveCompany,
        getMe,
        updateMe,
        getDashboard
    };
})();
