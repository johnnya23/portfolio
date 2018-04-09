<?php

/*
Plugin Name: JMA Custom Post Type Display
Description: Adds isotope animation and filtering to a  Custom Post Type display using shortcode
Version: 1.0
Author: John Antonacci
License: GPL2
*/


if (!function_exists('jma_personnel_detect_shortcode')) {
    function jma_personnel_detect_shortcode($needle = '', $post_item = 0)
    {
        if ($post_item) {
            if (is_object($post_item)) {
                $post = $post_item;
            } else {
                $post = get_post($post_item);
            }
        } else {
            global $post; /*  add comment*/
        }
        if (is_array($needle)) {
            $pattern = get_shortcode_regex($needle);
        } elseif (is_string($needle)) {
            $pattern = get_shortcode_regex(array($needle));
        } else {
            $pattern = get_shortcode_regex();
        }
        preg_match_all('/'.$pattern.'/s', $post->post_content, $matches);

        if (//if shortcode(s) to be searched for were passed and not found $return false
            array_key_exists(2, $matches) &&
            count($matches[2])
        ) {
            $return = $matches;
        } else {
            $return = false;
        }

        return $return;
    }
}

if (!function_exists('jmaStartsWith')) {
    function jmaStartsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

function jma_product_isotope_new()
{
    wp_enqueue_style('jma_product_isotope', plugins_url('/jma_isotope.css', __FILE__));
    if (jma_personnel_detect_shortcode('post_type_grid')) {
        //wp_enqueue_script('jma_isotope', plugins_url('/isotope.pkgd.min.js', __FILE__), array('jquery'));
        wp_enqueue_script('jma_isotope_js', plugins_url('/jma_isotope.js', __FILE__), array('jquery'));
    }
}

add_action('wp_enqueue_scripts', 'jma_product_isotope_new');

function jma_product_image_sizes_new($sizes)
{
    // image size for header slider
    $sizes['jma-personnel-grid']['name'] = 'Personnel Grid';
    $sizes['jma-personnel-grid']['width'] = 300;
    $sizes['jma-personnel-grid']['height'] = 300;
    $sizes['jma-personnel-grid']['crop'] = true;

    return $sizes;
}
add_filter('themeblvd_image_sizes', 'jma_product_image_sizes_new');

function jma_personnel_title_filter($title)
{
    $title = explode(',', $title);
    $title[1] .= ' ' . $title[0];
    unset($title[0]);
    $title = implode(', ', $title);
    return $title;
}

/********************************************/

function jma_display_post_type_grid($atts)
{
    extract(shortcode_atts(array(
         'post_type' => 'personnel',
         'taxonomy' => '',
         'term' => '',
         'orderby' => 'menu_order',
         'order' => 'ASC',
         'gutter' => 30,
         'posts_per_page' => -1,
         'buttons' => false,
         'isotope' => false,
         /* classes for individual items */
         'classes' => 'col-md-2 col-sm-3 col-xs-6',
         ), $atts));
    $taxonomies = get_object_taxonomies($post_type, 'names');

    $jma_terms = get_terms($taxonomies);



    ob_start();
    $iso_grid = $isotope? 'jma-iso-tax-grid-wrap':'jma-standard-tax-grid-wrap';
    echo '<div id="jma-tax-grid-wrap" class="personnel-grid ' . $iso_grid . '">';
    if ($buttons) {
        echo '<div id="all-buttons" style="text-align: center">';

        echo '<div class="taxonomies button-group btn-group" style="margin-bottom: 10px">';
        echo '<!--<button type="button" class="all-btn btn btn-default trigger is-checked" data-filter="*">All Products</button>-->';
        $i = 0;
        foreach ($taxonomies as $tax) {
            //first button
            $checked = ' is-checked';
            $start_filter = 'leadership-term';
            if ($i) {//rest
                $checked = '';
                $start_filter =  'jma-column';
            }
            $i++;
            $tax = get_taxonomy($tax);
            echo '<button type="button" class="jma-btn trigger' . $checked . '" data-filter="' . $start_filter . '" data-tax="'.$tax->name.'">'.$tax->labels->singular_name.'</button>';
        }
        echo '</div><!--button-group--><br/>';
        $i = 0;
        foreach ($taxonomies as $taxonomy) {
            $styles = $i ?' style="height: 0': ' style="height: auto ';
            $i++;
            $jma_terms = get_terms(array('orderby' => 'term_order', 'parent' => 0, 'taxonomy' => $taxonomy , 'exclude' => array('tax-primary')));
            echo '<div class="filters button-group btn-group terms ' . $taxonomy . '"' . $styles . '">';
            foreach ($jma_terms as  $jma_term) {
                if ($jma_term->slug != 'tax-primary') {
                    echo '<button type="button" class="jma-btn trigger cat-id-'.$jma_term->term_id.'" data-filter="'.$jma_term->slug.'">'.$jma_term->name.'</button>';
                }
            }
            echo '</div><!--button-group-->';
        }
        echo '</div><!--all-buttons-->';
    }
    $gutter_style = $gutter? ' style="margin-left: -' . ($gutter/2) . 'px;margin-right: -' . ($gutter/2) . 'px"': '';
    echo '<div id="jma-tax-grid-inner" ' . $gutter_style . '>';
    $args = array(
        'post_type'=>$post_type,
        'orderby'=>$orderby,
        'order'=>$order,
        'posts_per_page'=>$posts_per_page
    );
    if ($taxonomy && $term) {
        $args['tax_query'] = array(
        array(
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $term,
        ),
    );
    }
    $custom_query = new WP_Query($args);
    echo '<!-- Start the Loop -->';
    $i = 1;
    while ($custom_query->have_posts()) {
        $custom_query->the_post();
        $jma_link = 'href="'.get_permalink().'"';
        $tb_jma_meta = get_post_meta($custom_query->post->ID, '', true);
        $jma_taxes = wp_get_post_terms($custom_query->post->ID, $taxonomies, array('fields' => 'slugs'));
        $jma_titles = wp_get_post_terms($custom_query->post->ID, 'title', array('fields' => 'names'));
        $custom_fields = get_fields();
        $class_string = '';
        foreach ($jma_taxes as $jma_tax) {
            $class_string .= ' '.$jma_tax;
        }
        $gutter_style = ' style="padding-left: ' . ($gutter/2) . 'px;padding-right: ' . ($gutter/2) . 'px;margin-bottom: ' . $gutter . 'px "';
        echo '<div class="jma-column'.$class_string. ' ' . $classes . ' post-id-'.$custom_query->post->ID.'" ' . $gutter_style . '" data-order="'.$i.'">';
        echo '<div>';
        echo '<a href="' . esc_url(get_permalink()) . '">';
        if (has_post_thumbnail()) {
            the_post_thumbnail('jma-personnel-grid'/*, array("class" => "pretty")*/);
        } else {
            echo '<img src=" ' . esc_url(plugins_url('/default.jpg', __FILE__)) . '"/>';
        }
        echo '</a>';

        echo '<div class="jma-tax-grid-title"><div><div class="personnel-name">' . get_the_title() . '</div>';
        if ($jma_titles[0]) {
            echo '<div class="main-title">' . $jma_titles[0] . '</div>';
        }
        if (is_array($custom_fields['additional-titles'])) {
            foreach ($custom_fields['additional-titles'] as $addition_title) {
                echo '<div class="sub-title">' . $addition_title['additional_title'] . '</div>';
            }
        }
        echo '</div></div>';

        echo '</div>';
        echo '</div><!-- .jma-column (end) -->';

        ++$i;
    }
    echo '</div><!-- jma-tax-grid-inner (end )-->';

    echo '<div class="clear"></div>';
    echo '</div><!-- jma-tax-wrap (end )-->';

    $x = ob_get_contents();
    ob_end_clean();
    wp_reset_query();

    return str_replace("\r\n", '', $x);
}
add_shortcode('post_type_grid', 'jma_display_post_type_grid');

function jma_personnel_redirect()
{
    add_filter('the_title', 'jma_personnel_title_filter');
}
add_action('template_redirect', 'jma_personnel_redirect');
