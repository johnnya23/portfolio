<?php

 function jma_child_logo_padding_adjustment_filter($x)
 {
     return 8;
 }
add_filter('logo_padding_adjustment_filter', 'jma_child_logo_padding_adjustment_filter');


function jma_blog_meta_args($args)
{
    $args['time'] = 'standard';
    return $args;
}
add_filter('themeblvd_blog_meta_args', 'jma_blog_meta_args');
add_filter('themeblvd_search_meta_args', 'jma_blog_meta_args');
add_filter('themeblvd_mini_list_meta_args', 'jma_blog_meta_args');


function jma_personnel_content($content)
{
    global $post;
    $fields = get_fields();
    $post_type = $post->post_type;

    // Get post type taxonomies.
    $taxonomies = get_object_taxonomies($post_type, 'names');
    $terms = wp_get_post_terms($post->ID, $taxonomies);
    foreach ($terms as $term) {
        $terms_by_tax[$term->taxonomy][] = $term->name;
    }
    if ($fields):
    $new .= '<div class="personnel-box">';
    foreach ($fields as $name => $value):
            $new .= '<li><b>'.$name.'</b> '.$value.'</li>';
    endforeach;
    $new .= '</div><!--personnel-box-->';
    endif;

    return $new.$content;
}


function jma_narrow_left_sidebar_layouts($layouts)
{
    $layouts['sidebar_left']['columns'] = array(
        'content'   => 'col-sm-9',
        'left'     => 'col-sm-3'
    );
    return $layouts;
}

function jma_child_jma_header_image_after()
{
    global $post;
    $fields = get_fields();
    if ($fields['header_text_use_page_title'] || $fields['header_text_custom_title'] || $fields['header_text_subtitle']) {
        $title_style = '';
        if ($fields['header_text_title_bg']) {
            $title_style = ' style="background: ' . $fields['header_text_title_bg'] . '"';
        }
        $subtitle_style = $fields['header_text_subtitle_bg']? ' style="background: rgba(0,0,0,0.7)"': '';
        echo '<div class="jma-custom-header-text-wrap">';
        echo '<div class="jma-custom-header-text-inner">';
        if ($fields['header_text_use_page_title']) {
            echo '<h1' . $title_style . '>' . esc_html(get_the_title()) . '</h1>';
        } elseif ($fields['header_text_custom_title']) {
            echo '<h2' . $title_style . '>' . $fields['header_text_custom_title'] . '</h2>';
        }
        if ($fields['header_text_subtitle']) {
            echo '<div' . $subtitle_style . '>' . $fields['header_text_subtitle'] . '</div>';
        }
        if ($fields['header_text_subtitle_2']) {
            echo '<div' . $subtitle_style . '>' . $fields['header_text_subtitle_2'] . '</div>';
        }
        echo '</div><!--jma-custom-header-text-inner-->';
        echo '</div><!--jma-custom-header-text-wrap-->';
    }
}

function jma_child_template_redirect()
{
    if (is_singular('personnel')) {
        add_filter('the_content', 'jma_personnel_content');
    }
    if (is_page(37)) {
        //add_filter('themeblvd_sidebar_layouts', 'jma_narrow_left_sidebar_layouts');
    }
    add_action('jma_header_image_after', 'jma_child_jma_header_image_after');
}
add_action('template_redirect', 'jma_child_template_redirect');

function jma_child_do()
{
    $do = true;
    return $do;
}

function jma_child_search_sc()
{
    add_filter('themeblvd_do_floating_search', 'jma_child_do');
    ob_start();
    echo '<span class="sc-search">' . themeblvd_get_floating_search_trigger($args) . '</span>';
    $x = ob_get_contents();
    ob_end_clean();
    return str_replace("\r\n", '', $x);
}
add_shortcode('search_sc', 'jma_child_search_sc');

/* just custom post types from here down */
function register_post_types()
{
    $labels = array(
        'name' => _x('Personnel', 'themeblvd'),
        'singular_name' => _x('Personnel', 'themeblvd'),
        'add_new' => _x('Add New', 'themeblvd'),
        'add_new_item' => _x('Add New Personnel', 'themeblvd'),
        'edit_item' => _x('Edit Personnel', 'themeblvd'),
        'new_item' => _x('New Personnel', 'themeblvd'),
        'view_item' => _x('View Personnel', 'themeblvd'),
        'search_items' => _x('Search Personnel', 'themeblvd'),
        'not_found' => _x('No personnel found', 'themeblvd'),
        'not_found_in_trash' => _x('No personnel found in Trash', 'themeblvd'),
        'parent_item_colon' => _x('Parent Personnel:', 'themeblvd'),
        'menu_name' => _x('Personnel', 'themeblvd'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'custom-fields', 'thumbnail'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'personnel'),
        'capability_type' => 'post',
    );

    register_post_type('personnel', $args);
}

