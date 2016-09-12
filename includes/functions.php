<?php

/**
 * Get taxonomy
 *
 * @param bool $filter If false, filter won't be applied.
 * @return array
 */
function sg_option( $filter = true ) {
	$option = (array) get_option( 'stringy_gum_taxonomy', [] );
	if ( $filter ) {
		/**
		 * stringy_gum_taxonomy
		 *
		 * @param array $option
		 * @return array
		 */
		$option = apply_filters( 'stringy_gum_taxonomy', $option );
	}
	return $option;
}

/**
 * Detect if string is huge or not.
 *
 * @param string $taxonomy
 *
 * @return bool
 */
function sg_is_huge( $taxonomy ) {
	return false !== array_search( $taxonomy, sg_option() );
}

/**
 * Create select tag
 *
 * @param string $taxonomy
 * @param string $id
 * @param array $args
 *
 * @return string
 */
function sg_select( $taxonomy, $id, $args = [] ) {
	$args = wp_parse_args( $args, [
		'name' => $id,
	    'limit' => 1,
	    'class' => '',
	    'selected' => 0,
	    'multiple' => ( isset( $args['limit'] ) && $args['limit'] > 1 ),
	    'placeholder' => __( 'Type and search...', 'sg' ),
	] );
	$option = '';
	if ( $args['selected'] && ( $term = get_term_by( 'id', $args['selected'], $taxonomy ) ) ) {
		$option = sprintf( '<option value="%s" selected>%s</option>', esc_attr( $term->term_id ), esc_html( $term->name ) );
	}
	return sprintf(
		'<select data-replacer="select2" id="%1$s" name="%2$s" data-placeholder="%3$s" data-taxonomy="%4$s" data-length="%5$d" %6$s class="%7$s">%8$s</select>',
		esc_attr( $id ), esc_attr( $args['name'] ),
		esc_attr( $args['placeholder'] ),
		esc_attr( $taxonomy ), esc_attr( $args['limit'] ),
		$args['multiple'] ? 'multiple' : '',
		esc_attr( $args['class'] ),
		$option
	);
}

/**
 * Search term tree recursively
 *
 * @param WP_Term $term
 * @param array $node
 *
 * @return bool
 */
function sg_find_node( $term, &$node ) {
	if ( $node['term']->term_id == $term->parent ) {
		$node['children'][] = [
			'term' => $term,
			'children' => [],
		];
		return true;
	} else {
		foreach ( $node['children'] as &$child ) {
			if ( sg_find_node( $term, $child ) ) {
				return true;
			}
		}
	}
	return false;
};

/**
 * Search any term
 *
 * @param string $taxonomy
 * @param string $query
 *
 * @return array
 */
function sg_get_term_tree( $taxonomy, $query ) {
	global $wpdb;
	$query = "%{$query}%";
	$sql = <<<SQL
				SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->terms} AS t
				INNER JOIN {$wpdb->term_taxonomy} AS tt
				ON t.term_id = tt.term_id
				WHERE tt.taxonomy = %s
				  AND (
				  	(t.name LIKE %s)
				  	OR
				  	(tt.description LIKE %s)
				  )
				ORDER BY t.name ASC
				LIMIT 20
SQL;
	$all_terms = [];
	$found = $wpdb->get_results( $wpdb->prepare( $sql, $taxonomy, $query, $query ) );
	$total = (int) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
	foreach ( $found as $term ) {
		$terms = [];
		$term = new WP_Term( $term );
		while ( $term ) {
			array_unshift( $terms, $term );
			if ( $term->parent && ( $parent = get_term_by( 'term_id', $term->parent, $term->taxonomy ) ) ) {
				$term = $parent;
			} else {
				$term = false;
			}
		}
		$root = array_shift( $terms );
		if ( ! isset( $all_terms[ $root->term_id ] ) ) {
			$children = [];
			if ( ! $terms ) {
				$children[] = [
					'id' => $root->term_id,
				    'text' => $root->name,
				];
			}
			$all_terms[ $root->term_id ] = [
				'text' => $root->name,
			    'children' => $children,
			];
		}
		$label = [];
		$id = 0;
		foreach ( $terms as $t ) {
			$label[] = $t->name;
			$id = $t->term_id;
		}
		if ( $id ) {
			$all_terms[ $root->term_id ]['children'][] = [
				'id'   => $id,
				'text' => implode( ' > ', $label ),
			];
		}
	}
	return [
		'results' => array_values( $all_terms ),
		'total' => $total,
	];
}
