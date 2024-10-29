<?php 

	function reviews_list($product_id, $args = null) {
            
			$reviews = get_product_reviews($product_id, $args);
			
			
             foreach ($reviews as $review) {
             
				show_review($review);
            } 
			
        }
	
		
	 function get_product_reviews($product_id = null, $args = null) {

            if ($args == null) {
                $args = default_query_args($product_id);
            }

            //  if $product_id is null, retrieve all reviews without filters
            if (is_null($product_id)) {
                unset($args['meta_query']);
            }
			
            return get_posts($args);
        }
		
	 function default_query_args($product_id) {
            return array(
                'numberposts' => -1,    //By default retrieve all reviews
                'offset' => 0,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post_type' => 'parp_reviews',
                'post_parent' => '0',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'parp_product_id',
                        'value' => $product_id,
                        'compare' => '=',
                        'type' => 'numeric'
                    ),
                    array(
                        'key' => 'parp_review_apprpved',
                        'value' => 1,
                        'compare' => '=',
                        'type' => 'numeric'
                    )
                )
            );
        }
	
	function get_meta_value_rating($review_id) {
            return get_post_meta($review_id, 'parp_rating', true);
	}
	
	function get_meta_value_approved($review_id) {
            return get_post_meta($review_id, 'parp_review_apprpved', true);
        }
	
	
	function get_meta_value_author($review_id) {
            
		return array(
			'review_user_id' => get_post_meta($review_id, 'parp_key_review_user_id', true),
			'review_author' => get_post_meta($review_id, 'parp_meta_key_review_author', true),
			'review_author_email' => get_post_meta($review_id, 'parp_meta_key_review_author_email', true),
			'review_author_url' => get_post_meta($review_id, 'parp_meta_key_review_author_url', true),
			'review_author_IP' => get_post_meta($review_id, 'parp_meta_key_review_author_IP', true)
		);
	}
	
	function get_meta_value_product_id($review_id) {
            return get_post_meta($review_id, 'parp_product_id', true); 
	}
		
	function show_review($review, $featured = false, $classes = '') {
           
			global $parp_review;
			
            $parp_review = $review;
			
			 wc_get_template('parp_review.php', array(
                'review' => $review,
                'featured' => $featured,
                'classes' => $classes
            ), '', plugin_dir_path( __FILE__ )); 
		
        }	
	
		function get_product_reviews_by_rating($product_id, $rating = 0) {
			
            $args = default_query_args($product_id);
            if ($rating > 0) {
                $args['meta_query'][] = array(
                    'key' => 'parp_rating',
                    'value' => $rating,
                    'compare' => '=',
                    'type' => 'numeric'
                );
            }

            return get_product_reviews($product_id, $args);
        }	
		
	
?>