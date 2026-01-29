const Autocomplete = (() => {
    const bind = ({ input, list, onSelect }) => {
        const inputEl = $(input);
        const listEl = $(list);

        const render = (items) => {
            if (!items.length) {
                listEl.removeClass('open');
                return;
            }
            listEl.html(items.map((item) => `<div data-id="${item.id}">${item.label}</div>`).join(''));
            listEl.addClass('open');
        };

        inputEl.on('input', () => {
            const query = inputEl.val().toString().toLowerCase();
            const items = list.filter((item) => item.label.toLowerCase().includes(query));
            render(items);
        });

        listEl.on('click', 'div', (event) => {
            const id = $(event.target).data('id');
            const item = list.find((entry) => entry.id === id);
            if (item) {
                onSelect(item);
                listEl.removeClass('open');
                inputEl.val('');
            }
        });

        inputEl.on('focus', () => render(list));
        $(document).on('click', (event) => {
            if (!$(event.target).closest(listEl).length && !$(event.target).is(inputEl)) {
                listEl.removeClass('open');
            }
        });
    };

    return { bind };
})();
