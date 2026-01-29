const State = (() => {
    const storageKey = 'easySkladState';
    const defaults = {
        activeCompanyId: null,
        activeWarehouseId: null,
        theme: 'light',
        lastPage: 'dashboard'
    };

    const load = () => {
        const stored = localStorage.getItem(storageKey);
        return stored ? { ...defaults, ...JSON.parse(stored) } : { ...defaults };
    };

    const save = (data) => {
        localStorage.setItem(storageKey, JSON.stringify(data));
    };

    const get = (key) => {
        const data = load();
        return data[key];
    };

    const set = (key, value) => {
        const data = load();
        data[key] = value;
        save(data);
    };

    return {
        get,
        set,
        load
    };
})();
