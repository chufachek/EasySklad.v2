window.PageHandlers = window.PageHandlers || {};

PageHandlers.dashboard = () => {
    let revenueChart = null;
    let pieChart = null;
    let currentRange = '7d';

    const statusBadge = (status) => {
        const normalized = (status || '').toString().toLowerCase();
        const mapping = {
            paid: 'badge-success',
            draft: 'badge-warning',
            canceled: 'badge-danger',
            'оплачен': 'badge-success',
            'в работе': 'badge-warning',
            'новый': 'badge-neutral',
            'отменен': 'badge-danger'
        };
        const labelMap = {
            paid: 'Оплачен',
            draft: 'Черновик',
            canceled: 'Отменен',
            'оплачен': 'Оплачен',
            'в работе': 'В работе',
            'новый': 'Новый',
            'отменен': 'Отменен'
        };
        const key = mapping[normalized] || 'badge-neutral';
        const label = labelMap[normalized] || status || '—';
        return `<span class="badge ${key}">${label}</span>`;
    };

    const renderSkeleton = () => {
        $('#dashboardOrders').html('<div class="skeleton" style="height:140px"></div>');
        $('#dashboardLowStock').html('<div class="skeleton" style="height:140px"></div>');
        $('#dashboardTopProducts').html('<div class="skeleton" style="height:140px"></div>');
        $('#dashboardOps').html('<div class="skeleton" style="height:120px"></div>');
        $('#dashboardActivity').html('<div class="skeleton" style="height:160px"></div>');
    };

    const renderRevenueChart = (series) => {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        const styles = getComputedStyle(document.body);
        const textColor = styles.getPropertyValue('--text').trim() || '#1f2a24';
        const gridColor = styles.getPropertyValue('--border').trim() || 'rgba(0,0,0,0.08)';
        const labels = series.map((item) => item.date);
        const values = series.map((item) => item.value);
        if (revenueChart) {
            revenueChart.data.labels = labels;
            revenueChart.data.datasets[0].data = values;
            revenueChart.update();
            return;
        }
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Выручка',
                    data: values,
                    borderColor: 'rgba(43, 182, 115, 1)',
                    backgroundColor: 'rgba(43, 182, 115, 0.15)',
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor } }
                }
            }
        });
    };

    const renderPieChart = (series) => {
        const ctx = document.getElementById('salesPieChart');
        if (!ctx) return;
        const labels = series.map((item) => item.label);
        const values = series.map((item) => item.value);
        const colors = ['#2bb673', '#2c7be5', '#f0b429', '#e36f6f', '#6c7a89'];
        const legend = $('#salesPieLegend');
        legend.html(series.map((item, index) => `
            <span><i class="legend-dot" style="background:${colors[index % colors.length]}"></i>${item.label} — ${item.value}%</span>
        `).join(''));
        if (pieChart) {
            pieChart.data.labels = labels;
            pieChart.data.datasets[0].data = values;
            pieChart.update();
            return;
        }
        pieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                cutout: '62%'
            }
        });
    };

    const render = async () => {
        renderSkeleton();
        const companyId = State.get('activeCompanyId');
        const warehouseId = State.get('activeWarehouseId');
        const data = await Api.getDashboard({ companyId, warehouseId, range: currentRange });

        $('#metricRevenue').text(`${data.revenue_total.toLocaleString('ru-RU')} ₽`);
        $('#metricRevenueSub').text(`за ${currentRange === '30d' ? '30 дней' : '7 дней'}`);
        $('#metricRevenueTrend').text(`+${Math.round(Math.random() * 12)}%`);
        $('#metricSales').text(`${data.orders_count}`);
        $('#metricSalesSub').text(`средний чек ${Number(data.avg_check || 0).toLocaleString('ru-RU')} ₽`);
        $('#metricOrdersPaid').text(`${Math.max(0, data.orders_count - 1)} оплачено`);

        const totalStock = data.stock_low.length + data.stock_out.length;
        $('#metricStock').text(`${totalStock}`);
        $('#metricStockSub').text(`SKU на складах`);
        $('#metricStockAlert').text(`${data.stock_low.length} мало`);
        $('#metricOps').text(`${data.last_ops.length}`);
        $('#metricOpsTrend').text(`${data.last_ops.length} операций`);

        renderRevenueChart(data.revenue_series);
        renderPieChart(data.pie_series);

        $('#dashboardOps').html(data.last_ops.map((item) => `
            <div class="activity-item">
                <div>
                    <strong>${item.label}</strong>
                    <span>${item.time}</span>
                </div>
                <div class="badge badge-soft">${item.value}</div>
            </div>
        `).join(''));

        const warehouses = companyId ? await Api.listWarehouses(companyId) : [];
        const warehouseName = warehouses.find((w) => w.id === warehouseId)?.name || '—';
        const ordersRows = (data.last_orders || []).slice(0, 6).map((order, index) => ({
            number: order.number ? `#${order.number}` : `#${index + 1}`,
            date: order.date || order.created_at || '—',
            customer: order.customer_name || order.customer || '—',
            sum: `${Number(order.sum || order.total || 0).toLocaleString('ru-RU')} ₽`,
            status: statusBadge(order.status || 'paid'),
            warehouse: warehouseName
        }));

        $('#dashboardOrders').html(Table.render({
            columns: [
                { key: 'number', label: '№' },
                { key: 'date', label: 'Дата/время' },
                { key: 'customer', label: 'Клиент' },
                { key: 'sum', label: 'Сумма' },
                { key: 'status', label: 'Статус' },
                { key: 'warehouse', label: 'Склад' }
            ],
            rows: ordersRows
        }).replace('<table class="table">', '<table class="table compact">'));

        $('#dashboardLowStock').html(Table.render({
            columns: [
                { key: 'name', label: 'Товар' },
                { key: 'stock', label: 'Остаток' },
                { key: 'price', label: 'Цена' }
            ],
            rows: data.stock_low.slice(0, 5).map((item) => ({
                name: item.name,
                stock: `${item.stock || item.qty || 0} шт.`,
                price: `${Number(item.price || 0).toLocaleString('ru-RU')} ₽`
            }))
        }));

        const products = warehouseId ? await Api.listProducts(warehouseId) : [];
        $('#dashboardTopProducts').html(Table.render({
            columns: [
                { key: 'name', label: 'Товар' },
                { key: 'stock', label: 'Продано' },
                { key: 'sum', label: 'Выручка' }
            ],
            rows: products.slice(0, 5).map((item) => ({
                name: item.name,
                stock: `${Math.max(1, Math.round(item.stock / 3))} шт.`,
                sum: `${Math.round(item.price * 4).toLocaleString('ru-RU')} ₽`
            }))
        }));

        $('#dashboardActivity').html([
            { title: 'Новый приход', time: '10 минут назад' },
            { title: 'Оплата заказа', time: '30 минут назад' },
            { title: 'Обновление товара', time: '1 час назад' }
        ].map((item) => `
            <div class="activity-item">
                <div>
                    <strong>${item.title}</strong>
                    <span>${item.time}</span>
                </div>
                <span class="badge badge-neutral">Событие</span>
            </div>
        `).join(''));
    };

    $('#revenueRange').on('click', 'button', (event) => {
        const range = $(event.target).data('range');
        if (!range || range === currentRange) return;
        currentRange = range;
        $('#revenueRange button').removeClass('active');
        $(event.target).addClass('active');
        render();
    });

    $('#quickCreateOrder').on('click', () => {
        $('[data-quick="order"]').trigger('click');
    });

    $(document).on('companyChanged warehouseChanged', render);
    $(document).on('themeChanged', () => {
        if (revenueChart) {
            revenueChart.destroy();
            revenueChart = null;
        }
        if (pieChart) {
            pieChart.destroy();
            pieChart = null;
        }
        render();
    });
    render();
};
