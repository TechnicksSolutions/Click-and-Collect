<?php

add_filter('woocommerce_product_data_tabs', 'add_cac_product_data_tab', 99, 1);
function add_cac_product_data_tab($product_data_tabs)
{

    $product_data_tabs['cac-tab'] = array(
        'label' => __('Click and Collect', 'clickandcollect'),
        'target' => 'cac_product_data',
    );
    return $product_data_tabs;
}

add_action('woocommerce_product_data_panels', 'add_cac_product_data_fields');
function add_cac_product_data_fields()
{
    ?>
    <div id="cac_product_data" class="panel woocommerce_options_panel">
        <?php
        woocommerce_wp_checkbox(array(
            'id' => 'cac_USE_BRANCH_STOCK',
            'wrapper_class' => 'show_if_simple',
            'label' => __('Enable Branch Stock', 'clickandcollect'),
            'description' => __('Use branch stock levels for click and collect purchase of this product', 'clickandcollect'),
            'cbvalue' => 'yes',
            'desc_tip' => false,
        ));

        ?>
        <style>
            .cac-text-field input[type=text].short {
                width: 100%;
            }

            .cac-text-field.no-label label {
                display: none;
            }

            .cac-text-field p.form-field {
                padding-left: 1em !important;
            }
        </style>
        <div style="padding-left: 10px;">
            <table>
                <tr>
                    <th style="text-align: left;">Branch Name</th>
                    <th style="text-align: left;">Stock in Branch</th>
                </tr>
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
                    <tr>
                        <td><?php
                            echo $branchName;
                            ?></td>
                        <td class="cac-text-field no-label"><?php
                            woocommerce_wp_text_input(array(
                                'id' => 'cac_BRANCH_STOCK_' . $id,
                                'wrapper_class' => 'show_if_simple',
                                'label' => __('', 'clickandcollect'),
                                'description' => __('', 'clickandcollect'),
                                'default' => '',
                                'desc_tip' => false,
                            ));
                            ?></td>
                    </tr>
                <?php
                endwhile;

                wp_reset_postdata();

                ?>
            </table>
        </div>

    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'woocommerce_process_cac_product_meta_fields_save');
function woocommerce_process_cac_product_meta_fields_save($post_id)
{
    $cac_USE_BRANCH_STOCK = $_POST['cac_USE_BRANCH_STOCK'];
    update_post_meta($post_id, 'cac_USE_BRANCH_STOCK', $cac_USE_BRANCH_STOCK);
    foreach($_POST as $key => $value) {
        if (strpos($key, 'cac_BRANCH_STOCK_') === 0) {
            update_post_meta($post_id, $key, $value);
        }
    }
}