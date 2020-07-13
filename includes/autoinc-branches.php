<?php
// Our custom post type function
function create_posttype() {

    register_post_type( 'branches',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Branches', 'clickcollect' ),
                'singular_name' => __( 'Branch', 'clickcollect' ),
                'all_items' => __('All Branches', 'clickcollect'), /* the all items menu item */
                'add_new' => __('Add New', 'clickcollect'), /* The add new menu item */
                'add_new_item' => __('Add New Branch', 'clickcollect'), /* Add New Display Title */
                'edit' => __( 'Edit', 'clickcollect' ), /* Edit Dialog */
                'edit_item' => __('Edit Branch', 'clickcollect'), /* Edit Display Title */
                'new_item' => __('New Branch', 'clickcollect'), /* New Display Title */
                'view_item' => __('View Branch', 'clickcollect'), /* View Display Title */
                'search_items' => __('Search Branches', 'clickcollect'), /* Search Custom Type Title */
                'not_found' =>  __('Nothing found in the Database.', 'clickcollect'), /* This displays if there are no entries yet */
                'not_found_in_trash' => __('Nothing found in Trash', 'clickcollect'), /* This displays if there is nothing in the trash */
            ),
            'menu_icon' => 'dashicons-admin-multisite',
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'branches'),
            'show_in_rest' => true,

        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );

add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    // Use your post type key instead of 'product'
    if ($post_type === 'branches') return false;
    return $current_status;
}

add_action( 'init', function() {
    remove_post_type_support( 'branches', 'editor' );
}, 99);

//branch address

function branch_address_meta_box() {
    add_meta_box(
        'branch_address',
        __( 'Branch Address', 'clickcollect' ),
        'branch_address_meta_box_callback',
        'branches'
    );
}

function branch_address_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'branch_address_nonce', 'branch_address_nonce' );

    $value = get_post_meta( $post->ID, '_branch_address', true );

    echo '<textarea style="width:100%" id="branch_address" name="branch_address">' . esc_attr( $value ) . '</textarea>';
}

add_action( 'add_meta_boxes', 'branch_address_meta_box' );

function save_branch_address_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['branch_address_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['branch_address_nonce'], 'branch_address_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['branch_address'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['branch_address'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_branch_address', $my_data );
}

add_action( 'save_post', 'save_branch_address_meta_box_data' );

//branch Phone

function branch_phone_meta_box() {
    add_meta_box(
        'branch_phone',
        __( 'Branch Phone', 'clickcollect' ),
        'branch_phone_meta_box_callback',
        'branches'
    );
}

function branch_phone_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'branch_phone_nonce', 'branch_phone_nonce' );

    $value = get_post_meta( $post->ID, '_branch_phone', true );

    echo '<input type="text" style="width:100%" id="branch_phone" name="branch_phone" value="' . esc_attr( $value ).'">';
}

add_action( 'add_meta_boxes', 'branch_phone_meta_box' );

function save_branch_phone_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['branch_phone_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['branch_phone_nonce'], 'branch_phone_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['branch_phone'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['branch_phone'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_branch_phone', $my_data );
}

add_action( 'save_post', 'save_branch_phone_meta_box_data' );

//branch Email

function branch_email_meta_box() {
    add_meta_box(
        'branch_email',
        __( 'Branch Email Address', 'clickcollect' ),
        'branch_email_meta_box_callback',
        'branches'
    );
}

function branch_email_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'branch_email_nonce', 'branch_email_nonce' );

    $value = get_post_meta( $post->ID, '_branch_email', true );

    echo '<input type="email" style="width:100%" id="branch_email" name="branch_email" value="' . esc_attr( $value ).'">';
}

add_action( 'add_meta_boxes', 'branch_email_meta_box' );

function save_branch_email_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['branch_email_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['branch_email_nonce'], 'branch_email_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['branch_email'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['branch_email'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_branch_email', $my_data );
}

add_action( 'save_post', 'save_branch_email_meta_box_data' );