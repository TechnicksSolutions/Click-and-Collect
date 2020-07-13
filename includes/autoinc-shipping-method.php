<?php

if (!defined('WPINC')) {
    die;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     * Create the Click and Collect Shipping Method
     */

    function click_collect_method()
    {
        if (!class_exists('click_collect_method')) {
            class click_collect_method extends WC_Shipping_Method
            {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct()
                {
                    $this->id = 'clickcollect';
                    $this->method_title = __('Click & Collect Shipping', 'clickcollect');
                    $this->method_description = __('Custom Shipping Method for Click & Collect', 'clickcollect');

                    $this->init();

                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Click & Collect Shipping', 'clickcollect');
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                /**
                 * Define settings field for this shipping
                 * @return void
                 */
                function init_form_fields()
                {

                    // We will add our settings here

                    $this->form_fields = array(

                        'enabled' => array(
                            'title' => __('Enable', 'clickcollect'),
                            'type' => 'checkbox',
                            'description' => __('Enable this shipping.', 'clickcollect'),
                            'default' => 'yes'
                        ),

                        'title' => array(
                            'title' => __('Title', 'clickcollect'),
                            'type' => 'text',
                            'description' => __('Title to be display on site', 'clickcollect'),
                            'default' => __('Click & Collect', 'clickcollect')
                        ),

                        'mincarttotal' => array(
                            'title' => __('Minimum Cart value', 'clickcollect'),
                            'type' => 'number',
                            'description' => __('Minimum Cart value to apply charge (Blank or 0 = Always apply charge)', 'clickcollect'),
                            'default' => 50
                        ),

                        'collectcharge' => array(
                            'title' => __('Collect charge', 'clickcollect'),
                            'type' => 'text',
                            'description' => __('Cost for Click & Collect Service (Blank, NaN or 0 = No Charge)', 'clickcollect'),
                            'default' => 0
                        ),

                    );

                }

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package , $woocommerce
                 * @return void
                 */
                public function calculate_shipping($package = array())
                {

                    // We will add the cost, rate and logics in here

                    global $woocommerce;

                    $cost = $this->settings['collectcharge'];
                    $cartValue = $woocommerce->cart->cart_contents_total;
                    $mincartTotal = $this->settings['mincarttotal'];

                    //error_log('$cartTotal = '.$cartValue);

                    if ($cartValue >= $mincartTotal) {
                        $cost = 0;
                    }

                    foreach ($package['contents'] as $item_id => $values) {
                        $_product = $values['data'];
                    }

                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost
                    );

                    $this->add_rate($rate);

                }
            }
        }
    }
    add_action('woocommerce_shipping_init', 'click_collect_method');

    /**
     * @param $methods
     * @return mixed
     *
     * Add the Click and Collect Shipping Method
     */
    function add_click_collect_method($methods)
    {
        $methods[] = 'click_collect_method';
        return $methods;
    }
    add_filter('woocommerce_shipping_methods', 'add_click_collect_method');

    //This function is unused: cac_store_row_layout()
    function cac_store_row_layout()
    {
        $packages = WC()->shipping->get_packages();
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        if (is_array($chosen_methods) && in_array('clickcollect', $chosen_methods)) {

            foreach ($packages as $i => $package) {
                if ($chosen_methods[$i] != "clickcollect") {
                    continue;
                }
                ?>
                <tr class="shipping-cac-store">
                    <th><strong>Select a Branch</strong></th>
                    <td>
                        <label for="cac_branchID">
                            Branches avaiable for collection
                            <select name="cac_branchID" id="cac_breanchID">
                                <?php
                                $args = array(
                                    'post_type' => 'branches',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1,
                                    'orderby' => 'title',
                                    'order' => 'ASC',
                                );

                                $branches = new WP_Query($args);
                                while ($branches->have_posts()) : $branches->the_post();
                                    $id = get_the_ID();
                                    $branchName = get_the_title();
                                    ?>
                                    <option value="<?php echo $id; ?>"><?php echo $branchName; ?></option>
                                <?php
                                endwhile;

                                wp_reset_postdata();
                                ?>
                            </select>
                        </label>

                    </td>
                </tr>
                <?php
            }
        }
    }

    /**
     * @param $posted
     *
     * Validates the Checkout Page
     */
    function cac_validate_order($posted)
    {
        $packages = WC()->shipping()->get_packages();

        $chosen_methods = WC()->session->get('chosen_shipping_methods');

        if (is_array($chosen_methods) && in_array('clickcollect', $chosen_methods)) {
            foreach ($packages as $i => $package) {
                if ($chosen_methods[$i] != 'clickcollect') {
                    continue;
                }


                $frmData = array();
                parse_str(urldecode($_POST['post_data']), $frmData);
                $shipping_COLLECTION_BRANCH = $frmData['shipping_COLLECTION_BRANCH'];

                if($shipping_COLLECTION_BRANCH) {

                    foreach ($package['contents'] as $item_id => $values) {
                        $_product = $values['data'];
                        $cac_USE_BRANCH_STOCK = get_post_meta($_product->get_id(), 'cac_USE_BRANCH_STOCK');
                        if ($cac_USE_BRANCH_STOCK && ($cac_USE_BRANCH_STOCK[0] === 'yes')) {

                            $branchStockLevelMETA = get_post_meta($_product->get_id(), 'cac_BRANCH_STOCK_' . $shipping_COLLECTION_BRANCH);
                            $branchStockLevel = $branchStockLevelMETA[0];

                            //error_log('$branchStockLevel = ' . print_r($branchStockLevel, true) . ' For BranchID = ' . $shipping_COLLECTION_BRANCH);

                            if (!($branchStockLevel) || ($branchStockLevel < 1)) {

                                $message = sprintf(__('Sorry,  %s has no stock in your selected collection branch. Please choose a different branch or update your basket', 'clickcollect'), $_product->get_title());

                                $messageType = "error";

                                if (!wc_has_notice($message, $messageType)) {

                                    wc_add_notice($message, $messageType);

                                }
                            }
                        } else {
                            //error_log('not branch stock selected');
                        }
                    }
                } else {
                    //error_log('no branch found');
                }

            }
        }
    }
    add_action('woocommerce_review_order_before_cart_contents', 'cac_validate_order', 10);
    add_action('woocommerce_after_checkout_validation', 'cac_validate_order', 10);

    /**
     * @param $button
     * @return string
     *
     * Disables the checkout button if Click and Collect not validated
     */
    function inactive_order_button_html( $button ) {
        $found = false;

        $packages = WC()->shipping()->get_packages();

        $chosen_methods = WC()->session->get('chosen_shipping_methods');

        if (is_array($chosen_methods) && in_array('clickcollect', $chosen_methods)) {
            foreach ($packages as $i => $package) {
                if ($chosen_methods[$i] != 'clickcollect') {
                    continue;
                }
            }

            $frmData = array();
            parse_str(urldecode($_POST['post_data']), $frmData);
            $shipping_COLLECTION_BRANCH = $frmData['shipping_COLLECTION_BRANCH'];

            if($shipping_COLLECTION_BRANCH) {

                foreach ($package['contents'] as $item_id => $values) {
                    $_product = $values['data'];
                    $cac_USE_BRANCH_STOCK = get_post_meta($_product->get_id(), 'cac_USE_BRANCH_STOCK');
                    if ($cac_USE_BRANCH_STOCK && ($cac_USE_BRANCH_STOCK[0] === 'yes')) {

                        $branchStockLevelMETA = get_post_meta($_product->get_id(), 'cac_BRANCH_STOCK_' . $shipping_COLLECTION_BRANCH);
                        $branchStockLevel = $branchStockLevelMETA[0];

                        //error_log('$branchStockLevel = ' . print_r($branchStockLevel, true) . ' For BranchID = ' . $shipping_COLLECTION_BRANCH);

                        if (!($branchStockLevel) || ($branchStockLevel < 1)) {

                            $found = true;
                        }
                    } else {
                        //error_log('not branch stock selected');
                    }
                }
            } else {
                //error_log('no branch found');
            }
        }

        // If found we replace the button by an inactive greyed one
        if( $found ) {
            $style = 'style="background:Silver !important; color:white !important; cursor: not-allowed !important; float: right;"';
            $button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );
            $button = '<a class="button" '.$style.'>' . $button_text . '</a>';
        }
        return $button;
    }
    add_filter('woocommerce_order_button_html', 'inactive_order_button_html' );

	/**
	 * @param $order_id
     *
     * Update Stock Levels
     * Action: cac_update_stock_level
	 */
    function cac_update_frm_entry_after_wc_order_completed( $order_id ) {
        if ( ! $order_id ) return;
        $order = new WC_Order( $order_id );

        $ship_array =$order->get_items( 'shipping' );
        $shipping = reset( $ship_array )->get_method_id();
        if($shipping==='clickcollect') {
            $collectionBranch = get_post_meta($order_id,'_shipping_COLLECTION_BRANCH',true);
            //error_log(print_r($collectionBranch,true));
            foreach ( $order->get_items() as $item_id => $item ) {

                if( $item['variation_id'] > 0 ){
                    $product_id = $item['variation_id']; // variable product
                } else {
                    $product_id = $item['product_id']; // simple product
                }

                $useBranchStock = get_post_meta($product_id,'cac_USE_BRANCH_STOCK',true);

                if($useBranchStock==='yes') {

                    $currentStock = (int)get_post_meta($product_id, 'cac_BRANCH_STOCK_' . $collectionBranch, true);
                    $numberOrdered = (int)$item['quantity'];
                    $newStock = $currentStock - $numberOrdered;

                    update_post_meta($product_id, 'cac_BRANCH_STOCK_' . $collectionBranch, $newStock);

                    do_action('cac_update_stock_level', $product_id, $currentStock, $numberOrdered, $newStock);
                }


            }
        }
    }
	add_action( 'woocommerce_thankyou', 'cac_update_frm_entry_after_wc_order_completed' );

    /**
     * Add a custom field (in an order) to the emails
     */
    add_filter( 'woocommerce_email_order_meta_fields', 'cac_woocommerce_email_order_meta_fields', 10, 3 );

    function cac_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
        $ship_array =$order->get_items( 'shipping' );
        $shipping = reset( $ship_array )->get_method_id();
        if($shipping==='clickcollect') {
            $fields['meta_key'] = array(
                'label' => __('Collection Branch'),
                'value' => get_post_meta($order->id, '_COLLECTION_BRANCH_NAME', true),
            );
        }
        return $fields;
    }

}


