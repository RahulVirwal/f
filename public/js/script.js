jQuery(document).ready(function($) {
    // Make AJAX request to fetch product data
    $.ajax({
        url: myAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'my_get_products'
        },
        success: function(response) {
            // Handle successful AJAX response
            if (response.success) {
                // Update HTML content of home page post type with fetched product data
                // Example: $('#home-page-content').html(response.data.products);
            } else {
                // Handle error
                console.error('Error: ' + response.data.message);
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX error
            console.error('AJAX Error: ' + status + ' - ' + error);
        }
    });
});
