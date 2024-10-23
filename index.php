<?php
/**
 * Plugin Name: Travel Packages
 * Version: 1.0
 * Author: Sayeh Mirghasemi
 * Journey Mentor
 */

// Register Custom Post Type
function create_travel_packages_cpt() {
    $labels = array(
        'name'                  => _x('Travel Packages', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Travel Package', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => __('Travel Packages', 'textdomain'),
        'name_admin_bar'        => __('Travel Package', 'textdomain'),
        'archives'              => __('Package Archives', 'textdomain'),
        'attributes'            => __('Package Attributes', 'textdomain'),
        'parent_item_colon'     => __('Parent Package:', 'textdomain'),
        'all_items'             => __('All Packages', 'textdomain'),
        'add_new_item'          => __('Add New Package', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'new_item'              => __('New Package', 'textdomain'),
        'edit_item'             => __('Edit Package', 'textdomain'),
        'update_item'           => __('Update Package', 'textdomain'),
        'view_item'             => __('View Package', 'textdomain'),
        'view_items'            => __('View Packages', 'textdomain'),
        'search_items'          => __('Search Package', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Travel Package', 'textdomain'),
        'description'           => __('Travel packages including price and availability', 'textdomain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'custom-fields'),
        'public'                => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-palmtree',
        'has_archive'           => true,
        'rewrite'               => array('slug' => 'travel-packages'),
        'show_in_rest'          => true,
    );
    
    register_post_type('travel_package', $args);
}
add_action('init', 'create_travel_packages_cpt');

// Add custom meta boxes for price and availability
function add_travel_package_meta_boxes() {
    add_meta_box(
        'travel_package_price',
        'Package Price',
        'render_price_meta_box',
        'travel_package',
        'side',
        'default'
    );
    
    add_meta_box(
        'travel_package_availability',
        'Package Availability',
        'render_availability_meta_box',
        'travel_package',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_travel_package_meta_boxes');

// Render the price meta box
function render_price_meta_box($post) {
    $price = get_post_meta($post->ID, '_travel_package_price', true);
    echo '<label for="travel_package_price">Price ($):</label>';
    echo '<input type="number" id="travel_package_price" name="travel_package_price" value="' . esc_attr($price) . '" />';
}

// Render the availability meta box
function render_availability_meta_box($post) {
    $availability = get_post_meta($post->ID, '_travel_package_availability', true);
    echo '<label for="travel_package_availability">Availability:</label>';
    echo '<input type="text" id="travel_package_availability" name="travel_package_availability" value="' . esc_attr($availability) . '" />';
}

// Save custom meta box data
function save_travel_package_meta($post_id) {
    if (array_key_exists('travel_package_price', $_POST)) {
        update_post_meta($post_id, '_travel_package_price', $_POST['travel_package_price']);
    }
    if (array_key_exists('travel_package_availability', $_POST)) {
        update_post_meta($post_id, '_travel_package_availability', $_POST['travel_package_availability']);
    }
}
add_action('save_post', 'save_travel_package_meta');

// Front-end display for Travel Packages
function display_travel_packages_shortcode() {
    $args = array(
        'post_type' => 'travel_package',
        'posts_per_page' => 10,
    );
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $output = '<div class="travel-packages">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $price = get_post_meta(get_the_ID(), '_travel_package_price', true);
            $availability = get_post_meta(get_the_ID(), '_travel_package_availability', true);
            
            $output .= '<div class="travel-package">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<p>' . get_the_content() . '</p>';
            $output .= '<p><strong>Price: </strong>$' . esc_html($price) . '</p>';
            $output .= '<p><strong>Availability: </strong>' . esc_html($availability) . '</p>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        wp_reset_postdata();
        
        return $output;
    } else {
        return '<p>No travel packages found.</p>';
    }
}
add_shortcode('display_travel_packages', 'display_travel_packages_shortcode');

// Enqueue front-end styles
function travel_packages_styles() {
    wp_enqueue_style('travel-packages-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'travel_packages_styles');

