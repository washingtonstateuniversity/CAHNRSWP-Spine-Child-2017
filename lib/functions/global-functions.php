<?php
/*
* ignite_get_post_image( $post_id, $size = 'full' )
* ignite_get_post_image_array( $post_id )
* ignite_get_theme_path( $subdirectory = '' )
* ignite_get_theme_url( $subdirectory = '' )
* ignite_get_part( $subdirectory )
* ignite_load_class( $subdirectory, $include_once = false )
* ignite_get_slides( $args = array() )
* ignite_get_terms( $taxonomy, $include_empty = true, $as_select = true, $include_empty = true )
* ignite_get_sidebar( $sidebar_id, $args = array() )
* the_ignite_theme_header( $context = 'single', $args = array(), $echo = true )
* ignite_check_active_post_type( $post_type )
* ignite_get_sidebar( $id );
*
* -------------- DEPRECATED --------------------
* the_ignite_theme_footer Use ignite_get_part
* get_after_content_sidebar_ignite() Use ignite_get_sidebar( $sidebar_id, $args = array() )
*
*/

/*
* Get the image data from a given post id
* @param int $post_id ID for given post
* @param string $size Size of image to be used (full,large,medium,small, or custom)
* @return array Array of image data
*/
function ignite_get_post_image( $post_id, $size = 'full' ){
	
	$image = array();
		
	if ( $post_id && has_post_thumbnail( $post_id ) ){

		$img_id = get_post_thumbnail_id( $post_id );

		$img_url_array = wp_get_attachment_image_src( $img_id, $size, true );
		
		$image['id'] = $img_id;

		$image['src'] = $img_url_array[0];
		
		$image['alt'] = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

	} // End if

	return $image;
	
} // End ignite_get_post_image

function ignite_get_post_image_array( $post_id ){
	
	$sizes = array('thumbnail','medium','large','full');
	
	$images = array();
		
	if ( $post_id && has_post_thumbnail( $post_id ) ){
		
		$img_id = get_post_thumbnail_id( $post_id );
		
		foreach( $sizes as $size ){
			
			$img_url_array = wp_get_attachment_image_src( $img_id, $size, true );
			
			$image = array(
				'id' 	=> $img_id,
				'src' 	=> $img_url_array[0],
				'alt' 	=> get_post_meta( $img_id, '_wp_attachment_image_alt', true ),
			);
			
			$images[$size] = $image;
			
		} // End foreach	

	} // End if

	return $images;
	
} // End ignite_get_post_image

/*
* Get the plugin directory path with added optional subdirectory
* @param string $subdirectory Subdirectory to add to base path
* @return string Full path
*/
function ignite_get_theme_path( $subdirectory = '' ){
	
	$path = CAHNRSIGNITEPATH;
	
	if ( ! empty( $subdirectory ) ){
		
		$path .= $subdirectory;
		
	}
	
	return $path;
	
} // End ignite_get_theme_path

/*
* Get the plugin directory url with added optional subdirectory
* @param string $subdirectory Subdirectory to add to base url
* @return string Full url
*/
function ignite_get_theme_url( $subdirectory = '' ){
	
	$url = CAHNRSIGNITEURL;
	
	if ( ! empty( $subdirectory ) ){
		
		$url .= $subdirectory;
		
	}
	
	return $url;
	
} // End ignite_get_theme_url

/*
* Get path to theme part
* @param string $subdirectory Subdirectory of the class
* @return string Path to file
*/
function ignite_get_part( $part, $context = 'single', $args = array(), $echo = true ){
	
	$html = '';
	
	ignite_load_class('classes/class-abstract-theme-part-ignite.php', true );
	
	switch ( $part ){
			
		case 'header':
			ignite_load_class('lib/theme-parts/header/class-header-ignite.php', true );
			$header = new Header_Ignite();
			$html .= $header->get_part( $context, $args );
			break;
			
		case 'footer':
			ignite_load_class('lib/theme-parts/footer/class-footer-ignite.php', true );
			$footer = new Footer_Ignite();
			$html .= $footer->get_part( $context, $args );
			break;
			
	} // End switch
	
	$html = apply_filters( 'cahnrs_ignite_page_html', $html, $part, $context, $args );
	
	if ( $echo ){
		
		echo $html;
		
	} else {
		
		return $html;
		
	} // End if

} // End ignite_get_class

