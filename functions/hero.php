<?php
/**
 * Register Hero CPT
 */
function thd_register_hero_cpt() {
    $labels = array(
        'name'               => _x( 'Heroes', 'post type general name', 'thd' ),
        'singular_name'      => _x( 'Hero', 'post type singular name', 'thd' ),
        'menu_name'          => _x( 'Heroes', 'admin menu', 'thd' ),
        'name_admin_bar'     => _x( 'Hero', 'add new on admin bar', 'thd' ),
        'add_new'            => _x( 'Add New', 'hero', 'thd' ),
        'add_new_item'       => __( 'Add New Hero', 'thd' ),
        'new_item'           => __( 'New Hero', 'thd' ),
        'edit_item'          => __( 'Edit Hero', 'thd' ),
        'view_item'          => __( 'View Hero', 'thd' ),
        'all_items'          => __( 'All Heroes', 'thd' ),
        'search_items'       => __( 'Search Heroes', 'thd' ),
        'parent_item_colon'  => __( 'Parent Heroes:', 'thd' ),
        'not_found'          => __( 'No heroes found.', 'thd' ),
        'not_found_in_trash' => __( 'No heroes found in Trash.', 'thd' )
    );
	
	$args = array(
		'labels'	=> $labels,
		'public'	=> true,
		'show_ui'	=> true,
		'menu_icon'	=> 'dashicons-images-alt',
		'supports'	=> array( 'title' ),
		'taxonomies'=> array( 'category' )
	);
	
	register_post_type( 'hero', $args );
}
add_action( 'init', 'thd_register_hero_cpt' );

/**
 * Get Hero Post Object
 */
function thd_get_hero( $get_archive = false, $use_default = false ) {
	// get the post in case all of our querying fails
	global $post;
	
	// set a default in case all our conditions fail
	$meta_query = $meta_query_default = array(
		'key'	=> 'relationship',
		'value'	=> 'default'
	);
	
	$tax_query = array();
	
	// if all runs have failed, we'll just use the default. else:
	if ( false === $use_default ) {
		// if the first run has failed and we're looking for archive defaults
		if ( true === $get_archive ) {
			if ( is_category() ) {
				$meta_query = array(
					'key' 	=> 'taxonomy',
					'value'	=> 'category'
				);
			} elseif ( is_tag() ) {
				$meta_query = array(
					'key' 	=> 'taxonomy',
					'value'	=> 'post_tag'
				);
			//--- insert custom post type and taxonomy archives here
			} elseif ( is_archive() ) {
				$meta_query = array(
					'key' 	=> 'relationship',
					'value'	=> 'archive'
				);
			}
		// first run
		} else {
			if ( is_home() ) {
				$meta_query = array(
					'key' 	=> 'relationship',
					'value'	=> 'home'
				);
			} elseif ( is_404() ) {
				$meta_query = array(
					'key'	=> 'relationship',
					'value'	=> '404'
				);
			} elseif ( is_search() ) {
				$meta_query = array(
					'key'	=> 'relationship',
					'value'	=> 'search'
				);
			} elseif ( is_category() ) {
				$meta_query = array();
				$tax_query = array(
					'taxonomy'	=> 'category',
					'field'		=> 'term_id',
					'terms'		=> get_queried_object()->term_id
				);
			} elseif ( is_tag() ) {
				$meta_query = array();
				$tax_query = array(
					'taxonomy'	=> 'post_tag',
					'field'		=> 'term_id',
					'terms'		=> get_queried_object()->term_id
				);
			}
			//--- insert custom taxonomy terms here
		}
	}
	
	// get that hero
	$args = array(
		'posts_per_page'	=> 1,
		'post_type'			=> 'hero'
	);
	
	if ( false === empty( $meta_query ) ) {
		$args['meta_query'] = array( $meta_query );
	}
	
	if ( false === empty( $tax_query ) ) {
		$args['tax_query'] = array( $tax_query );
	}
	
	$post_array = get_posts( $args );
	
	// if there's no hero no matter what we try, just return nothing
	if ( true === empty( $post_array ) && true === $get_archive && true === $use_default ) {
		$post = false;
	}
	// if there's no hero and we're looking for an archive, just get the default
	elseif ( true === empty( $post_array ) && true === $get_archive ) {
		$post = thd_get_hero( true, true );
	}
	// if there's a hero, use it!
	elseif ( false === empty( $post_array ) ) {
		$post = $post_array[0];
	}
	// if there's no hero, try again for an archive
	else {
		$post = thd_get_hero( true );
	}
	
	// return the post object
	return $post;
}

/**
 * Returns hero contrast class for body_class()
 */
function thd_hero_contrast() {
	global $post;
	
	if ( false === is_singular() ) {
		$post = thd_get_hero();
	}

	return 'hero_' . get_field( 'hero_contrast', $post->ID );
}

/**
 * Hero Title
 */
function thd_hero_title() {	
	// hero title trumps it all
	if ( $hero_title = get_field( 'hero_title' ) ) {
		$title = $hero_title;
	} else {
	
		// get post title
		$title = get_the_title();
		
		// get index titles
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_author() ) {
			$object = get_queried_object();
			$title = $object->data->display_name;
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} elseif ( is_day() ) {
			$title = get_the_time( 'F j, Y' );
		} elseif ( is_month() ) {
			$title = get_the_time( 'F Y' );
		} elseif ( is_year() ) {
			$title = get_the_time( 'Y' );
		} elseif ( is_post_type_archive() ) {
			global $wp_query;
			$label = $wp_query->queried_object->label;
			$custom	= get_field( strtolower( $label ) . '_page_title', 'option' );
			$title = $custom ? $custom : $label;
		} elseif ( is_search() ) {
			$title = '<span>' . __( 'Search For', 'ut7' ) . ':</span> ' . get_search_query( );
		} elseif ( is_404() ) {
			$title = '404';
		}
	}
		
	echo $title;	
}

/**
 * Hero Subtitle
 */
function thd_hero_subtitle( $post ) {
	if ( false === is_single() ) {
		the_field( 'hero_subtitle', $post->ID );
	} else {
		the_time( 'F j, Y' );
		echo ' &nbsp; &nbsp; &nbsp; ';
		the_category( ', ' );
	}
}

/**
 * Hero Style
 */
function thd_hero_style( $post ) {
	$background_color		= get_field( 'hero_background_color', $post->ID );
	$image_repeat			= get_field( 'hero_image_repeat', $post->ID );
	$image					= get_field( 'hero_image', $post->ID );
	$mobile_image_position	= get_field( 'hero_mobile_image_position', $post->ID );
	$mobile_image_size		= get_field( 'hero_mobile_image_size', $post->ID );
	$desktop_image_position	= get_field( 'hero_desktop_image_position', $post->ID );
	$desktop_image_size		= get_field( 'hero_desktop_image_size', $post->ID );
	$desktop_image_size		= get_field( 'hero_desktop_image_size', $post->ID );
	
	$style = '<style type="text/css">.hero {';
	
	$style .= 'background-color:' . ( $background_color ? $background_color : '#E0E6E8' ) . ';';
	$style .= 'background-image:url(' . $image . ');';
	$style .= 'background-position:' . $mobile_image_position . ';';
	$style .= 'background-size:' . $mobile_image_size . ';';
	$style .= 'background-repeat:' . $image_repeat . ';';
	
	$style .= '}@media (min-width:900px) {.hero {';
	
	$style .= 'background-position:' . $desktop_image_position . ';';
	$style .= 'background-size:' . $desktop_image_size . ';';
	
	$style .= '}}';
	
	$style .= '</style>';
	
	echo $style;
}