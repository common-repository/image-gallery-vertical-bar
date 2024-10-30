<?php
/*
Plugin Name: Image gallery vertical bar
Plugin URL: http://beautiful-module.com/demo/image-gallery-vertical-bar/
Description: A simple Responsive Image gallery vertical bar
Version: 1.0
Author: Module Express
Author URI: http://beautiful-module.com
Contributors: Module Express
*/
/*
 * Register CPT sp_gallery.vertical.bar
 *
 */
if(!class_exists('Image_Gallery_Vertical_Bar')) {
	class Image_Gallery_Vertical_Bar {

		function __construct() {
		    if(!function_exists('add_shortcode')) {
		            return;
		    }
			add_action ( 'init' , array( $this , 'igvb_responsive_gallery_setup_post_types' ));

			/* Include style and script */
			add_action ( 'wp_enqueue_scripts' , array( $this , 'igvb_register_style_script' ));
			
			/* Register Taxonomy */
			add_action ( 'init' , array( $this , 'igvb_responsive_gallery_taxonomies' ));
			add_action ( 'add_meta_boxes' , array( $this , 'igvb_rsris_add_meta_box_gallery' ));
			add_action ( 'save_post' , array( $this , 'igvb_rsris_save_meta_box_data_gallery' ));
			register_activation_hook( __FILE__, 'igvb_responsive_gallery_rewrite_flush' );


			// Manage Category Shortcode Columns
			add_filter ( 'manage_responsive_igvb_slider-category_custom_column' , array( $this , 'igvb_responsive_gallery_category_columns' ), 10, 3);
			add_filter ( 'manage_edit-responsive_igvb_slider-category_columns' , array( $this , 'igvb_responsive_gallery_category_manage_columns' ));
			require_once( 'igvb_gallery_admin_settings_center.php' );
		    add_shortcode ( 'sp_gallery.vertical.bar' , array( $this , 'igvb_responsivegallery_shortcode' ));
		}


		function igvb_responsive_gallery_setup_post_types() {

			$responsive_gallery_labels =  apply_filters( 'gallery_vertical_bar_labels', array(
				'name'                => 'Responsive header image gallery',
				'singular_name'       => 'Responsive header image gallery',
				'add_new'             => __('Add New', 'gallery_vertical_bar'),
				'add_new_item'        => __('Add New Image', 'gallery_vertical_bar'),
				'edit_item'           => __('Edit Image', 'gallery_vertical_bar'),
				'new_item'            => __('New Image', 'gallery_vertical_bar'),
				'all_items'           => __('All Image', 'gallery_vertical_bar'),
				'view_item'           => __('View Image', 'gallery_vertical_bar'),
				'search_items'        => __('Search Image', 'gallery_vertical_bar'),
				'not_found'           => __('No Image found', 'gallery_vertical_bar'),
				'not_found_in_trash'  => __('No Image found in Trash', 'gallery_vertical_bar'),
				'parent_item_colon'   => '',
				'menu_name'           => __('Image gallery vertical bar', 'gallery_vertical_bar'),
				'exclude_from_search' => true
			) );


			$responsiveslider_args = array(
				'labels' 			=> $responsive_gallery_labels,
				'public' 			=> true,
				'publicly_queryable'		=> true,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true,
				'query_var' 		=> true,
				'capability_type' 	=> 'post',
				'has_archive' 		=> true,
				'hierarchical' 		=> false,
				'menu_icon'   => 'dashicons-format-gallery',
				'supports' => array('title','editor','thumbnail')
				
			);
			register_post_type( 'gallery_vertical_bar', apply_filters( 'sp_faq_post_type_args', $responsiveslider_args ) );

		}
		
		function igvb_register_style_script() {
		    wp_enqueue_style( 'verticalbar_responsiveimgslider',  plugin_dir_url( __FILE__ ). 'css/responsiveimgslider.css' );
			/*   REGISTER ALL CSS FOR SITE */
			wp_enqueue_style( 'verticalbar_main',  plugin_dir_url( __FILE__ ). 'css/verticalbar.css' );

			/*   REGISTER ALL JS FOR SITE */			
			wp_enqueue_script( 'verticalbar_jssor.core', plugin_dir_url( __FILE__ ) . 'js/jssor.core.js', array( 'jquery' ));
			wp_enqueue_script( 'verticalbar_jssor.utils', plugin_dir_url( __FILE__ ) . 'js/jssor.utils.js', array( 'jquery' ));
			wp_enqueue_script( 'verticalbar_jssor.slider', plugin_dir_url( __FILE__ ) . 'js/jssor.slider.js', array( 'jquery' ));
			
		}
		
		
		function igvb_responsive_gallery_taxonomies() {
		    $labels = array(
		        'name'              => _x( 'Category', 'taxonomy general name' ),
		        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		        'search_items'      => __( 'Search Category' ),
		        'all_items'         => __( 'All Category' ),
		        'parent_item'       => __( 'Parent Category' ),
		        'parent_item_colon' => __( 'Parent Category:' ),
		        'edit_item'         => __( 'Edit Category' ),
		        'update_item'       => __( 'Update Category' ),
		        'add_new_item'      => __( 'Add New Category' ),
		        'new_item_name'     => __( 'New Category Name' ),
		        'menu_name'         => __( 'Gallery Category' ),
		    );

		    $args = array(
		        'hierarchical'      => true,
		        'labels'            => $labels,
		        'show_ui'           => true,
		        'show_admin_column' => true,
		        'query_var'         => true,
		        'rewrite'           => array( 'slug' => 'responsive_igvb_slider-category' ),
		    );

		    register_taxonomy( 'responsive_igvb_slider-category', array( 'gallery_vertical_bar' ), $args );
		}

		function igvb_responsive_gallery_rewrite_flush() {  
				igvb_responsive_gallery_setup_post_types();
		    flush_rewrite_rules();
		}


		function igvb_responsive_gallery_category_manage_columns($theme_columns) {
		    $new_columns = array(
		            'cb' => '<input type="checkbox" />',
		            'name' => __('Name'),
		            'gallery_vertical_shortcode' => __( 'Gallery Category Shortcode', 'vertical_slick_slider' ),
		            'slug' => __('Slug'),
		            'posts' => __('Posts')
					);

		    return $new_columns;
		}

		function igvb_responsive_gallery_category_columns($out, $column_name, $theme_id) {
		    $theme = get_term($theme_id, 'responsive_igvb_slider-category');

		    switch ($column_name) {      
		        case 'title':
		            echo get_the_title();
		        break;
		        case 'gallery_vertical_shortcode':
					echo '[sp_gallery.vertical.bar cat_id="' . $theme_id. '"]';			  	  

		        break;
		        default:
		            break;
		    }
		    return $out;   

		}

		/* Custom meta box for slider link */
		function igvb_rsris_add_meta_box_gallery() {
			add_meta_box('custom-metabox',__( 'LINK URL', 'link_textdomain' ),array( $this , 'igvb_rsris_gallery_box_callback' ),'gallery_vertical_bar');			
		}
		
		function igvb_rsris_gallery_box_callback( $post ) {
			wp_nonce_field( 'igvb_rsris_save_meta_box_data_gallery', 'rsris_meta_box_nonce' );
			$value = get_post_meta( $post->ID, 'rsris_slide_link', true );
			echo '<input type="url" id="rsris_slide_link" name="rsris_slide_link" value="' . esc_attr( $value ) . '" size="25" /><br />';
			echo 'ie http://www.google.com';
		}
		
		function igvb_rsris_save_meta_box_data_gallery( $post_id ) {
			if ( ! isset( $_POST['rsris_meta_box_nonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_POST['rsris_meta_box_nonce'], 'igvb_rsris_save_meta_box_data_gallery' ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( isset( $_POST['post_type'] ) && 'gallery_vertical_bar' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}
			if ( ! isset( $_POST['rsris_slide_link'] ) ) {
				return;
			}
			$link_data = sanitize_text_field( $_POST['rsris_slide_link'] );
			update_post_meta( $post_id, 'rsris_slide_link', $link_data );
		}
		
		/*
		 * Add [sp_gallery.vertical.bar] shortcode
		 *
		 */
		function igvb_responsivegallery_shortcode( $atts, $content = null ) {
			
			extract(shortcode_atts(array(
				"limit"  => '',
				"cat_id" => '',
				"autoplay" => '',
				"autoplay_interval" => ''
			), $atts));
			
			if( $limit ) { 
				$posts_per_page = $limit; 
			} else {
				$posts_per_page = '-1';
			}
			if( $cat_id ) { 
				$cat = $cat_id; 
			} else {
				$cat = '';
			}
			
			if( $autoplay ) { 
				$autoplay_slider = $autoplay; 
			} else {
				$autoplay_slider = 'true';
			}	 	
			
			if( $autoplay_interval ) { 
				$autoplay_intervalslider = $autoplay_interval; 
			} else {
				$autoplay_intervalslider = '4000';
			}
						

			ob_start();
			// Create the Query
			$post_type 		= 'gallery_vertical_bar';
			$orderby 		= 'post_date';
			$order 			= 'DESC';
						
			 $args = array ( 
		            'post_type'      => $post_type, 
		            'orderby'        => $orderby, 
		            'order'          => $order,
		            'posts_per_page' => $posts_per_page,  
		           
		            );
			if($cat != ""){
		            	$args['tax_query'] = array( array( 'taxonomy' => 'responsive_igvb_slider-category', 'field' => 'id', 'terms' => $cat) );
		            }        
		      $query = new WP_Query($args);

			$post_count = $query->post_count;
			$i = 1;

			if( $post_count > 0) :
			?>
				<div id="vertical_slider1_container" style="position: relative; top: 0px; left: 0px; width: 960px;
					height: 480px; background: #191919; overflow: hidden;">

					<!-- Loading Screen -->
					<div u="loading" style="position: absolute; top: 0px; left: 0px;">
						<div style="filter: alpha(opacity=70); opacity:0.7; position: absolute; display: block;
							background-color: #000000; top: 0px; left: 0px;width: 100%;height:100%;">
						</div>
						<div class="exc-loading-container">
						</div>
					</div>

					<!-- Slides Container -->
					<div u="slides" style="cursor: move; position: absolute; left: 240px; top: 0px; width: 720px; height: 480px; overflow: hidden;">
						<?php								
							while ($query->have_posts()) : $query->the_post();
								include('designs/design-1.php');
								
							$i++;
							endwhile;									
						?>	
					</div>
					
					<!-- Direction Navigator Skin Begin -->
					<span u="arrowleft" class="jssord05l" style="width: 40px; height: 40px; top: 158px; left: 248px;">
					</span>
					<span u="arrowright" class="jssord05r" style="width: 40px; height: 40px; top: 158px; right: 8px">
					</span>
					<!-- Direction Navigator Skin End -->
					
					<!-- Thumbnail Navigator Skin 02 Begin -->
					<div u="thumbnavigator" class="jssort02" style="position: absolute; width: 240px; height: 480px; left:0px; bottom: 0px;">					
						<!-- Thumbnail Item Skin Begin -->
						<div u="slides" style="cursor: move;">
							<div u="prototype" class="p" style="position: absolute; width: 101px; height: 68px; top: 0; left: 0;">
								<div class=w><thumbnailtemplate style=" width: 100%; height: 100%; border: none;position:absolute; top: 0; left: 0;"></thumbnailtemplate></div>
								<div class=c>
								</div>
							</div>
						</div>
						<!-- Thumbnail Item Skin End -->
					</div>
				</div>
	
				<?php
				endif;
				// Reset query to prevent conflicts
				wp_reset_query();
			?>							
			<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var _SlideshowTransitions = [
				//Zoom- in
				{$Duration: 1200, $Zoom: 1, $Easing: { $Zoom: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseOutQuad }, $Opacity: 2 },
				//Zoom+ out
				{$Duration: 1000, $Zoom: 11, $SlideOut: true, $Easing: { $Zoom: $JssorEasing$.$EaseInExpo, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 },
				//Rotate Zoom- in
				{$Duration: 1200, $Zoom: 1, $Rotate: true, $During: { $Zoom: [0.2, 0.8], $Rotate: [0.2, 0.8] }, $Easing: { $Zoom: $JssorEasing$.$EaseSwing, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseSwing }, $Opacity: 2, $Round: { $Rotate: 0.5} },
				//Rotate Zoom+ out
				{$Duration: 1000, $Zoom: 11, $Rotate: true, $SlideOut: true, $Easing: { $Zoom: $JssorEasing$.$EaseInExpo, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInExpo }, $Opacity: 2, $Round: { $Rotate: 0.8} },

				//Zoom HDouble- in
				{$Duration: 1200, $Cols: 2, $Zoom: 1, $FlyDirection: 1, $Assembly: 2049, $ChessMode: { $Column: 15 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Zoom: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.5, $Opacity: 2 },
				//Zoom HDouble+ out
				{$Duration: 1200, $Cols: 2, $Zoom: 11, $SlideOut: true, $FlyDirection: 1, $Assembly: 2049, $ChessMode: { $Column: 15 }, $Easing: { $Left: $JssorEasing$.$EaseInExpo, $Zoom: $JssorEasing$.$EaseInExpo, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 4, $Opacity: 2 },

				//Rotate Zoom- in L
				{$Duration: 1200, $Zoom: 1, $Rotate: true, $During: { $Left: [0.2, 0.8], $Zoom: [0.2, 0.8], $Rotate: [0.2, 0.8] }, $FlyDirection: 1, $Easing: { $Left: $JssorEasing$.$EaseSwing, $Zoom: $JssorEasing$.$EaseSwing, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseSwing }, $ScaleHorizontal: 0.6, $Opacity: 2, $Round: { $Rotate: 0.5} },
				//Rotate Zoom+ out R
				{$Duration: 1000, $Zoom: 11, $Rotate: true, $SlideOut: true, $FlyDirection: 2, $Easing: { $Left: $JssorEasing$.$EaseInExpo, $Zoom: $JssorEasing$.$EaseInExpo, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInExpo }, $ScaleHorizontal: 4, $Opacity: 2, $Round: { $Rotate: 0.8} },
				//Rotate Zoom- in R
				{$Duration: 1200, $Zoom: 1, $Rotate: true, $During: { $Left: [0.2, 0.8], $Zoom: [0.2, 0.8], $Rotate: [0.2, 0.8] }, $FlyDirection: 2, $Easing: { $Left: $JssorEasing$.$EaseSwing, $Zoom: $JssorEasing$.$EaseSwing, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseSwing }, $ScaleHorizontal: 0.6, $Opacity: 2, $Round: { $Rotate: 0.5} },
				//Rotate Zoom+ out L
				{$Duration: 1000, $Zoom: 11, $Rotate: true, $SlideOut: true, $FlyDirection: 1, $Easing: { $Left: $JssorEasing$.$EaseInExpo, $Zoom: $JssorEasing$.$EaseInExpo, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInExpo }, $ScaleHorizontal: 4, $Opacity: 2, $Round: { $Rotate: 0.8} },

				//Rotate HDouble- in
				{$Duration: 1200, $Cols: 2, $Zoom: 1, $Rotate: true, $FlyDirection: 5, $Assembly: 2049, $ChessMode: { $Column: 15 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Top: $JssorEasing$.$EaseInCubic, $Zoom: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseOutQuad, $Rotate: $JssorEasing$.$EaseInCubic }, $ScaleHorizontal: 0.5, $ScaleVertical: 0.3, $Opacity: 2, $Round: { $Rotate: 0.7} },
				//Rotate HDouble- out
				{$Duration: 1000, $Cols: 2, $Zoom: 1, $Rotate: true, $SlideOut: true, $FlyDirection: 5, $Assembly: 2049, $ChessMode: { $Column: 15 }, $Easing: { $Left: $JssorEasing$.$EaseInExpo, $Top: $JssorEasing$.$EaseInExpo, $Zoom: $JssorEasing$.$EaseInExpo, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInExpo }, $ScaleHorizontal: 0.5, $ScaleVertical: 0.3, $Opacity: 2, $Round: { $Rotate: 0.7} },
				//Rotate VFork in
				{$Duration: 1200, $Rows: 2, $Zoom: 11, $Rotate: true, $FlyDirection: 6, $Assembly: 2049, $ChessMode: { $Row: 28 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Top: $JssorEasing$.$EaseInCubic, $Zoom: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseOutQuad, $Rotate: $JssorEasing$.$EaseInCubic }, $ScaleHorizontal: 4, $ScaleVertical: 2, $Opacity: 2, $Round: { $Rotate: 0.7} },
				//Rotate HFork in
				{$Duration: 1200, $Cols: 2, $Zoom: 11, $Rotate: true, $FlyDirection: 5, $Assembly: 2049, $ChessMode: { $Column: 19 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Top: $JssorEasing$.$EaseInCubic, $Zoom: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseOutQuad, $Rotate: $JssorEasing$.$EaseInCubic }, $ScaleHorizontal: 1, $ScaleVertical: 2, $Opacity: 2, $Round: { $Rotate: 0.8} }
				];

				var options = {
					$AutoPlay: <?php if($autoplay_slider == "false") { echo 'false';} else { echo 'true'; } ?>,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
					$AutoPlayInterval: <?php echo $autoplay_intervalslider; ?>,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
					$PauseOnHover: 3,                                //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, default value is 3

					$DragOrientation: 3,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)
					$ArrowKeyNavigation: true,   			            //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
					$SlideDuration: 600,                                //Specifies default duration (swipe) for slide in milliseconds

					$SlideshowOptions: {                                //[Optional] Options to specify and enable slideshow or not
						$Class: $JssorSlideshowRunner$,                 //[Required] Class to create instance of slideshow
						$Transitions: _SlideshowTransitions,            //[Required] An array of slideshow transitions to play slideshow
						$TransitionsOrder: 1,                           //[Optional] The way to choose transition to play slide, 1 Sequence, 0 Random
						$ShowLink: true                                    //[Optional] Whether to bring slide link on top of the slider when slideshow is running, default value is false
					},

					$DirectionNavigatorOptions: {                       //[Optional] Options to specify and enable direction navigator or not
						$Class: $JssorDirectionNavigator$,              //[Requried] Class to create direction navigator instance
						$ChanceToShow: 1,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
						$AutoCenter: 2,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
						$Steps: 1                                       //[Optional] Steps to go for each navigation request, default value is 1
					},

					$ThumbnailNavigatorOptions: {                       //[Optional] Options to specify and enable thumbnail navigator or not
						$Class: $JssorThumbnailNavigator$,              //[Required] Class to create thumbnail navigator instance
						$ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always

						$ActionMode: 1,                                 //[Optional] 0 None, 1 act by click, 2 act by mouse hover, 3 both, default value is 1
						$Lanes: 2,                                      //[Optional] Specify lanes to arrange thumbnails, default value is 1
						$SpacingX: 12,                                   //[Optional] Horizontal space between each thumbnail in pixel, default value is 0
						$SpacingY: 10,                                   //[Optional] Vertical space between each thumbnail in pixel, default value is 0
						$DisplayPieces: 6,                             //[Optional] Number of pieces to display, default value is 1
						$ParkingPosition: 156,                          //[Optional] The offset position to park thumbnail
						$Orientation: 2                                //[Optional] Orientation to arrange thumbnails, 1 horizental, 2 vertical, default value is 1
					}
				};

				var jssor_slider1 = new $JssorSlider$("vertical_slider1_container", options);
				//responsive code begin
				//you can remove responsive code if you don't want the slider scales while window resizes
				function ScaleSlider() {
					var parentWidth = jssor_slider1.$Elmt.parentNode.clientWidth;
					if (parentWidth)
						jssor_slider1.$SetScaleWidth(Math.max(Math.min(parentWidth, 960), 300));
					else
						window.setTimeout(ScaleSlider, 30);
				}

				ScaleSlider();

				if (!navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|IEMobile)/)) {
					$(window).bind('resize', ScaleSlider);
				}
				//responsive code end
			});
			</script>
			<?php
			return ob_get_clean();
		}		
	}
}
	
function igvb_master_gallery_images_load() {
        global $mfpd;
        $mfpd = new Image_Gallery_Vertical_Bar();
}
add_action( 'plugins_loaded', 'igvb_master_gallery_images_load' );