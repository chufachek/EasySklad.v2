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

    const initSidebar = () => {
        const applyCollapsed = (collapsed) => {
            $('body').toggleClass('sidebar-collapsed', collapsed);
            State.set('sidebarCollapsed', collapsed);
        };

        applyCollapsed(Boolean(State.get('sidebarCollapsed')));

        $('#sidebarToggle').on('click', () => {
            applyCollapsed(!$('body').hasClass('sidebar-collapsed'));
        });

        $('#sidebarMobileToggle').on('click', () => {
            $('body').addClass('sidebar-open');
        });

        $('#sidebarClose, #sidebarOverlay').on('click', () => {
            $('body').removeClass('sidebar-open');
        });
    };

    const applyProfileData = (user) => {
        if (!user) return;
        const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim() || user.username || user.email;
        const initials = (user.first_name?.[0] || user.username?.[0] || 'U').toUpperCase();
        $('#profileName').text(user.first_name || user.username || 'Пользователь');
        $('#profileFullName').text(fullName);
        $('#profileUserId').text(`#${user.id}`);
        $('#profileTariff').text(user.tariff || 'Free');
        $('#profileBalance').text(`${Number(user.balance || 0).toLocaleString('ru-RU')} ₽`);
        $('.avatar').text(initials);
    };

    const initProfileData = async () => {
        try {
            const user = await Api.getMe();
            applyProfileData(user);
        } catch (error) {
            // ignore if not authenticated
        }
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
    initSidebar();
    initNavigation();
    initProfileData();
    Breadcrumbs.render();

    $(document).on('profileUpdated', (_, user) => {
        applyProfileData(user);
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }

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
