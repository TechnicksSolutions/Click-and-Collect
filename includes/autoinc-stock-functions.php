<?php

function branchStockEnabled( $productID ) {
	$useBranchStock = get_post_meta( $productID, 'cac_USE_BRANCH_STOCK', true );

	return $useBranchStock === 'yes';
}

function getProductBranchStock( $productID ) {

	if ( ! branchStockEnabled( $productID ) ) {
		return false;
	}

	$res  = array();
	$args = array(
		'post_type'      => 'branches',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);


	$branches = new WP_Query( $args );
	while ( $branches->have_posts() ) : $branches->the_post();
		$branchRow          = array();
		$id                 = get_the_ID();
		$branchName         = get_the_title();
		$branchStock        = get_post_meta( $productID, 'cac_BRANCH_STOCK_' . $id, true );
		$branchRow['id']    = $id;
		$branchRow['name']  = $branchName;
		$branchRow['stock'] = $branchStock;
		$res[]              = $branchRow;
	endwhile;

	wp_reset_postdata();
	return $res;

}

/**
 * Add a custom product data tab
 */
add_filter( 'woocommerce_product_tabs', 'cac_product_tab' );
function cac_product_tab( $tabs ) {

	// Adds the new tab

	if ( branchStockEnabled( get_the_ID() ) ) {
		$tabs['cac_tab'] = array(
			'title'    => __( 'Branch Stock', 'clickcollect' ),
			'priority' => 50,
			'callback' => 'cac_product_tab_content'
		);
	}

	return $tabs;

}

function cac_product_tab_content() {

	// The new tab content
	$branches = getProductBranchStock( get_the_ID() );
	if ( $branches ) {
		?>
        <table class="woocommerce-product-attributes shop_attributes">
			<?php foreach ( $branches as $branch ): ?>
	        <tr>
		        <th><?php echo $branch['name'];?></th>
		        <td><?php echo $branch['stock'];?></td>
	        </tr>
			<?php endforeach; ?>
        </table>
		<?php

	}
}
