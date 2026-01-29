const Modal = (() => {
    let active = null;
    let lastFocus = null;

    const getFocusable = (container) => container
        .find('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])')
        .filter(':visible');

    const close = () => {
        if (active) {
            const onClose = active.data('onClose');
            active.remove();
            active = null;
            $(document).off('keydown.modal');
            $('body').removeClass('modal-open');
            if (lastFocus) {
                lastFocus.focus();
                lastFocus = null;
            }
            if (onClose) {
                onClose();
            }
        }
    };

    const open = ({
        title = '',
        content = '',
        footer = '',
        size = 'md',
        onSubmit,
        onOpen,
        onClose,
        submitLabel = 'Сохранить',
        cancelLabel = 'Отмена'
    }) => {
        close();
        lastFocus = document.activeElement;
        const sizeClass = size ? `modal--${size}` : '';
        const isFullscreen = size === 'fullscreen';
        const footerHtml = footer || `
            <button class="btn btn-ghost" data-close>${cancelLabel}</button>
            <button class="btn btn-primary" data-submit>${submitLabel}</button>
        `;
        const backdrop = $(`
            <div class="modal-backdrop ${isFullscreen ? 'fullscreen' : ''}" role="presentation">
                <div class="modal ${sizeClass}" role="dialog" aria-modal="true">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button class="btn btn-ghost" data-close aria-label="Закрыть">✕</button>
                    </div>
                    <div class="modal-body">${content}</div>
                    <div class="modal-footer">${footerHtml}</div>
                </div>
            </div>
        `);
        backdrop.data('onClose', onClose || null);
        backdrop.on('click', '[data-close]', close);
        backdrop.on('click', (event) => {
            if (event.target === backdrop[0]) {
                close();
            }
        });
        backdrop.on('click', '[data-submit]', () => {
            if (onSubmit) {
                onSubmit();
            }
        });
        $(document).on('keydown.modal', (event) => {
            if (event.key === 'Escape') {
                close();
                return;
            }
            if (event.key === 'Tab' && active) {
                const focusable = getFocusable(active);
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });

        active = backdrop;
        $('body').addClass('modal-open').append(backdrop);
        const focusable = getFocusable(backdrop);
        if (focusable.length) {
            focusable[0].focus();
        }
        if (onOpen) {
            onOpen(backdrop);
        }
    };

    const confirm = ({ title = 'Подтвердите', message = 'Вы уверены?', confirmLabel = 'Удалить', cancelLabel = 'Отмена', onConfirm }) => {
        open({
            title,
            content: `<p>${message}</p>`,
            size: 'sm',
            footer: `
                <button class="btn btn-ghost" data-close>${cancelLabel}</button>
                <button class="btn btn-primary" data-confirm>${confirmLabel}</button>
            `,
            onOpen: (backdrop) => {
                backdrop.on('click', '[data-confirm]', () => {
                    if (onConfirm) {
                        onConfirm();
                    }
                    close();
                });
            }
        });
    };

    return {
        open,
        close,
        confirm
    };
})();
