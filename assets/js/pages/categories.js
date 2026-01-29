window.PageHandlers = window.PageHandlers || {};

PageHandlers.categories = () => {
    const render = async () => {
        const companyId = State.get('activeCompanyId');
        if (!companyId) {
            $('#categoriesTable').html('<div class="empty-state">Выберите компанию</div>');
            return;
        }
        $('#categoriesTable').html('<div class="empty-state">Загрузка...</div>');
        const categories = await Api.listCategories(companyId);

        $('#categoriesTable').html(Table.render({
            columns: [
                { key: 'name', label: 'Название' },
                { key: 'count', label: 'Товаров' },
                { key: 'actions', label: 'Действия' }
            ],
            rows: categories.map((category) => ({
                name: category.name,
                count: category.productsCount ?? category.products_count ?? 0,
                actions: `
                    <div class="table-actions">
                        <button class="btn btn-ghost" data-edit="${category.id}">Редактировать</button>
                        <button class="btn btn-ghost" data-delete="${category.id}">Удалить</button>
                    </div>
                `
            }))
        }));
    };

    const openModal = (category = {}) => {
        Modal.open({
            title: category.id ? 'Редактировать категорию' : 'Создать категорию',
            content: `
                <label class="field">
                    <span>Название</span>
                    <input id="categoryName" type="text" value="${category.name || ''}" />
                </label>
            `,
            onSubmit: async () => {
                const name = $('#categoryName').val();
                if (category.id) {
                    await Api.updateCategory({ id: category.id, name });
                } else {
                    await Api.createCategory({ companyId: State.get('activeCompanyId'), name });
                }
                Modal.close();
                Toast.show('Категория сохранена');
                render();
            }
        });
    };

    $('#addCategoryBtn').on('click', () => openModal());

    $('#categoriesTable').on('click', '[data-edit]', async (event) => {
        const companyId = State.get('activeCompanyId');
        const categories = await Api.listCategories(companyId);
        const category = categories.find((item) => item.id === $(event.target).data('edit'));
        openModal(category);
    });

    $('#categoriesTable').on('click', '[data-delete]', async (event) => {
        const id = $(event.target).data('delete');
        Modal.confirm({
            title: 'Удалить категорию',
            message: 'Категория будет удалена, товары останутся без категории.',
            confirmLabel: 'Удалить',
            onConfirm: async () => {
                await Api.deleteCategory({ id });
                Toast.show('Категория удалена');
                render();
            }
        });
    });

    $(document).on('companyChanged', render);
    render();
};
