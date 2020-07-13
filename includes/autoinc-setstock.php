<?php
function setBranchStockStatus( $status ) {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	$loop = new WP_Query( $args );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			echo 'Setting Branch Stock Status for ' . get_the_title();
			echo '<br> Current Value = '.get_post_meta(get_the_ID(),'cac_USE_BRANCH_STOCK',true);
			echo '<br> result = '. update_post_meta( get_the_ID(), 'cac_USE_BRANCH_STOCK', $status ).'<br>';
		endwhile;
	}

	wp_reset_postdata();
}

function setProductBranchStockLvl( $productID, $branchID, $lvl ) {
	$key = 'cac_BRANCH_STOCK_' . $branchID;
	update_post_meta( $productID, $key, $lvl );
}

function setDummyStockLvls() {
	$args = array(
		'post_type'      => 'branches',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	$branchIDs = array();
	$branches  = new WP_Query( $args );
	while ( $branches->have_posts() ) : $branches->the_post();
		$id          = get_the_ID();
		$branchIDs[] = $id;
	endwhile;

	wp_reset_postdata();

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	$loop = new WP_Query( $args );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			$post_id = get_the_ID();
			foreach ( $branchIDs as $branchID ) {
				setProductBranchStockLvl( $post_id, $branchID, 10 );
			}
		endwhile;
	}

	wp_reset_postdata();
}