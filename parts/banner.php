<?php 

$single_post = new Single_Post_Ignite( get_the_ID() );

$settings = $single_post->get_settings();

if ( is_front_page() && ! empty( $single_post->get_post_image('full') ) ){
	
	$banner_img = $single_post->get_post_image('full');
	
	include CAHNRSIGNITEPATH . 'includes/banners/static-image-wide.php';
	
} // end if

?>