<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );




function pwwp_enqueue_my_styles() {
    wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' );

}
add_action('wp_enqueue_scripts', 'pwwp_enqueue_my_styles');

function my_custom_function(){
    ?>
    <script>
    	window.onscroll = function() {myFunction()};
		// Get the header
		var header = document.getElementById( "sticky_header" );
		// Get the offset position of the navbar
		var sticky = header.offsetTop;
		// Add the sticky class to the header when you reach its scroll position. Remove "sticky" when you leave the scroll position
		function myFunction() {
		  if ( window.pageYOffset > 40 ) {		    
			header.classList.add( "hfe-sticky" );		  		
		  } else {
		  	setTimeout(function(){
				header.classList.remove( "hfe-sticky" );		  		
		  	}, 100);		    
		  }
		}
    </script>
    <?php
}
add_action('wp_footer', 'my_custom_function');

