<?php
/**
* Plugin Name: Advance Reviews For WooCommerce

* Plugin URI: http://www.phoeniixx.com/

* Description: The plugin also lets you present customer ratings in form of Review Rating Bar Graph. 

* Version: 1.1

* Text Domain: parp

* Domain Path: /i18n/languages/

* Author: phoeniixx

* Author URI: http://www.phoeniixx.com/

* License: GPL2
*/ 


if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here
	require_once(ABSPATH . 'wp-settings.php');

	error_reporting(0);
	
	ob_start();
	
	session_start();
	
	require_once('parp_settings.php');
	
	require_once('parp_show_review.php');
	
	//require_once('converted_review.php');
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	$parp_genral_data=get_option('data_gen_adv_review');
	
		if(!empty($parp_genral_data)){
		
			extract($parp_genral_data);
		
		}
	
	global $product;

	add_action('admin_menu', 'parp_advncreview_tab');

		function parp_advncreview_tab(){
			
			$page_title="Advance Reviews";
			
			$menu_title="Advance Reviews";
			
			$capability="manage_options";
			
			$menu_slug="parp-manager";
			
			$plugin_dir_url =  plugin_dir_url( __FILE__ );
			
			add_menu_page( 'phoeniixx', __( 'Phoeniixx', 'phe' ), 'nosuchcapability', 'phoeniixx', NULL, $plugin_dir_url.'/assets/images/logo-wp.png', 57 );
			
			add_submenu_page( 'phoeniixx', $page_title, $menu_title, $capability, $menu_slug ,'parp_settings');

		}
	
	add_action('wp_head', 'parp_assets_file');
	
		function parp_assets_file() {
			
			$parp_genral_set=get_option('data_gen_adv_review');
		
				if(!empty($parp_genral_set)){
					
					extract($parp_genral_set);
				}
			
			?>
			<script>
					var plugin_enable='<?php echo $parp_plugin_enable; ?>';
					var limit_multiple_upload='<?php echo $parp_upload_lmt ;?>';
			</script>
			<?php 
			
			wp_enqueue_style( 'parpp-custom-css', plugin_dir_url( __FILE__ ).'assets/css/parp_custom_css.css' );
			
			wp_enqueue_script( 'media_upload', plugin_dir_url( __FILE__ ) . 'assets/js/media_upload_for_frend.js', array( 'jquery' ) );	
			
			add_action('parp_summary_prepend', array($this, 'add_reviews_average_info'));
			
			add_filter('woocommerce_product_tabs', 'update_tab_reviews_count', 20);
			
			add_filter( 'wc_get_template', 'filter_wc_get_template', 99, 5 );
			
		}
		
		add_action('admin_head','parp_add_admin_assests');

	function parp_add_admin_assests(){
		
			wp_enqueue_script('wp-color-picker'); 
			
			wp_enqueue_style('wp-color-picker');
			
			wp_enqueue_style( 'parp-custom-css', plugin_dir_url( __FILE__ ).'assets/css/parp_custom_css.css' );
			
			wp_enqueue_script( 'advance-ajax-request', plugin_dir_url( __FILE__ ) . 'assets/js/parp_custom.js', array( 'jquery' ) );	
			
			wp_localize_script( 'advance-ajax-request', 'Areview_Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
			
		}

	function  update_tab_reviews_count($tabs) {
            global $product;

            if (isset($tabs['reviews'])) {
                $tabs['reviews']['title'] = sprintf(__('Reviews(%d)', 'parp'), count(get_product_reviews_by_rating($product->id)));
            }

            return $tabs;
        }	
		
		
	function redirect_after_submit_review($location, $comment) {
		
            // Set the new comment as imported so it will not imported when clicking on "convert reviews", creating duplicated entries
            update_comment_meta($comment->comment_ID, 'parp_imported_key', 1);

            return get_permalink($comment->comment_post_ID);
        }
	
	if($parp_plugin_enable==1 && $parp_upload_enable==1){
			
			add_filter('woocommerce_product_review_comment_form_args', 'parp_add_fields_to_comment_form');	
	
	}
	function parp_add_fields_to_comment_form($comment_form) {
			
			$current_content = $comment_form['comment_field'];

            $hide_rating = isset($_REQUEST["replytocom"]) ? "hide-rating" : '';
            
			$selected = isset($_REQUEST["replytocom"]) ? "selected" : '';
	
			$comment_form['comment_field'] .= '<p class="parp_upload_section ' . $hide_rating . '" >
													<label for="uploadFile" > ' . __('Attachments', 'parp') . ' </label >
													<input type = "button" value = "' . __('Choose file(s)', 'parp') . '" id = "do_uploadFile" />
													<input type = "file" name = "uploadFile[]" id = "uploadFile" accept = "image/*" multiple = "2" />
												</p>
												<p>
													<ul id = "uploadFileList" >
													</ul>
												</p> ';
	
		return $comment_form;
	}

	add_action('comment_post', 'parp_submit_review');
	
	function parp_submit_review($comment_id) {

		$parp_genral_datas=get_option('data_gen_adv_review');
			
			if(!empty($parp_genral_datas)){
				
				extract($parp_genral_datas);
			
			}
	
		   if (!isset($_POST)) {
                
				return;
           
		   }

            $review_title =  $parp_sh_ttl && isset($_POST["title"]) ? wp_strip_all_tags($_POST["title"]) : '';

            $post_parent = apply_filters('parp_advanced_reviews_post_parent', $_POST["comment_parent"]);

            $comment = get_comment($comment_id);
			
            // Create post object
            $my_post = array(
               
				'post_author' => $comment->user_id,
                'post_title' => $review_title,
                'post_content' => $comment->comment_content,
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'parp_reviews',
                'post_parent' => $post_parent,
                'review_user_id' => $comment->user_id,
                'review_rating' => (isset($_POST["rating"]) ? $_POST["rating"] : 0),
                'review_product_id' => $comment->comment_post_ID,
                'review_comment_id' => $comment_id,
                'review_approved' => apply_filters('parp_advanced_reviews_approve_new_review', true),
                'review_author' => $comment->comment_author,
                'review_author_email' => $comment->comment_author_email,
                'review_author_IP' => $comment->comment_author_IP,
                'review_author_url' => $comment->comment_author_url
            );

            // Insert the post into the database
            $review_id = insert_review($my_post);
			
            submit_attachments($review_id); 
        }

       function insert_review($args) {
            // Create post object
            $defaults = array(
                'post_title' => '',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 0,
                'post_type' => 'parp_reviews',
                'post_parent' => 0,
                'review_user_id' => 0,
                'review_approved' => 1,
                'review_rating' => 0,
                'review_product_id' => 0,
                'review_comment_id' => 0,
                'review_upvotes' => 0,
                'review_downvotes' => 0,
                'review_votes' => array(),
                'review_inappropriate_list' => array(),
                'review_inappropriate_count' => 0,
                'review_is_featured' => 0,
                'review_is_reply_blocked' => 0,
                'review_thumbnails' => array(),
                'review_author' => '',
                'review_author_email' => '',
                'review_author_url' => '',
                'review_author_IP' => ''
            );

            $args = wp_parse_args($args, $defaults);

            // Insert the post into the database
            $review_id = wp_insert_post($args);

            //  Set rating only for top level reviews, not for replies
            if (0 != $args["post_parent"]) {
               
			   update_post_meta($review_id,'parp_rating', 0);
           
		   } else {
               
			   update_post_meta($review_id, 'parp_rating', $args["review_rating"]);
            
			}

            update_post_meta($review_id, 'parp_rating', $args["review_rating"]);
            update_post_meta($review_id, 'parp_review_apprpved', $args["review_approved"]);
            update_post_meta($review_id, 'parp_product_id', $args["review_product_id"]);
            update_post_meta($review_id, 'parp_comment_id', $args["review_comment_id"]);
            update_post_meta($review_id, 'parp_media', $args["review_thumbnails"]);

            update_post_meta($review_id, 'parp_key_upvotes_count', $args["review_upvotes"]);
            update_post_meta($review_id, 'parp_key_downvotes_count', $args["review_downvotes"]);
            update_post_meta($review_id, 'parp_key_votes', $args["review_votes"]);

            update_post_meta($review_id, 'parp_key_inappropriate_list', $args["review_inappropriate_list"]);
            update_post_meta($review_id, 'parp_key_inappropriate_count', $args["review_inappropriate_count"]);
            update_post_meta($review_id, 'parp_key_featured', $args["review_is_featured"]);
            update_post_meta($review_id, 'parp_key_stop_reply', $args["review_is_reply_blocked"]);

            update_post_meta($review_id, 'parp_key_review_user_id', $args["review_user_id"]);
            update_post_meta($review_id, 'parp_key_review_author', $args["review_author"]);
            update_post_meta($review_id, 'parp_key_review_author_email', $args["review_author_email"]);
            update_post_meta($review_id, 'parp_key_review_author_url', $args["review_author_url"]);
            update_post_meta($review_id, 'parp_key_review_author_IP', $args["review_author_IP"]); 

            return $review_id;
        }
		 
		function submit_attachments($review_id) {
            //  check if attachments are enabled
			
			$parp_genral_dataaa=get_option('data_gen_adv_review');

			if(!empty($parp_genral_dataaa)){
				
				extract($parp_genral_dataaa);
			
			}
			
            if ($parp_upload_enable!=1) {
                
				return;
           
			}
			
            if ($_FILES) {
                
				$files = $_FILES["uploadFile"];
                
				$files_count = count($files['name']);
				
                //  check for attachments limits
                if (($parp_upload_lmt > 0) && ($files_count > $parp_upload_lmt)) {
                    return;
                }

                $attacchments_array = array();

                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name' => $files['name'][$key],
                            'type' => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error' => $files['error'][$key],
                            'size' => $files['size'][$key]
                        );
						
                        $_FILES = array("uploadFile" => $file);

                        foreach ($_FILES as $file => $array) {
                            $attachId = insert_attachment($file, $review_id);

                            //  enqueue attachments to current comment
                            array_push($attacchments_array, $attachId);
                        }
                    }
                }
				//  save review with attachments array
                update_post_meta($review_id, 'parp_media', $attacchments_array);
            } 
        }
		
		function insert_attachment($fileHandler, $postId) {
        
			if ($_FILES[$fileHandler]['error'] !== UPLOAD_ERR_OK) {
                __return_false();
            }

            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');

            return media_handle_upload($fileHandler, $postId);
        }
		
	
		function get_thumbnails($review) {
			
			$parp_genral_dataaaa=get_option('data_gen_adv_review');

			if(!empty($parp_genral_dataaaa)){
				extract($parp_genral_dataaaa);
			}
			
			$is_toplevel = (0 == $review->post_parent);
            $thumbnail_div = '';

            if ($is_toplevel && $parp_upload_enable) {

                if ($thumbs = get_post_meta($review->ID, 'parp_media', true)) {

                    $thumbnail_div = '<div class="review_thumbnail horizontalRule"> ';

                    foreach ($thumbs as $thumb_id) {
                       
					   $file_url = wp_get_attachment_url($thumb_id);
                       
					   $image_thumb = wp_get_attachment_image_src($thumb_id, array(100, 100), true);
 
					   $thumbnail_div .= "<a href='$file_url' data-rel = \"prettyPhoto[review-gallery-$review->ID]\"><img class=\"parp_thumbnail\" src='{$image_thumb[0]}' width='70px' height='70px'></a>";
                    }
                    $thumbnail_div .= ' </div> ';
                }
            }

            return $thumbnail_div;
        }
		
		function parp_show_expanded_review_content($review) {
			
			$parp_genral_datasa=get_option('data_gen_adv_review');
				
				if(!empty($parp_genral_datasa)){
					
					extract($parp_genral_datasa);
					
				}
					
			if (!is_product() && !(defined('DOING_AJAX') && DOING_AJAX)) {
                
				return $review->post_content;
            }

            $review_title = '';
            
			$thumbnail_div = get_thumbnails($review);
			
            if ($parp_plugin_enable==1 && $parp_sh_ttl==1) {
                //  Add review title before review content text
                if (!empty($review->post_title)) {
                    $review_title = '<span class="review_title"> ' . esc_attr($review->post_title) . '</span> ';
                }
            }
			
          return $review_title . $review->post_content . $thumbnail_div;
        }
	
	
		if($parp_plugin_enable==1){
			
			add_filter( 'woocommerce_product_tabs', 'parp_custom_review_tab', 98 );
			
			add_filter('parp_advanced_reviews_review_content', 'parp_show_expanded_review_content');
			
			add_action('comment_form_logged_in_after', 'parp_add_custom_fields_on_comment_form');
		
			add_action('comment_form_after_fields', 'parp_add_custom_fields_on_comment_form');
		}
		
		
			
		function parp_add_custom_fields_on_comment_form() {

			$parp_genral_dataaa=get_option('data_gen_adv_review');
				
			if(!empty($parp_genral_dataaa)){
				extract($parp_genral_dataaa);
			}
				
            if (!is_product() && !(defined('DOING_AJAX') && DOING_AJAX)) {
                return;
            }
			
            if ($parp_plugin_enable==1 && $parp_sh_ttl==1) {
                echo '<p class="comment-form-title"><label for="title">' . __('Review title ', 'parp') . '</label><input type="text" name="title" id="title" required;/></p>';
            }
        }
		
		
		function parp_custom_review_tab( $tabs ) {

				$tabs['reviews']['callback'] = 'parp_custom_review_tab_content';	

				return $tabs;
			}
		
		function get_average_rating($product_id) {
            global $wpdb;

            $query = $wpdb->prepare("
				select avg(meta_value)
				from {$wpdb->prefix}postmeta pm
				where meta_key = 'parp_rating' and post_id in
					(select post_id from {$wpdb->prefix}postmeta where meta_key = 'parp_product_id' and meta_value = %d and post_id IN
						(select post_id from {$wpdb->prefix}postmeta where meta_key = 'parp_review_apprpved' and meta_value = 1))", $product_id);

            
			
			$count = $wpdb->get_var($query);
			return number_format($count, 2);
        }
		
		// define the wc_get_template callback
		function filter_wc_get_template( $located, $template_name, $args, $template_path, $default_path ) 
		{
		
			if ("single-product/rating.php" != $template_name) {
                return $located;
            }
			
           $located = wc_locate_template("parp_ratings.php", $template_path, $default_path);
 
            if (file_exists($located)) {
                return $located;
            }

			// make filter magic happen here... 
			//return plugin_dir_url(__FILE__).'parp_ratings.php';
			
			if ( ! defined( 'ABSPATH' ) ) {
					exit; // Exit if accessed directly
				}

				global $product;

				if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
					return;
				}

				$rating_count = $review_count = count( get_product_reviews( $product->id ) );
				$average      = get_average_rating( $product->id );

				
				   if ( $rating_count > 0 ) : ?>
					<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope
						 itemtype="http://schema.org/AggregateRating">
						<div class="star-rating" title="<?php printf( __( 'Rated %s out of 5', 'parp' ), $average ); ?>">
							<span style="width:<?php echo( ( $average / 5 ) * 100 ); ?>%">
								<strong itemprop="ratingValue"
										class="ratingss"><?php echo esc_html( $average ); ?></strong> <?php printf( __( 'out of %s5%s', 'parp' ), '<span itemprop="bestRating">', '</span>' ); ?>
								<?php printf( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'parp' ), '<span itemprop="ratingCount" class="rating">' . $rating_count . '</span>' ); ?>
							</span>
						</div>

						<?php if ( comments_open() ) : ?><a href="#reviews" class="woocommerce-review-link" rel="nofollow">
							(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'parp' ), '<span itemprop="reviewCount" class="count">' . $review_count . '</span>' ); ?>
							)</a><?php endif ?>
					</div>

				<?php endif; 
			
		};
				
		
		
		
		function parp_custom_review_tab_content() {
			
			global $product;
			
			$parp_genral_da=get_option('data_gen_adv_review');
	
				if(!empty($parp_genral_da)){
					extract($parp_genral_da);
				}
			$parp_style_dataa=get_option('data_style_adv_review');
	
				if(!empty($parp_style_dataa)){
					extract($parp_style_dataa);
				}
				?>
				<style>
					.star-rating{
						color:<?php if(!isset($parp_str_clr)){echo "#000";}else{echo $parp_str_clr;} ?>;
					}
				</style>
				
				<?php 		
			$rating_array=array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0');
			
			$parp_args = array ('post_id' => $product->id,'ID' => '',); 
			
			$parp_comments = get_comments( $parp_args );
			
			
			$parp_count = $product->get_review_count();
			
			
			for($i=0;$i<$parp_count;$i++){
				
				$parp_comment_id = $parp_comments[$i]->comment_ID;
				$meta_values= get_comment_meta( $parp_comment_id, 'rating');
				//print_r($meta_values);
				if(!empty($meta_values)){
					if($meta_values[0]==1){
						$star_data=$rating_array[1];
						$star_data++;
						$rating_array[1]=$star_data;
					}
					if($meta_values[0]==1){
						$star_data=$rating_array[1];
						$star_data++;
						$rating_array[1]=$star_data;
					}
					if($meta_values[0]==2){
						$star_data=$rating_array[2];
						$star_data++;
						$rating_array[2]=$star_data;
					}
					if($meta_values[0]==3){
						$star_data=$rating_array[3];
						$star_data++;
						$rating_array[3]=$star_data;
					}
					if($meta_values[0]==4){
						$star_data=$rating_array[4];
						$star_data++;
						$rating_array[4]=$star_data;
					}
					if($meta_values[0]==5){
						$star_data=$rating_array[5];
						$star_data++;
						$rating_array[5]=$star_data;
					}
					
					
				}
			}

			$reviews_count = count( get_product_reviews_by_rating( $product->id ) );
			
			if (!is_product()) {
                return $template;
            }

            global $review_stats;
            
			$review_stats = array(
                '1' => count(get_product_reviews_by_rating($product->id, 1)),
                '2' => count(get_product_reviews_by_rating($product->id, 2)),
                '3' => count(get_product_reviews_by_rating($product->id, 3)),
                '4' => count(get_product_reviews_by_rating($product->id, 4)),
                '5' => count(get_product_reviews_by_rating($product->id, 5)),
                'total' => count(get_product_reviews_by_rating($product->id))
            );
			
			?>
			
			<div id="reviews">
				<div id="comments">
				<h3>Customers' review</h3><br>
				<?php	if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
								return;
							}

							$rating_count = $review_count = count( get_product_reviews( $product->id ) );
							$average      = get_average_rating( $product->id );
							
							if ( $rating_count > 0 ) : ?>

								<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
									<div class="star-rating" title="<?php printf( __( 'Rated %s out of 5', 'woocommerce' ), $average ); ?>">
										<span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
											<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( __( 'out of %s5%s', 'woocommerce' ), '<span itemprop="bestRating">', '</span>' ); ?>
											<?php printf( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'woocommerce' ), '<span itemprop="ratingCount" class="rating">' . $rating_count . '</span>' ); ?>
										</span>
										
									</div>
									<?php if ( comments_open() ) : ?><span  class="woocommerce-review-link" >
										<?php printf( _n( '%s customer review', '%s reviews', $review_count, 'woocommerce' ), '<span itemprop="reviewCount" class="count">' . $review_count . '</span>' ); ?></span> <?php echo '<br><span class="review-rating-value"> ' . esc_html($average) . ' ' . __("out of 5 stars", 'parp') . '</span>'; ?><?php endif ?>
								</div>
		
							<?php endif; ?>
					
					<div class="reviews_bar">

						<?php for ( $i = 5; $i >= 1; $i-- ) :
							$perc = ( $review_stats['total'] == '0' ) ? 0 : floor( $review_stats[ $i ] / $review_stats['total'] * 100 );
							?>

							<div class="parp_review_row">
								<?php do_action( 'parp_summary_row_prepend', $i, $product->id ) ?>

								<span
									class="parp_stars_value"><?php printf( _n( '%s star', '%s stars', $i, 'parp' ), $i ); ?></span>
								<span class="parp_num_reviews"><?php echo $review_stats[$i]; ?></span>
								<span class="parp_rating_bar">
									<span style="background-color:<?php if(!isset($parp_rating_bg_color)){echo "#f4f4f4";}else{echo $parp_rating_bg_color;}?>"
										  class="parp_scala_rating">
										<span class="parp_perc_rating"
											  style="width: <?php echo $perc; ?>%; background-color:<?php if(!isset($parp_rating_color)){echo "#a9709d";}else{echo $parp_rating_color;} ?>">
											<?php if ($parp_spv_enable==1) : ?>
												 <span style="color:<?php if(!isset($parp_prctg_color)){echo "#fff";}else{echo $parp_prctg_color;} ?>"
													  class="parp_perc_value"><?php printf( '%s %%', $perc ); ?>
												</span>							<?php endif; ?>
										</span>
									</span>
								</span>

								<?php do_action( 'parp_summary_row_append', $i, $product->id ) ?>
								
							</div>
						<?php endfor; ?>
					</div>
					 <h4 style="color:<?php if(isset($parp_ttl_color)){echo $parp_ttl_color;} ?>"><?php
						if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && $reviews_count ) {
							printf( _n( '%s review for %s', '%s reviews for %s', $reviews_count, 'parp' ), $reviews_count, get_the_title() );
						} else {
							_e( 'Reviews', 'parp' );
						}
						?>
					</h4> 
						
					<?php if ( $reviews_count ) : ?>
							<?php do_action( 'parp_advanced_reviews_before_review_list', $product ); ?>

							<ol class="commentlist">
								<?php reviews_list( $product->id ); ?>
							</ol>
						<?php else : ?>

							<p class="woocommerce-noreviews"><?php _e( 'There are no reviews yet.', 'parp' ); ?></p>

						<?php endif; ?>
					
				</div>

				<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>

					<div id="review_form_wrapper">
						<div id="review_form">
							<?php
								$commenter = wp_get_current_commenter();
								$comment_form = array(
									'title_reply'          => have_comments() ? __( 'Add a review', 'woocommerce' ) : __( 'Be the first to review', 'woocommerce' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
									'title_reply_to'       => __( 'Leave a Reply to %s', 'woocommerce' ),
									'comment_notes_before' => '',
									'comment_notes_after'  => '',
									'fields'               => array(
										'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'woocommerce' ) . ' <span class="required">*</span></label> ' .
													'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
										'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'woocommerce' ) . ' <span class="required">*</span></label> ' .
													'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
													
									),
									'label_submit'  => __( 'Submit', 'woocommerce' ),
									'logged_in_as'  => '',
									'comment_field' => ''
								);
								if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
									$comment_form['must_log_in'] = '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'woocommerce' ), esc_url( $account_page_url ) ) . '</p>';
								}
								if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
									$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Your Rating', 'woocommerce' ) .'</label><select name="rating" id="rating">
										<option value="">' . __( 'Rate&hellip;', 'woocommerce' ) . '</option>
										<option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>
										<option value="4">' . __( 'Good', 'woocommerce' ) . '</option>
										<option value="3">' . __( 'Average', 'woocommerce' ) . '</option>
										<option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>
										<option value="1">' . __( 'Very Poor', 'woocommerce' ) . '</option>
									</select></p>';
								}
								$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'woocommerce' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>';
								comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
							
							?>
						</div>
					</div>

				<?php else : ?>

					<p class="woocommerce-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>

				<?php endif; ?>

				<div class="clear"></div>
			</div>
			<?php
		}
	
		
	add_filter('comment_post_redirect', 'redirect_after_submit_review');
		
	add_action('woocommerce_admin_field_parp_import_previous_reviews','show_import_reviews_button');
	
	add_action('wp_ajax_convert_reviews','convert_reviews_callback');
	
	function convert_reviews_callback() {

			$review_converted = import_previous_reviews();
			
			$response = '';

			if ($review_converted) {
				
				$response = sprintf(__('Task completed. %d reviews have been processed and converted.', 'parp'), $review_converted);
			
			} else {
				
				$response = __('Task completed. No review to convert has been found.', 'parp');
		   
		   }

			wp_send_json(array("value" => $response));
		}
		
		add_action("admin_init","check_import_actions");	
	
		function  check_import_actions() {
			   
			   if (isset($_GET["convert-reviews"])) {
				
					import_previous_reviews();

					//wp_redirect(esc_url(remove_query_arg("convert-reviews")));
					
				}
			}
		
		
		
		function import_previous_reviews(){
			
			global $wpdb;

            $review_converted = 0;
			
			$query = "SELECT *
					FROM {$wpdb->prefix}comments as co left join {$wpdb->prefix}commentmeta as cm
					on co.comment_ID = cm.comment_id
					where ((co.comment_approved = '0') or (co.comment_approved = '1')) and  cm.meta_key = 'rating'";

            $results = $wpdb->get_results($query);
			
			$review_ids = array();
            $parent_review = array();
			
			foreach ($results as $comment) {

                // Check if comment_meta exists for previous reviews and is not still imported
                if ("1" === get_comment_meta($comment->comment_ID, 'parp_imported_key', true)) {
                    //  comment still imported, update only author data (Fix for upgrade from 1.1.2 to 1.2.3 then skip it!

                    //  Retrieve review(post) id linked to current comment
                    $args = array(
                        'post_type' => 'parp_reviews',
                        'meta_query' => array(
                            array(
                                'key' => 'parp_comment_id',
                                'value' => $comment->comment_ID,
                                'compare' => '=',
                                'type' => 'numeric'
                            )
                        )
                    );
					
                    $reviews = get_posts($args);
					
                    if (isset($reviews)) {
                        $review = $reviews[0];

                        // Update review meta
                        update_post_meta($review->ID, 'parp_key_review_user_id', $comment->user_id);
                        update_post_meta($review->ID, 'parp_meta_key_review_author', $comment->comment_author);
                        update_post_meta($review->ID, 'parp_meta_key_review_author_email', $comment->comment_author_email);
                        update_post_meta($review->ID, 'parp_meta_key_review_author_url', $comment->comment_author_url);
                        update_post_meta($review->ID, 'parp_meta_key_review_author_IP', $comment->comment_author_IP);
                    }

                    continue;
                }
				
                //  Set execution time
               //echo set_time_limit(30);
			   
                $val = get_comment_meta($comment->comment_ID, "title", true);
                $title = $val ? $val : '';

                $val = get_comment_meta($comment->comment_ID, "thumb_ids", true);
                $thumb_ids = $val ? $val : array();

                $val = get_comment_meta($comment->comment_ID, "rating", true);
                $rating = $val ? $val : 0;

                //  Import previous settings for "stop reply" from the comment
                $val = get_comment_meta($comment->comment_ID, "no_reply", true);
                $no_reply = $val ? $val : 0;

                //  Import previous settings for "votes" from the comment
                $val = get_comment_meta($comment->comment_ID, "votes", true);
                $votes = $val ? $val : array();

                //  Extract upvotes and downvotes count
                $votes_grouped = array_count_values(array_values($votes));
                $yes_votes = isset($votes_grouped['1']) && is_numeric($votes_grouped['1']) ? $votes_grouped['1'] : 0;
                $no_votes = isset($votes_grouped['-1']) && is_numeric($votes_grouped['-1']) ? $votes_grouped['-1'] : 0;

                // Create post object
                $args = array(
                    'post_author' => $comment->user_id,
                    'post_date' => $comment->comment_date,
                    'post_date_gmt' => $comment->comment_date_gmt,
                    'post_content' => $comment->comment_content,
                    'post_title' => $title,
                    'review_user_id' => $comment->user_id,
                    'review_approved' => $comment->comment_approved,
                    'review_product_id' => $comment->comment_post_ID,
                    'review_thumbnails' => $thumb_ids,
                    'review_comment_id' => $comment->comment_ID,
                    'review_rating' => $rating,
                    'review_is_reply_blocked' => $no_reply,
                    'review_votes' => $votes,
                    'review_upvotes' => $yes_votes,
                    'review_downvotes' => $no_votes,
                    'review_author' => $comment->comment_author,
                    'review_author_email' => $comment->comment_author_email,
                    'review_author_url' => $comment->comment_author_url,
                    'review_author_IP' => $comment->comment_author_IP
                );
				
				// Insert the post into the database
                $review_id = insert_review($args);

                $review_ids[$comment->comment_ID] = $review_id;

                //  If current comment have parent, store it and update all relationship when the import ends
                if ($comment->comment_parent > 0) {
                    $parent_review[$review_id] = $comment->comment_parent;
                }

                //  set current comment as imported
                update_comment_meta($comment->comment_ID, 'parp_imported_key', 1);
                $review_converted++;
            }
			foreach ($parent_review as $key => $value) {
                if (isset($review_ids[$value])) {

                    //update the post which id is in $key, setting parent to $review_ids[$value]
                    $args = array(
                        'ID' => $key,
                        'post_parent' => $review_ids[$value]
                    );

                    // Update the post into the database
                    wp_update_post($args);
                }
            
			}
			
            return $review_converted; 
		}
		
	
	
		
	function parp_activate() {

		$parp_genral_=get_option('data_gen_adv_review');
		
			if((!isset($parp_genral_)||empty($parp_genral_))){
				
				$parp_genral_data_default=array('parp_plugin_enable'=>1,
													'parp_sh_ttl'=>1,'parp_spv_enable'=>1,
													'parp_upload_enable'=>1,
													'parp_upload_lmt'=>1,
												);
		
				update_option('data_gen_adv_review',$parp_genral_data_default);
			} 
	}
	
	register_activation_hook( __FILE__, 'parp_activate' );
	
	
	
}
else
{ 
	?>
		<div class="error notice is-dismissible " id="message"><p>Please <strong>Activate</strong> WooCommerce Plugin First, to use it.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
	<?php 
}  
?>
