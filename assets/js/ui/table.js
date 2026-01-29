const Table = (() => {
    const render = ({ columns, rows }) => {
        if (!rows || rows.length === 0) {
            return '<div class="empty-state">Данных пока нет</div>';
        }
        const head = columns.map((col) => `<th>${col.label}</th>`).join('');
        const body = rows.map((row) => {
            const cols = columns.map((col) => `<td>${row[col.key] ?? ''}</td>`).join('');
            return `<tr>${cols}</tr>`;
        }).join('');
        return `<table class="table"><thead><tr>${head}</tr></thead><tbody>${body}</tbody></table>`;
    };

    return { render };
})();
