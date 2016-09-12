<?php
/**
 * Change context for drop down
 */
add_filter( 'get_terms_defaults', function( $args, $taxonomy ) {
	if ( sg_is_huge( $taxonomy ) && ( ! $args['child_of'] && ! $args['parent'] ) ) {
		$args['depth'] = 1;
	}
	return $args;
}, 10, 2 );

/**
 * If drop down is changed, use select2
 */
add_filter( 'wp_dropdown_cats', function( $output, $args ) {
	if ( isset( $args['taxonomy'] ) && sg_is_huge( $args['taxonomy'] ) ) {
		$output = sg_select( $args['taxonomy'], 'parent', [
			'selected' => isset( $args['selected'] ) ? $args['selected'] : 0,
		] );
	}
	return $output;
}, 10, 2 );