/*
* Load class from the plugin
* @param string $subdirectory Subdirectory of the class
* @param bool $include_once Use include (default) or include_once
*/
function ignite_load_class( $subdirectory, $include_once = false ){
	
	$path = ignite_get_theme_path( $subdirectory );
	
	if ( $include_once ){
		
		include_once $path;
		
	} else {
	
		include $path;
		
	} // End if

} // End ignite_get_class


function ignite_get_slides( $args = array() ){
	
	include_once ignite_get_theme_path( 'lib/factories/class-slide-factory-ignite.php' );
	
	$slide_factory = new Slide_Factory_Ignite();
	
	$slides = $slide_factory->get_slides( $args );
	
	return $slides;
	
} // End ignite_get_slides


function ignite_get_terms( $taxonomy, $include_empty = true, $as_select = true ){
	
	$return_terms = array();
	
	if ( $include_empty ){
		
		$return_terms[0] = 'No Category';
		
	} // end if
	
	$terms = get_terms( $taxonomy, array( 'hide_empty' => false, ) );
	
	if ( $as_select ){
		
		foreach( $terms as $term ){
			
			$return_terms[ $term->term_id ] = $term->name;
			
		} // End foreach
		
	} else {
		
		$return_terms = $terms;
		
	}// End if
	
	return $return_terms;
	
} // End ignite_get_terms

function ignite_get_sidebar( $sidebar_id, $args = array() ){
	
	$default_args = array(
		'wrap' => 'div',
		'class' => '',
		'id'	=> '',
		'check_active' => true,
	);
	
	$args = array_merge( $default_args, $args );
	
	$sidebar_html = '';
	
	ob_start();
	
	if ( $args['check_active'] ) {
		
		if ( is_active_sidebar( $sidebar_id ) ) {

			dynamic_sidebar( $sidebar_id );
			
		} // End if

	} else {
		
		dynamic_sidebar( $sidebar_id );
		
	} // End if
	
	$sidebar_html .= ob_get_clean();
	
	if ( ! empty( $args['wrap'] ) ){
		
		$html = '<' . $args['wrap'] . ' id="' . $args['id'] . '" class="' . $sidebar_id . '-sidebar ' . $args['class'] . '">' . $sidebar_html .'</' . $args['wrap'] . '>';
		
	} else {
		
		$html = $sidebar_html;
		
	}// End if
	
	return $html;
	
	
} // End ignite_get_sidebar

function get_after_content_sidebar_ignite(){
	
	$html = '';
	
	$html .= ignite_get_sidebar( 'content_after', array('id' => 'content-after-widget-area' ) );
	
	//if ( is_active_sidebar( 'content_after' ) ) {

		//ob_start();

		//dynamic_sidebar( 'content_after' );

		//$html .= '<div id="content-after-widget-area">' . ob_get_clean() .'</div>';

	//} // End if
	
	return $html;
	
} // End get_after_content_sidebar_ignite


function the_ignite_theme_header( $context = 'single', $args = array(), $echo = true ){
	
	ob_start();
	
	include_once CAHNRSIGNITEPATH . 'theme-parts/theme-header/class-theme-header-ignite.php';
	
	$theme_header = new Theme_Header_Ignite();
	
	$theme_header->the_theme_header( 'single' );
	
	$html = apply_filters( 'cahnrs_ignite_page_html', ob_get_clean() );
	
	if ( $echo ){
		
		echo $html;
		
	} else {
		
		return $html;
		
	} // End if
	
} // End get_theme_header


function the_ignite_theme_footer( $context = 'single', $args = array(), $echo = true ){
	
	ob_start();
	
	include_once CAHNRSIGNITEPATH . 'theme-parts/footers/class-footer-ignite.php';
	
	$footer = new Footer_Ignite();
	
	$footer->the_footer( $context );

	include locate_template( 'includes/main/main-end.php', false );

	get_footer();
	
	$html = apply_filters( 'cahnrs_ignite_page_html', ob_get_clean() );
	
	if ( $echo ){
		
		echo $html;
		
	} else {
		
		return $html;
		
	} // End if
	
} // End 


function ignite_check_active_post_type( $post_type ){
		
	return ( get_theme_mod( "_cahnrswp_enable_{$post_type}", false ) ) ? true : false;
	
} // End 


function ignite_get_widget_area( $sidebar_id, $class, $check_active = true ) {
	
	$html = '';
	
	$html .= '<div id="' . $class . '-widget-area widget-area-' . $class . '">' . ignite_get_sidebar( $sidebar_id, array('wrap' => false ) )  .'</div>';
	
	return $html;
	
} // End ignite_get_widget_area

