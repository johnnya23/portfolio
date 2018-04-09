jQuery(document).ready(function($) {

    $window = $(window);

    /* replace the parent theme toggle code with this containing angles instead of plus/minus*/
    $('.collapse').on('show.bs.collapse', function() {

        // Toggle is opening, add "active-trigger" class and
        // change icon to a minus sign.
        $(this).closest('.panel').find('.jma-panel-heading a').addClass('active-trigger').find('.switch-me').removeClass('fa-angle-right').addClass('fa-angle-down');

    });

    $('.collapse').on('hide.bs.collapse', function() {

        // Toggle is closing, remove "active-trigger" class and
        // change icon to a plus sign.
        $(this).closest('.panel').find('.jma-panel-heading a').removeClass('active-trigger').find('.switch-me').removeClass('fa-angle-down').addClass('fa-angle-right');

    });

    function fix_menu() {
        if ($(window).width() > 991) {
            var $menu = $('#branding').find('#access');
            var $menu_top_pos = parseInt($('#wrapper').css("padding-top"));
            $logo_wrap = $menu.find('.site-logo');

            $menu.prevAll().each(function() {
                $menu_top_pos += $(this).height();
            });
            var $admin_bar_height = $('#wpadminbar').length ? $('#wpadminbar').height() : 0;
            var $offset = $window.scrollTop();

            if ($offset > $menu_top_pos &&
                (($(window).height() + $menu.height() + $menu_top_pos + 25) < $('body').height())) {
                $menu.addClass('fix-menu');
                $menu.css({
                    'top': $admin_bar_height + 'px',
                    //'height': $menu.find('ul.sf-menu').find('li.level-1').find('a').outerHeight() + 'px'
                });
                $widget_height = 0;
                $menu_height = $menu.find('.sf-menu').outerHeight();

                $menu.next().css('margin-top', $menu.height() + 'px');
                if ($menu.hasClass('bottom')) {
                    if ($menu.find('.jma-header-right').length) {
                        $widget_height = $menu.find('.jma-header-right').outerHeight(true) + parseInt($menu.find('.jma-header-right').css('top'), 10);
                    }
                    if (!$menu.hasClass('slide-menu')) {
                        $menu.find('.sf-menu').css('margin-top', $widget_height + 'px');
                    } else {
                        $menu.find('.sf-menu').css('margin-top', '');
                    }
                }
                $logo_vert_padding = $logo_wrap.outerHeight() - $logo_wrap.height();
                $logo_wrap.find('img').css({
                    //'height': ($widget_height + $menu_height - $logo_vert_padding) + 'px'
                });
            } else {
                $menu.removeClass('fix-menu');
                $menu.css({
                    'top': '',
                    //'height': ''
                });
                $menu.next().css('margin-top', '');
                $menu.find('.sf-menu').css('margin-top', '');
                $logo_wrap.find('img').css({
                    'height': $logo_wrap.find('img').attr('height') + 'px'
                });
            }
        }
    };

    function header_adjust() {
        var $menu = $('.header-nav');
        var header_width = $('#branding').width();
        var logo_width = $('.logo.header-nav .site-logo').outerWidth();
        var menu_width = $('.logo.header-nav ul.sf-menu').width();
        if ((logo_width + menu_width) > header_width) {
            $menu.addClass('slide-menu');
        } else {
            $menu.removeClass('slide-menu');
        };
    };

    $window.load(function() {
        header_adjust();
        if ($('#access').data('usechildsticky')) {
            fix_menu();
        }
    });
    $window.scroll(function() {
        //do this one immediately
        if ($('#access').data('usechildsticky')) {
            fix_menu();
            header_adjust();
        }
    });

    $window.bind('baseresizeEnd', function() {
        //do something, window hasn't changed size in 500ms
        header_adjust();
    });

    $window.resize(function() {
        if (this.baseresizeTO) clearTimeout(this.baseresizeTO);
        this.baseresizeTO = setTimeout(function() {
            $(this).trigger('baseresizeEnd');
        }, 500);
    });

});