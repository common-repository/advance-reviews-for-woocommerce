<?php
echo plugin_dir_path( __FILE__ );
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}

$rating_count = $review_count = count( get_product_reviews( $product->id ) );
$average      = get_average_rating( $product->id );

echo $rating_count;
echo $review_count; 
 /* if ( $rating_count > 0 ) : ?>
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

<?php endif; ?>  */