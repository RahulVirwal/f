<?php
/*
Plugin Name: Cart Plugin
Description: Simple eCommerce cart plugin for WordPress.
Version: 1.0
Author: Rahul
*/


define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include necessary files
include_once(MY_PLUGIN_DIR . 'includes/functions.php');
include_once(MY_PLUGIN_DIR . 'includes/helpers.php');

// Register activation/deactivation/uninstall hooks
register_activation_hook(__FILE__, 'my_ecommerce_cart_activate');
register_deactivation_hook(__FILE__, 'my_ecommerce_cart_deactivate');
register_uninstall_hook(__FILE__, 'my_ecommerce_cart_uninstall');

// Activation function
function my_ecommerce_cart_activate()
{
    // Activation logic here
    // For example, you might create database tables or set up default options
    my_register_product_post_type();

    // Create the products table
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        description text NOT NULL,
        price decimal(10, 2) NOT NULL,
        image_url varchar(255) NOT NULL, 
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


// Deactivation function
function my_ecommerce_cart_deactivate()
{
    // Deactivation logic here
    // For example, you might remove scheduled events or clear caches
    // Optionally, you can unregister the custom post types to clean up
    unregister_post_type('product');
}

// Uninstallation function
function my_ecommerce_cart_uninstall()
{
    // Uninstallation logic here
    // For example, you might delete database tables or options
    // Optionally, you can unregister the custom post types to clean up
    unregister_post_type('product');
}

function my_enqueue_scripts()
{

    wp_enqueue_script('my-ajax-script', plugin_dir_url(__FILE__) . 'public/js/ajax-script.js', array('jquery'), '1.0', true);
    wp_localize_script('my-ajax-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));


    wp_enqueue_script('home-js', plugin_dir_url(__FILE__) . 'public/js/home.js', array('jquery'), '1.0', true);


    wp_enqueue_style('my-custom-css', plugin_dir_url(__FILE__) . 'public/css/custom.css');
    wp_enqueue_style('home-page-css', plugin_dir_url(__FILE__) . 'public/css/mainHome.css');
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');

function my_ecommerce_cart_init()
{
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

function my_register_product_post_type()
{
    $labels = array(
        'name'               => _x('Products', 'post type general name', 'cart-plugin'),
        // Add your other labels here
    );

    $args = array(
        'labels'             => $labels,
        // Add your other arguments here
    );

    register_post_type('product', $args);
}
add_action('init', 'my_register_product_post_type');

function my_products_shortcode($atts)
{
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'category' => '',
        'limit'    => 5,
    ), $atts);

    // Query products
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $atts['limit'],
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    $products_query = new WP_Query($args);

    // Output products
    if ($products_query->have_posts()) {
        $output = '<ul class="products">';
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        $output .= '</ul>';
        wp_reset_postdata();
    } else {
        $output = '<p>No products found.</p>';
    }

    return $output;
}


// Shortcode for displaying cart
function my_cart_shortcode($atts)
{
    ob_start(); // Start output buffering
    include(MY_PLUGIN_DIR . 'home.php'); // Include the home.php file
    return ob_get_clean(); // Return the output buffer contents
}

add_shortcode('my_cart', 'my_cart_shortcode');

// Add a menu item in the admin dashboard
function my_ecommerce_menu()
{
    add_menu_page('Add Product', 'Add Product', 'manage_options', 'add-product', 'my_ecommerce_add_product_page');
    add_submenu_page('add-product', 'Home', 'Home', 'manage_options', 'home', 'my_ecommerce_show_home_page'); // Add submenu "Home"
    // add_submenu_page('add-product', 'My Home', 'My Home', 'manage_options', 'myhome', 'my_ecommerce_show_my_home_page'); // Add submenu "My Home"
}
add_action('admin_menu', 'my_ecommerce_menu');


// Callback function to display the add product page
function my_ecommerce_add_product_page()
{
    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Process the form data
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_text_field($_POST['description']);
        $price = floatval($_POST['price']);

        // Handle image upload
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $upload_overrides = array('test_form' => false);
            $uploaded_image = wp_handle_upload($_FILES['image'], $upload_overrides);

            if (isset($uploaded_image['url'])) {
                $image_url = $uploaded_image['url']; // Get the image URL
            }
        } else {
            $image_url = ''; // Default value if no image uploaded
        }

        // Insert data into the products table
        global $wpdb;
        $table_name = $wpdb->prefix . 'products';

        $wpdb->insert(
            $table_name,
            array(
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'image_url' => $image_url // Store the image URL in the database
            ),
            array(
                '%s',
                '%s',
                '%f',
                '%s'
            )
        );

        // Display a success message
        echo '<div class="updated"><p>Product added successfully!</p></div>';
    }

    // Display the add product form
?>
    <div class="wrap">
        <h2>Add New Product</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="title">Product Title</label></th>
                        <td><input type="text" name="title" id="title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="description">Product Description</label></th>
                        <td><textarea name="description" id="description" class="regular-text" required></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="price">Product Price</label></th>
                        <td><input type="number" name="price" id="price" class="regular-text" step="0.01" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="image">Product Image</label></th>
                        <td><input type="file" name="image" id="image" accept="image/*"></td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Add Product">
        </form>
    </div>
<?php
}

