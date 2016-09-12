<?php

/**
 * Register setting field
 */
add_action( 'admin_init', function(){
	// Add setting section to writing.
	add_settings_section(
		'sg_setting_section',
		__( 'Stringy Gum Setting', 'sg' ),
		function(){
			printf(
				'<p class="description">%s</p>',
				__( 'Turn checkbox on if it is huge taxonomy.', 'sg' )
			);
		},
		'writing'
	);

	// Add field to section
	add_settings_field(
		'stringy_gum_taxonomy',
		__( 'Huge Taxonomies', 'sg' ),
		function(){
			$taxonomies = get_taxonomies( [
				'hierarchical' => true,
			], OBJECT );
			foreach ( $taxonomies as $taxonomy ) {
				printf(
					'<label class="sg-block-label"><input name="stringy_gum_taxonomy[]" type="checkbox" value="%s" %s/> %s</label>  ',
					esc_attr( $taxonomy->name ),
					checked( sg_is_huge( $taxonomy->name ), true, false ),
					esc_html( $taxonomy->label )
				);
			}
		},
		'writing',
		'sg_setting_section'
	);

	// 新しい設定が $_POST で扱われ、コールバック関数が <input> を
	// echo できるように、新しい設定を登録
	register_setting( 'writing', 'stringy_gum_taxonomy' );
} );
