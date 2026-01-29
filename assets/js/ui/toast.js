const Toast = (() => {
    const show = (message, type = 'success') => {
        const toast = $(
            `<div class="toast">
                <strong>${type === 'success' ? 'Готово' : 'Внимание'}</strong>
                <span>${message}</span>
            </div>`
        );
        $('#toast-root').append(toast);
        setTimeout(() => toast.fadeOut(200, () => toast.remove()), 2500);
    };

    return { show };
})();
