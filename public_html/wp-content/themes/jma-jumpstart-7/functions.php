<?php

function jma_theme_quicktags()
{
    if (wp_script_is('quicktags')) {
        ?>
		<script language="javascript" type="text/javascript">

		QTags.addButton( 'JMA_h2', 'h2', '<h2>', '</h2>' );
		QTags.addButton( 'JMA_h3', 'h3', '<h3>', '</h3>' );
		QTags.addButton( 'JMA_div', 'div', '<div class="list_lead_in">', '</div>' );
		QTags.addButton( 'JMA_html', 'html', '[html class="wrap"]', '[/html]' );
	    </script>
<?php
    }
}
add_action('admin_print_footer_scripts', 'jma_theme_quicktags');

/*-------------------------------------------------------*/
/* Run Theme Blvd framework (required)
/*-------------------------------------------------------*/
require_once TEMPLATEPATH.'/framework/themeblvd.php';

/* enable to overwrite adjust-logo.js in child theme */
function jma_adjust_theme_scripts()
{
    // Register child theme file before Jump Start to override

    global $jma_spec_options;
    if ($jma_spec_options['child_sticky_menu'] || logo_in_menu()) {
        wp_dequeue_script('adjust_site_js');
        wp_enqueue_script('child_adjust_logo_js', get_stylesheet_directory_uri().'/adjust-logo.js', array('jquery'));
    }
}
add_action('wp_enqueue_scripts', 'jma_adjust_theme_scripts', 30);

function jumpstart_child_option_id($id)
{
    return 'jumpstart';
}
add_filter('themeblvd_option_id', 'jumpstart_child_option_id');

/*$jma_spec_options = jma_get_theme_values();
echo '<pre>';print_r($jma_spec_options);echo '</pre>';*/
$file = get_stylesheet_directory().'/functions-site-specific-special-options.php';

if (file_exists($file)) {
    include $file;
}//pull in file from prev versions if exists

function jma_add_child_scripts()
{
    //wp_enqueue_script( 'child_theme_js', get_stylesheet_directory_uri() . '/theme.js', array('jquery') );*/
    wp_enqueue_style('jma_custom_css', get_stylesheet_directory_uri().'/custom.css', false, '1.0');
}
add_action('wp_enqueue_scripts', 'jma_add_child_scripts', 40);
