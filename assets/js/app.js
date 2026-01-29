$(async () => {
    const initTheme = () => {
        const theme = State.get('theme') || 'light';
        if (theme === 'dark') {
            $('body').addClass('theme-dark');
        }
        $('#themeToggle').on('click', () => {
            $('body').toggleClass('theme-dark');
            State.set('theme', $('body').hasClass('theme-dark') ? 'dark' : 'light');
        });
    };

    const initProfileMenu = () => {
        $('#profileTrigger').on('click', () => {
            $('.profile-menu').toggleClass('open');
        });
        $(document).on('click', (event) => {
            if (!$(event.target).closest('.profile-menu').length) {
                $('.profile-menu').removeClass('open');
            }
        });
    };

    const initNavigation = () => {
        const page = $('body').data('page');
        if (page) {
            State.set('lastPage', page);
            $(`.sidebar a[data-page="${page}"]`).addClass('active');
        }
    };

    await Selects.init();
    initTheme();
    initProfileMenu();
    initNavigation();
    Breadcrumbs.render();

    $(document).on('companyChanged warehouseChanged', () => {
        const page = $('body').data('page');
        if (page) {
            $(document).trigger(`page:${page}`);
        }
    });

    if (window.PageHandlers) {
        const page = $('body').data('page');
        PageHandlers[page]?.();
    }
});
