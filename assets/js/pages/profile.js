window.PageHandlers = window.PageHandlers || {};

PageHandlers.profile = async () => {
    let cachedUser = null;

    const fieldMap = {
        email: 'profileEmail',
        first_name: 'profileFirstName',
        last_name: 'profileLastName',
        username: 'profileUsername'
    };

    const setFieldError = (field, message) => {
        const inputId = fieldMap[field];
        const input = inputId ? $(`#${inputId}`) : $();
        const hint = $(`[data-error-for="${field}"]`);
        input.closest('.field').toggleClass('error', Boolean(message));
        hint.text(message || '');
    };

    const clearErrors = () => {
        Object.keys(fieldMap).forEach((field) => setFieldError(field, ''));
    };

    const fillForm = (user) => {
        $('#profileEmail').val(user.email || '');
        $('#profileFirstName').val(user.first_name || '');
        $('#profileLastName').val(user.last_name || '');
        $('#profileUsername').val(user.username || '');
        $('#profileCardUserId').text(user.id ? `#${user.id}` : '—');
        $('#profileCardTariff').text(user.tariff || 'Free');
        $('#profileCardBalance').text(`${Number(user.balance || 0).toLocaleString('ru-RU')} ₽`);
    };

    const loadUser = async () => {
        try {
            const user = await Api.getMe();
            cachedUser = user;
            fillForm(user);
        } catch (error) {
            Toast.show('Не удалось загрузить профиль', 'error');
        }
    };

    $('#profileForm').on('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        const payload = {
            email: $('#profileEmail').val().trim(),
            first_name: $('#profileFirstName').val().trim(),
            last_name: $('#profileLastName').val().trim(),
            username: $('#profileUsername').val().trim()
        };

        let hasErrors = false;
        if (!payload.email || !/^[^@\\s]+@[^@\\s]+\\.[^@\\s]+$/.test(payload.email)) {
            setFieldError('email', 'Введите корректный email.');
            hasErrors = true;
        }
        if (!payload.first_name) {
            setFieldError('first_name', 'Введите имя.');
            hasErrors = true;
        }
        if (!/^[a-zA-Z0-9_]{3,32}$/.test(payload.username)) {
            setFieldError('username', 'Логин должен быть 3-32 символа (латиница, цифры, underscore).');
            hasErrors = true;
        }

        if (hasErrors) {
            Toast.show('Проверьте корректность заполнения формы', 'error');
            return;
        }

        try {
            const updated = await Api.updateMe(payload);
            cachedUser = updated;
            fillForm(updated);
            Toast.show('Изменения сохранены');
            $(document).trigger('profileUpdated', updated);
        } catch (error) {
            const response = error?.responseJSON;
            if (response?.error?.fields) {
                Object.entries(response.error.fields).forEach(([field, message]) => {
                    setFieldError(field, message);
                });
            }
            Toast.show(response?.error?.message || 'Не удалось сохранить изменения', 'error');
        }
    });

    $('#profileCancel').on('click', () => {
        if (cachedUser) {
            clearErrors();
            fillForm(cachedUser);
        }
    });

    await loadUser();
};