// Function to retrieve products from the database
function get_products_from_database()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    $products = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC", ARRAY_A);
    return $products;
}
// Function to display products as images in the Home submenu
function my_ecommerce_show_home_page()
{
?>

    <div class="wrap">
        <h2>Home</h2>
        <div class="product-gallery">
            <?php
            // Retrieve products from the database
            $products = get_products_from_database();

            // Check if products exist
            if ($products) {
                // Display products as images
                foreach ($products as $product) {
            ?>
                    <figure class="snip">
                        <h4><?php echo esc_html($product['title']); ?></h4>
                        <div class="image">
                            <img src="<?php echo esc_url($product['image_url']); ?>" class="product-image" width="200px" />
                        </div>
                        <figcaption>
                            <p><?php echo esc_html($product['description']); ?></p>
                            <div class="price">
                                <s>$24.00</s>
                                <div class="real" data-price="<?php echo esc_html($product['price']); ?>">$<?php echo esc_html($product['price']); ?></div>
                            </div>
                        </figcaption>
                        <button class="add-to-cart" href="#" data-product-id="<?php echo esc_attr($product['id']); ?>">Add to Cart</button>
                    </figure>
            <?php
                }
            } else {
                // If no products found
                echo '<p>No products found.</p>';
            }
            ?>
        </div>
    </div>
    <a class="cart" href="javascript:void(0);" title="Your Cart">
        <img src="https://4.bp.blogspot.com/-ipiLQtQy_oc/WdcKmFpW6KI/AAAAAAAABtc/mtwqfb7U_pIE18vTb8hqZiaWVngMJKm0QCLcBGAs/s1600/cart.png" />
        <span id="count-item" data-count="0">0 item</span>
    </a>
    <!-- Check out -->
    <div class="container">
        <div class="table">
            <span class="empty">Your cart is empty!</span>
            <img class="empty" src="https://2.bp.blogspot.com/-VYC7hvhUz4U/WdcPLAr86jI/AAAAAAAABuA/G3y27JwIL_0S5OsVIp6maXjsdgLRumaTwCLcBGAs/s1600/emptycart.png" style="width:200px;" />
            <div class="col1-name"></div>
            <div class="col2-price"></div>
        </div>
        <div class="bin"><span>Clear All</span><img id="bin" src="https://4.bp.blogspot.com/-Luy983wX20I/WdcLbR86CCI/AAAAAAAABto/g-7S5lzDEugWAQrIiwOtPn4JOkQNboxyACLcBGAs/s1600/delete.png" style="width:40px" /></div>
        <div class="total">
            <div class="total-text">Total :</div>
            <div class="total-amount">$ 0</div>
        </div>
        <div class="checkout" onclick="document.getElementById('id01').style.display='block'">Check out</div>
    </div>
    <!-- Bill -->
    <div id="id01" class="modal">
        <div class="wrapper">
            <div class="bill-content">
                <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close">&times;</span>
                <h1 class="box-title">Checkout</h1>
                <div class="price-total">
                    <span>PAY</span>
                    <div class="pay-last">$ 0</div>
                </div>
                <form>
                    <div class="form-text">
                        <label>Card Number</label>
                        <input name="card-number" type="text" required>
                    </div>
                    <div class="form-text">
                        <label>Card Verification Value</label>
                        <input name="name" type="text" placeholder="CVV" required>
                    </div>
                    <div class="form-text" id="col01">
                        <label>Expiry Date</label>
                        <input name="card-number" type="text" placeholder="MM/YY" maxlength="3" required>
                    </div>
                    <div class="form-text" id="col02">
                        <label>Coupon Code</label>
                        <input name="card-number" type="text">
                    </div>
                    <button id="end">PAY NOW</button>
                </form>
                <button id="coupon">Get coupon code</button>
            </div>
        </div>
    </div>

<?php
}


