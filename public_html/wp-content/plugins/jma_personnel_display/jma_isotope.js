jQuery(window).load(function() {

    /* Put any theme-specific JS here...
	 *
	 *
	 * to start with default class
  $container.isotope({ filter: '.first' });
  $('.filters').find("[data-filter='.first']").addClass('is-checked'); */

    function jma_show(input) {

        jQuery('.jma-column').each(function() {
            $this = jQuery(this);
            if ($this.hasClass(input)) {
                $this.removeClass('jma-hide jma-fade');
            }
        });

    }

    function jma_hide(input) {

        jQuery('.jma-column').each(function() {
            $this = jQuery(this);
            if ($this.hasClass(input)) {
                $this.addClass('jma-fade');
            } else {
                $this.addClass('jma-hide');
            }
        });

    }
    jma_hide();
    jma_show('leadership-term');
    //jQuery('.filters').find("[data-filter='tax-primary']").addClass('is-checked');
    // bind filter button click
    jQuery('#all-buttons').on('click', '.trigger', function() {

        var filterValue = jQuery(this).attr('data-filter');
        jQuery.when(jma_hide(filterValue)).done(function() {
            setTimeout(function() {
                jma_show(filterValue);
            }, 150);
        })
    });

});
jQuery(document).ready(function($) {
    // change is-checked class on buttons
    jQuery('#all-buttons').each(function(i, buttonGroup) {
        var $buttonGroup = jQuery(this);
        $buttonGroup.on('click', '.trigger', function() {
            if ($(this).parents('.taxonomies').length) {
                $buttonGroup.find('.is-checked').removeClass('is-checked');
            } else {
                $buttonGroup.find('.filters').find('.is-checked').removeClass('is-checked');
            }

            jQuery(this).addClass('is-checked');
        });
    });


    jQuery('.taxonomies').each(function(i, parentGroup) {
        var $taxButton = jQuery(this);
        $taxButton.on('click', '.trigger', function() {
            $clicked = jQuery(this);
            if (!$clicked.hasClass('is-checked')) {
                jQuery('.terms').animate({
                    'height': 0,
                    'margin-bottom': 0
                }, 100);
                currentClass = $clicked.data('tax');
                $currentClass = jQuery('.' + currentClass);
                $autoheight = $currentClass.css('height', 'auto').outerHeight();
                $currentClass.animate({
                    'height': $autoheight + 'px',
                    'margin-bottom': '15px'
                }, 300);
            }
        });
    });



});