const Modal = (() => {
    let active = null;

    const close = () => {
        if (active) {
            active.remove();
            active = null;
            $(document).off('keydown.modal');
        }
    };

    const open = ({ title, content, onSubmit }) => {
        close();
        const backdrop = $(
            `<div class="modal-backdrop">
                <div class="modal">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button class="btn btn-ghost" data-close>✕</button>
                    </div>
                    <div class="modal-body">${content}</div>
                    <div class="modal-actions">
                        <button class="btn btn-ghost" data-close>Отмена</button>
                        <button class="btn btn-primary" data-submit>Сохранить</button>
                    </div>
                </div>
            </div>`
        );
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
            }
            if (event.key === 'Enter' && $(event.target).is('input')) {
                event.preventDefault();
                if (onSubmit) {
                    onSubmit();
                }
            }
        });
        active = backdrop;
        $('body').append(backdrop);
    };

    return {
        open,
        close
    };
})();
