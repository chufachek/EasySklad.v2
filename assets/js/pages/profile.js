window.PageHandlers = window.PageHandlers || {};

PageHandlers.profile = async () => {
    const companies = await Api.listCompanies();
    const container = $('#profileCompanies');
    if (companies.length === 0) {
        container.html('<div class="empty-state">Компаний пока нет</div>');
        return;
    }
    container.html(`
        <ul>
            ${companies.map((company) => `<li><strong>${company.name}</strong> · ${company.city}</li>`).join('')}
        </ul>
    `);

    $('#createCompanyBtn').on('click', () => {
        Modal.open({
            title: 'Создать компанию',
            content: `
                <label class="field">
                    <span>Название</span>
                    <input id="companyName" type="text" placeholder="ООО" />
                </label>
                <label class="field">
                    <span>ИНН</span>
                    <input id="companyInn" type="text" placeholder="7701234567" />
                </label>
                <label class="field">
                    <span>Город</span>
                    <input id="companyCity" type="text" placeholder="Москва" />
                </label>
            `,
            onSubmit: async () => {
                await Api.saveCompany({
                    name: $('#companyName').val(),
                    inn: $('#companyInn').val(),
                    city: $('#companyCity').val(),
                    tariff: 'Free'
                });
                Modal.close();
                Toast.show('Компания создана');
                location.reload();
            }
        });
    });
};
