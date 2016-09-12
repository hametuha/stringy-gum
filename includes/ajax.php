<?php
/**
 * Register Ajax
 */
add_action( 'admin_init', function () {
	add_action( 'wp_ajax_term_dropdown', function () {
		try {
			/**
			 * @var string $taxonomy
			 * @var string $query
			 */
			$get = [];
			foreach ( [ 's' => 'query', 't' => 'taxonomy' ] as $key => $label ) {
				if ( ! isset( $_GET[ $key ] ) || ! $_GET[ $key ] ) {
					throw new Exception( sprintf( __( 'Parameter %s is not set.', 'sg' ), $key ), 400 );
				}
				$get[$label] = $_GET[ $key ];
			}
			extract( $get );
			$result = sg_get_term_tree( $taxonomy, $query );
			wp_send_json( $result );
		} catch ( Exception $e ) {
			status_header( $e->getCode() );
			wp_send_json( [
				'status'  => $e->getCode(),
				'message' => $e->getMessage(),
			] );
		}
	} );
} );

