<?php
	/**
	 *	Bakery WordPress Theme
	 */

	// Convert shortcode atts to string
	if( !function_exists('vu_shortcode_atts') ){
		function vu_shortcode_atts($atts){
			$return = '';

			foreach ($atts as $key => $value) {
				$return .= ' '. $key .'="'. esc_attr($value) .'"';
			}

			return $return;
		}
	}

	// Generate shortcode as string
	if( !function_exists('vu_generate_shortcode') ){
		function vu_generate_shortcode($tag, $atts, $content = null){
			$return = '['. $tag . vu_shortcode_atts($atts) .']';

			if( !empty($content) ){
				$return .= $content .'[/'. $tag .']';
			}

			return $return;
		}
	}

	// Get the URL (src) for an image attachment
	if( !function_exists('vu_get_attachment_image_src') ){
		function vu_get_attachment_image_src($attachment_id, $size = 'thumbnail', $icon = false, $return = 'url'){
			$image_attributes = wp_get_attachment_image_src( $attachment_id, $size, $icon );

			if( $image_attributes ) {
				switch ($return) {
					case 'all':
						$return = $image_attributes;
						break;
					case 'url':
						$return = $image_attributes[0];
						break;
					case 'width':
						$return = $image_attributes[1];
						break;
					case 'height':
						$return = $image_attributes[2];
						break;
					case 'resized ':
						$return = $image_attributes[3];
						break;
					
					default:
						$return = $image_attributes[0];
						break;
				}
			}

			return $return;
		}
	}

	// Animate element if animation option is enabled
	if( !function_exists('vu_animation') ){
		function vu_animation($echo = false, $delay = null){
			$return = (vu_get_option('animation') == true) ? ' onscroll-animate'. ((!empty($delay)) ? '" data-delay="'. esc_attr($delay) : '') : '';

			if( $echo ){
				echo $return;
			} else {
				return $return;
			}
		}
	}

	// Extra class for shortcode
	if( !function_exists('vu_extra_class') ){
		function vu_extra_class($class, $echo = true){
			$return = ((!empty($class)) ? ' '. esc_attr($class) : '');
			
			if( $echo == true ) {
				echo $return;
			} else {
				return $return;
			}
		}
	}

	// Get theme option value
	if( !function_exists('vu_get_option') ){
		function vu_get_option($option, $default = ''){
			global $vu_theme_options;

			if( is_array($option) ){
				$count = count($option);

				switch ($count) {
					case 2:
						return isset($vu_theme_options[$option[0]][$option[1]]) ? $vu_theme_options[$option[0]][$option[1]] : $default;
						break;
					case 3:
						return isset($vu_theme_options[$option[0]][$option[1]][$option[2]]) ? $vu_theme_options[$option[0]][$option[1]][$option[2]] : $default;
						break;
						
					default:
						return isset($vu_theme_options[$option[0]]) ? $vu_theme_options[$option[0]] : $default;
						break;
				}
			} else {
				return isset($vu_theme_options[$option]) ? $vu_theme_options[$option] : $default;
			}
		}
	}

	// Update Post Meta Data
	if( !function_exists('vu_update_post_meta') ) {
		function vu_update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = null ){
			if( is_array($meta_value) )
				$meta_value = vu_json_encode( $meta_value );

			update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
		}
	}

	// Get Post Meta Data
	if( !function_exists('vu_get_post_meta') ) {
		function vu_get_post_meta( $post_id, $key, $json = true ){
			$return = get_post_meta( $post_id, $key, true );

			if( $json )
				$return = vu_json_decode( $return );

			return $return;
		}
	}

	// JSON Encode
	if( !function_exists('vu_json_encode') ) {
		function vu_json_encode( $array ){
			return wp_slash(json_encode($array));
		}
	}

	// JSON Decode
	if( !function_exists('vu_json_decode') ) {
		function vu_json_decode( $json ){
			return ( !empty($json) ? wp_unslash(json_decode($json, true)) : false );
		}
	}

	// Print Pagination
	if( !function_exists('vu_pagination') ) {
		function vu_pagination(){
			global $wp_query, $wp_rewrite; 
				
			$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1; 
			$total_pages = $wp_query->max_num_pages; 

			if ($total_pages > 1){
				$permalink_structure = get_option('permalink_structure');
				$query_type = (count($_GET)) ? '&' : '?';	
				$format = empty( $permalink_structure ) ? $query_type.'paged=%#%' : 'page/%#%/';  
			
				echo '<div class="text-center"><div class="pagination">';
				 
				echo paginate_links(array(
					'base' => get_pagenum_link(1) . '%_%',
					'format' => $format,
					'current' => $current,
					'total' => $total_pages,
					'prev_text' => '<div class="pagination-item pagination-nav">'. __('prev', 'bakery-cpt') .'</div>',
					'next_text' => '<div class="pagination-item pagination-nav">'. __('next', 'bakery-cpt') .'</div>',
					'before_page_number' => '<div class="pagination-item">',
					'after_page_number' => '</div>'
				)); 
				
				echo  '</div></div>'; 
			}
		}
	}

	// Print Portfolio Item Socials Networks
	if( !function_exists('vu_portfolio_item_socials') ) {
		function vu_portfolio_item_socials($url, $title = null, $post_id = null){
			if( vu_get_option('portfolio-social') ) : ?>
				<div class="vu_socials clearfix<?php vu_animation(true); ?>">
					<h3><?php echo __('Share', 'bakery-cpt'); ?></h3>


					<?php if( vu_get_option( array('portfolio-social-networks','facebook') ) == '1' ) { ?>
						<div class="social-icon-container-small">
							<a href="#" class="vu_social-link" data-href="http://www.facebook.com/sharer.php?u=<?php echo esc_url($url); ?>&amp;t=<?php echo urlencode($title); ?>"><i class="fa fa-facebook"></i></a>
						</div>
					<?php } if( vu_get_option( array('portfolio-social-networks','twitter') ) == '1' ) { ?>
						<div class="social-icon-container-small">
							<a href="#" class="vu_social-link" data-href="https://twitter.com/share?text=<?php echo urlencode($title); ?>&amp;url=<?php echo esc_url($url); ?>"><i class="fa fa-twitter"></i></a>
						</div>
					<?php } if( vu_get_option( array('portfolio-social-networks','google-plus') ) == '1' ) { ?>
						<div class="social-icon-container-small">
							<a href="#" class="vu_social-link" data-href="https://plus.google.com/share?url=<?php echo esc_url($url); ?>"><i class="fa fa-google-plus"></i></a>
						</div>
					<?php } if( vu_get_option( array('portfolio-social-networks','pinterest') ) == '1' ) { ?>
						<div class="social-icon-container-small">
							<a href="#" class="vu_social-link" data-href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($url); ?>&amp;description=<?php echo urlencode($title); ?>&amp;media=<?php echo vu_get_attachment_image_src($post_id, array(705, 470)); ?>"><i class="fa fa-pinterest"></i></a>
						</div>
					<?php } if( vu_get_option( array('portfolio-social-networks','linkedin') ) == '1' ) { ?>
						<div class="social-icon-container-small">
							<a href="#" class="vu_social-link" data-href="http://linkedin.com/shareArticle?mini=true&amp;title=<?php echo urlencode($title); ?>&amp;url=<?php echo esc_url($url); ?>"><i class="fa fa-linkedin"></i></a>
						</div>
					<?php } ?>
				</div>
		<?php 
			endif;
		}
	}

	// Print Excerpt with Custom Length
	if( !function_exists('vu_the_excerpt') ) {
		function vu_the_excerpt($num_of_words, $post_excerpt = null) {
			$excerpt = empty($post_excerpt) ? get_the_excerpt() : $post_excerpt;

			$exwords = explode( ' ', trim( mb_substr( $excerpt, 0, mb_strlen($excerpt) - 5 ) ) );

			if( count($exwords) > $num_of_words ){
				$excerpt = array();

				$i = 0;
				foreach ($exwords as $value) {
					if( $i >= $num_of_words ) break;
					array_push($excerpt, $value);
					$i++;
				}

				echo implode(' ', $excerpt) . ' [...]';
			} else {
				echo $excerpt;
			}
		}
	}

	// Get Portfolio Category or Portfolio Tags
	if( !function_exists('vu_portfolio_terms') ) {
		function vu_portfolio_terms($post_id, $echo = true, $implode = ", ", $slug = false, $taxonomy = 'portfolio-category'){
			$terms = get_the_terms( $post_id, $taxonomy );

			$return = '&nbsp;';
								
			if ( $terms && ! is_wp_error( $terms ) ) {
				$terms_return = array();

				foreach ( $terms as $term ) {
					if( $slug ){
						$terms_return[] = $term->slug;
					} else {
						$terms_return[] = $term->name;
					}
				}
				
				$return = implode( $implode, $terms_return );
			}

			if( $echo ){
				echo $return;
			} else {
				return $return;
			}
		}
	}

	// Portfolio Item Template
	if( !function_exists('vu_portfolio_item') ) {
		function vu_portfolio_item($image_size = 'ratio-3:4', $layout = false, $class = '') {
			global $post;

			$vu_portfolio_item_settings = vu_get_post_meta( get_the_ID(), 'vu_portfolio_item_settings' );
		?>

			<?php if( $layout != false ) { ?>
				<div class="vu_product-item-container col-xs-12 col-sm-6 col-md-<?php echo 12 / $layout; ?><?php vu_extra_class($class); ?>">
			<?php } ?>

			<article <?php post_class('vu_product-item') ?> data-id="<?php the_ID(); ?>">
				<div class="vu_pi-image">
					<?php if( !empty($vu_portfolio_item_settings['ribbon']) and (int)vu_get_option('portfolio-item-ribbons') >= (int)$vu_portfolio_item_settings['ribbon'] ) : ?>
						<div class="vu_pi-label-container">
							<div class="vu_pi-label"><?php echo esc_attr(vu_get_option('ribbon-name-'. absint($vu_portfolio_item_settings['ribbon']))); ?></div>
							<div class="vu_pi-label-bottom"></div>
						</div>
					<?php endif; ?>

					<?php if( has_post_thumbnail() ) { the_post_thumbnail($image_size); } ?>
				</div>

				<div class="vu_pi-container">
					<div class="vu_pi-icons">
						<a href="<?php echo vu_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?>" title="<?php the_title(); ?>" class="vu_pi-icon vu_lightbox"><i class="fa fa-search"></i></a>
						
						<?php if( vu_get_option( array('portfolio-item-link', 'icon-link') ) ) : ?>
							<a href="<?php the_permalink(); ?>" class="vu_pi-icon"><i class="fa fa-link"></i></a>
						<?php endif; ?>

						<?php if( vu_get_option('portfolio-item-ordering') ) : ?>
							<a href="<?php the_permalink(); ?>#order-form" class="vu_pi-icon"><i class="fa fa-shopping-cart"></i></a>
						<?php endif; ?>
					</div>

					<div class="vu_pi-content">
						<h3 class="vu_pi-name">
							<?php if( vu_get_option( array('portfolio-item-link', 'title-link') ) ) : ?>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<?php else : ?>
								<?php the_title(); ?>
							<?php endif; ?>
						</h3>
						<span class="vu_pi-category"><?php vu_portfolio_terms( get_the_ID() ); ?></span>
						
						<?php if( !empty($vu_portfolio_item_settings['price']) and vu_get_option('portfolio-item-price') == true ) : ?>
							<div class="vu_pi-price"><span class="amount"><?php echo esc_attr(vu_get_option('portfolio-item-currency')) . esc_html($vu_portfolio_item_settings['price']); ?></span></div>
						<?php endif; ?>
					</div>
				</div>
			</article>

			<?php if( $layout != false ) { ?>
				</div>
			<?php } ?>
		<?php
		}
	}

	// Get Portfolio Items
	if( !function_exists('vu_get_portfolio_items') ) {
		function vu_get_portfolio_items(){
			$args = array(
				'post_type' => 'portfolio-item',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'ignore_sticky_posts'=> 1
			);

			$return = array('' => '');

			$portfolio_items = new WP_Query($args);

			if( $portfolio_items->have_posts() ) {
				while ($portfolio_items->have_posts()) : $portfolio_items->the_post();
					$return[get_the_title()] = get_the_ID();
				endwhile;
			}
			
			wp_reset_query();

			return $return;
		}
	}

	// Get All Portfolio Categories with levels for VC
	if( !function_exists('vu_portfolio_categories') ) {
		function vu_portfolio_categories(){
			ob_start();

			wp_dropdown_categories( 'taxonomy=portfolio-category&hierarchical=1' );

			$output = ob_get_contents();
			ob_end_clean();

			$output = preg_replace('/(&nbsp;&nbsp;&nbsp;)/', '– ', $output);

			preg_match_all('#<option.*?value="(\d+)">(.*?)</option>#', $output, $matches);

			if( !empty($matches[1]) and !empty($matches[2]) ) {
				return array("None" => "0") + array_combine((array)$matches[2], (array)$matches[1]);
			} else {
				return array("None" => "0");
			}
		}
	}
?>