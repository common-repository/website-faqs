<?php
/*
Plugin Name: Website FAQ (Responsive and Categorized with Shortcode)
Plugin URL: #
Description: Useful and handy FAQ plugin for any WordPress website.
Version: 1.2
Author: Nikunj Soni
Author URI: #
Contributors: Nikunj Soni
*/

function website_faq_setup_post_types() {

	$website_faq_labels =  apply_filters( 'website_faq_labels', array(
		'name'                	=> 'FAQ',
		'singular_name'       	=> 'FAQ',
		'add_new'             	=> __('Add New', 'website_faq'),
		'add_new_item'        	=> __('Add New FAQ', 'website_faq'),
		'edit_item'           	=> __('Edit FAQ', 'website_faq'),
		'new_item'            	=> __('New FAQ', 'website_faq'),
		'all_items'           	=> __('All FAQ', 'website_faq'),
		'view_item'           	=> __('View FAQ', 'website_faq'),
		'search_items'        	=> __('Search FAQ', 'website_faq'),
		'not_found'           	=> __('No FAQ found', 'website_faq'),
		'not_found_in_trash'  	=> __('No FAQ found in Trash', 'website_faq'),
		'parent_item_colon'   	=> '',
		'menu_name'           	=> __('FAQ', 'website_faq'),
		'exclude_from_search' 	=> true
	) );

	$faq_args = array(
		'labels' 				=> $website_faq_labels,
		'public' 				=> true,
		'publicly_queryable'	=> true,
		'show_ui' 				=> true,
		'show_in_menu' 			=> true,
		'query_var' 			=> true,
		'capability_type' 		=> 'post',
		'has_archive' 			=> true,
		'hierarchical' 			=> false,
		'supports'				=> array('title','editor'),
		'taxonomies'			=> array('category', 'post_tag')
	);
	register_post_type( 'website_faq', apply_filters( 'website_faq_post_type_args', $faq_args ) );

}
add_action('init', 'website_faq_setup_post_types');

/*
 * Add [website_faq] shortcode.
 * You can also get latest 5 FAQ by using [website_faq limit=5] shortcode.
 *
 */
function website_faq_shortcode_html( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		"limit" => '',
		"category" => '',
	), $atts));
	
	
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	
	if( $category ) { 
		$cat = $category; 
	} else {
		$cat = '';
	}
	
	ob_start();
				
	$website_faq_query = new WP_Query( array ( 
								'post_type'			=>	'website_faq',
								'post_status'		=>	'publish',
								'orderby'			=>	'post_date',
								'order'				=>	'DESC',
								'cat'				=>	$cat,
								'posts_per_page'	=>	$posts_per_page,
								'no_found_rows'		=>	1
								) 
							);
							
	$post_count = $website_faq_query->post_count;
	
		
	if( $post_count > 0) { ?>
	<div class="accordion">
		<?php
			while ($website_faq_query->have_posts()){
			$website_faq_query->the_post();
		?>
			<h4><?php echo get_the_title(); ?></h4>
			<div><?php the_content(); ?></div>
		<?php
			}	
		?>
	</div>
	<?php
	}
	wp_reset_query(); 
	
	return ob_get_clean();
}
add_shortcode("website_faq", "website_faq_shortcode_html");

wp_register_style( 'website-faq-style', plugin_dir_url( __FILE__ ) . 'css/jqueryui-css.css' );
wp_register_script( 'awebsite-faq-script', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.js', array( 'jquery' ) );	

wp_enqueue_style( 'website-faq-style' );
wp_enqueue_script( 'awebsite-faq-script' );

function website_faq_script() {
?>
	<script type="text/javascript">
		jQuery( ".accordion" ).accordion({
			heightStyle: "content"
		});
	</script>
<?php
}
add_action('wp_footer', 'website_faq_script'); 