function register_custom_taxonomies()
{
    $labels = array(
    'name' => _x('Leadership', 'themeblvd'),
    'singular_name' => _x('Leadership', 'themeblvd'),
    'search_items' => _x('Search Leadership', 'themeblvd'),
    'popular_items' => _x('Popular Leadership', 'themeblvd'),
    'all_items' => _x('All Leadership', 'themeblvd'),
    'parent_item' => _x('Parent Leadership', 'themeblvd'),
    'parent_item_colon' => _x('Parent Leadership:', 'themeblvd'),
    'edit_item' => _x('Edit Leadership', 'themeblvd'),
    'update_item' => _x('Update Leadership', 'themeblvd'),
    'add_new_item' => _x('Add New Leadership', 'themeblvd'),
    'new_item_name' => _x('New Leadership', 'themeblvd'),
    'add_or_remove_items' => _x('Add or remove Leadership', 'themeblvd'),
    'choose_from_most_used' => _x('Choose from most used Leadership', 'themeblvd'),
    'menu_name' => _x('Leadership', 'themeblvd'),
);

    $args = array(
    'labels' => $labels,
    'public' => true,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'show_tagcloud' => true,
    'hierarchical' => true,
    'rewrite' => array('slug' => 'leadership'),
    'query_var' => 'leadership',
);
    register_taxonomy('leadership', array('personnel'), $args);

    $labels = array(
    'name' => _x('Function Name', 'themeblvd'),
    'singular_name' => _x('Function', 'themeblvd'),
    'search_items' => _x('Search Functions', 'themeblvd'),
    'popular_items' => _x('Popular Functions', 'themeblvd'),
    'all_items' => _x('All Functions', 'themeblvd'),
    'parent_item' => _x('Parent Function', 'themeblvd'),
    'parent_item_colon' => _x('Parent Function:', 'themeblvd'),
    'edit_item' => _x('Edit Function', 'themeblvd'),
    'update_item' => _x('Update Function', 'themeblvd'),
    'add_new_item' => _x('Add New Function', 'themeblvd'),
    'new_item_name' => _x('New Function', 'themeblvd'),
    'add_or_remove_items' => _x('Add or remove functions', 'themeblvd'),
    'choose_from_most_used' => _x('Choose from most used functions', 'themeblvd'),
    'menu_name' => _x('Functions', 'themeblvd'),
);

    $args = array(
    'labels' => $labels,
    'public' => true,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'show_tagcloud' => true,
    'hierarchical' => true,
    'rewrite' => array('slug' => 'roles'),
    'query_var' => 'role',
);
    register_taxonomy('role', array('personnel'), $args);

    $labels = array(
    'name' => _x('Strategy', 'themeblvd'),
    'singular_name' => _x('Strategy', 'themeblvd'),
    'search_items' => _x('Search Strategy', 'themeblvd'),
    'popular_items' => _x('Popular Strategy', 'themeblvd'),
    'all_items' => _x('All Strategy', 'themeblvd'),
    'parent_item' => _x('Parent Strategy', 'themeblvd'),
    'parent_item_colon' => _x('Parent Strategy:', 'themeblvd'),
    'edit_item' => _x('Edit Strategy', 'themeblvd'),
    'update_item' => _x('Update Strategy', 'themeblvd'),
    'add_new_item' => _x('Add New Strategy', 'themeblvd'),
    'new_item_name' => _x('New Strategy', 'themeblvd'),
    'add_or_remove_items' => _x('Add or remove Strategy', 'themeblvd'),
    'choose_from_most_used' => _x('Choose from most used Strategy', 'themeblvd'),
    'menu_name' => _x('Strategy', 'themeblvd'),
);

    $args = array(
    'labels' => $labels,
    'public' => true,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'show_tagcloud' => true,
    'hierarchical' => true,
    'rewrite' => array('slug' => 'foci'),
    'query_var' => 'focus',
);
    register_taxonomy('focus', array('personnel', 'portfolio_item'), $args);


    $labels = array(
    'name' => _x('Titles', 'themeblvd'),
    'singular_name' => _x('Title', 'themeblvd'),
    'search_items' => _x('Search Titles', 'themeblvd'),
    'popular_items' => _x('Popular Titles', 'themeblvd'),
    'all_items' => _x('All Titles', 'themeblvd'),
    'parent_item' => _x('Parent Title', 'themeblvd'),
    'parent_item_colon' => _x('Parent Title:', 'themeblvd'),
    'edit_item' => _x('Edit Title', 'themeblvd'),
    'update_item' => _x('Update Title', 'themeblvd'),
    'add_new_item' => _x('Add New Title', 'themeblvd'),
    'new_item_name' => _x('New Title', 'themeblvd'),
    'add_or_remove_items' => _x('Add or remove Titles', 'themeblvd'),
    'choose_from_most_used' => _x('Choose from most used Titles', 'themeblvd'),
    'menu_name' => _x('Titles', 'themeblvd'),
);

    $args = array(
    'labels' => $labels,
    'public' => true,
    'show_in_nav_menus' => true,
    'show_ui' => true,
    'show_tagcloud' => true,
    'hierarchical' => true,
    'rewrite' => array('slug' => 'titles'),
    'query_var' => 'title',
);
    register_taxonomy('title', array('personnel'), $args);


    $labels = array(
        'name' => _x('Locations', 'themeblvd'),
        'singular_name' => _x('Location', 'themeblvd'),
        'search_items' => _x('Search Locations', 'themeblvd'),
        'popular_items' => _x('Popular Locations', 'themeblvd'),
        'all_items' => _x('All Locations', 'themeblvd'),
        'parent_item' => _x('Parent Location', 'themeblvd'),
        'parent_item_colon' => _x('Parent Location:', 'themeblvd'),
        'edit_item' => _x('Edit Location', 'themeblvd'),
        'update_item' => _x('Update Location', 'themeblvd'),
        'add_new_item' => _x('Add New Location', 'themeblvd'),
        'new_item_name' => _x('New Location', 'themeblvd'),
        'add_or_remove_items' => _x('Add or remove Locations', 'themeblvd'),
        'choose_from_most_used' => _x('Choose from most used Locations', 'themeblvd'),
        'menu_name' => _x('Locations', 'themeblvd'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'locations'),
        'query_var' => 'location',
        'sort' => true
    );

    register_taxonomy('location', array('personnel'), $args);
}
function my_cpt_init()
{
    register_post_types();
    register_custom_taxonomies();
}
add_action('init', 'my_cpt_init');

function my_rewrite_flush()
{
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'my_rewrite_flush');
