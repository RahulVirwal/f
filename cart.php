<?php
/*
Plugin Name: My Ecommerce Cart Plugin
Description: Simple eCommerce cart plugin for WordPress.
Version: 1.0
Author: Rahul
*/

// Activation hook
register_activation_hook(__FILE__, 'my_ecommerce_cart_activate');

// Deactivation hook
register_deactivation_hook(__FILE__, 'my_ecommerce_cart_deactivate');

// Uninstallation hook
register_uninstall_hook(__FILE__, 'my_ecommerce_cart_uninstall');

// Activation function
function my_ecommerce_cart_activate() {
    // Activation logic here
    // For example, you might create database tables or set up default options
    my_register_product_post_type();
    my_register_homepage_post_type(); // Register homepage custom post type
}

// Deactivation function
function my_ecommerce_cart_deactivate() {
    // Deactivation logic here
    // For example, you might remove scheduled events or clear caches
    // Optionally, you can unregister the custom post types to clean up
    unregister_post_type('product');
    unregister_post_type('homepage');
}

// Uninstallation function
function my_ecommerce_cart_uninstall() {
    // Uninstallation logic here
    // For example, you might delete database tables or options
    // Optionally, you can unregister the custom post types to clean up
    unregister_post_type('product');
    unregister_post_type('homepage');
}

// Include necessary files
include_once(plugin_dir_path(__FILE__) . 'includes/functions.php');
include_once(plugin_dir_path(__FILE__) . 'includes/helpers.php');

// Enqueue frontend scripts and styles
function my_enqueue_scripts() {
    wp_enqueue_style('my-ecommerce-cart-style', plugin_dir_url(__FILE__) . 'public/css/style.css');
    wp_enqueue_script('my-ecommerce-cart-script', plugin_dir_url(__FILE__) . 'public/js/script.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');

// Initialize plugin
function my_ecommerce_cart_init() {
    // Initialize session if not already started
    if (!session_id()) {
        session_start();
    }

    // Create cart session variable if not already exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
}
add_action('init', 'my_ecommerce_cart_init');

// Register custom post type for products
function my_register_product_post_type() {
    $labels = array(
        'name'               => _x('Products', 'post type general name', 'your-plugin-textdomain'),
        'singular_name'      => _x('Product', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name'          => _x('Products', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar'     => _x('Product', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new'            => _x('Add New', 'product', 'your-plugin-textdomain'),
        'add_new_item'       => __('Add New Product', 'your-plugin-textdomain'),
        'new_item'           => __('New Product', 'your-plugin-textdomain'),
        'edit_item'          => __('Edit Product', 'your-plugin-textdomain'),
        'view_item'          => __('View Product', 'your-plugin-textdomain'),
        'all_items'          => __('All Products', 'your-plugin-textdomain'),
        'search_items'       => __('Search Products', 'your-plugin-textdomain'),
        'parent_item_colon'  => __('Parent Products:', 'your-plugin-textdomain'),
        'not_found'          => __('No products found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No products found in Trash.', 'your-plugin-textdomain')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.', 'your-plugin-textdomain'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'product'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt')
    );

    register_post_type('product', $args);
}
add_action('init', 'my_register_product_post_type');

// Register custom post type for homepage
function my_register_homepage_post_type() {
    $labels = array(
        'name'               => _x('Homepage', 'post type general name', 'your-plugin-textdomain'),
        'singular_name'      => _x('Homepage', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name'          => _x('Homepage', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar'     => _x('Homepage', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new'            => _x('Add New', 'homepage', 'your-plugin-textdomain'),
        'add_new_item'       => __('Add New Homepage Content', 'your-plugin-textdomain'),
        'new_item'           => __('New Homepage Content', 'your-plugin-textdomain'),
        'edit_item'          => __('Edit Homepage Content', 'your-plugin-textdomain'),
        'view_item'          => __('View Homepage Content', 'your-plugin-textdomain'),
        'all_items'          => __('All Homepage Content', 'your-plugin-textdomain'),
        'search_items'       => __('Search Homepage Content', 'your-plugin-textdomain'),
        'parent_item_colon'  => __('Parent Homepage Content:', 'your-plugin-textdomain'),
        'not_found'          => __('No homepage content found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No homepage content found in Trash.', 'your-plugin-textdomain')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Homepage content.', 'your-plugin-textdomain'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'homepage'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt')
    );

    register_post_type('homepage', $args);
}
add_action('init', 'my_register_homepage_post_type');
