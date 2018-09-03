<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_theme_style' );
function enqueue_parent_theme_style() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

function SearchFilter($query) {

    if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('page'));
    }

    return $query;
}

add_filter('pre_get_posts','SearchFilter');

function rehub_create_btn ($btn_more='', $showme = '') {
	?>

		<?php
			$aff_url_exist = get_post_meta( get_the_ID(), 'affegg_product_orig_url', true );
			$offer_url_exist = get_post_meta( get_the_ID(), 'rehub_offer_product_url', true );
			$multiofferrows = get_post_meta(get_the_ID(), 'rehub_multioffer_group', true);
		?>
		<?php if (!empty($offer_url_exist)) : ?>

			<?php
				$offer_url = apply_filters('rh_post_offer_url_filter', $offer_url_exist);
			 	$offer_price = get_post_meta( get_the_ID(), 'rehub_offer_product_price', true );
			 	$offer_btn_text = get_post_meta( get_the_ID(), 'rehub_offer_btn_text', true );
			 	$offer_price_old = get_post_meta( get_the_ID(), 'rehub_offer_product_price_old', true );
			 	$offer_coupon = get_post_meta( get_the_ID(), 'rehub_offer_product_coupon', true );
			 	$offer_coupon_date = get_post_meta( get_the_ID(), 'rehub_offer_coupon_date', true );
			 	$offer_coupon_mask = get_post_meta( get_the_ID(), 'rehub_offer_coupon_mask', true );
			?>

			<?php $coupon_style = $expired = ''; if(!empty($offer_coupon_date)) : ?>
				<?php
					$timestamp1 = strtotime($offer_coupon_date);
					$seconds = $timestamp1 - (int)current_time('timestamp',0);
					$days = floor($seconds / 86400);
					$seconds %= 86400;
            		if ($days > 0) {
            			$coupon_style = '';
            		}
            		elseif ($days == 0){
            			$coupon_style = '';
            		}
            		else {
            			$coupon_text = __('Expired', 'rehub_framework');
            			$coupon_style = ' expired_coupon';
            			$expired = '1';
            		}
				?>
			<?php endif ;?>
			<?php do_action('post_change_expired', $expired); //Here we update our expired?>
			<?php $coupon_mask_enabled = (!empty($offer_coupon) && ($offer_coupon_mask =='1' || $offer_coupon_mask =='on') && $expired!='1') ? '1' : ''; ?>
			<?php $reveal_enabled = ($coupon_mask_enabled =='1') ? ' reveal_enabled' : '';?>
	        <div class="priced_block clearfix <?php echo $reveal_enabled; echo $coupon_style; ?>">
	            <?php if(!empty($offer_price) && $showme !='button') : ?>
	            	<span class="rh_price_wrapper">
	            		<span class="price_count">
	            			<ins><?php echo esc_html($offer_price) ?></ins>
	            			<?php if($offer_price_old !='') :?> <del><?php echo esc_html($offer_price_old) ; ?></del><?php endif ;?>
	            		</span>
	            	</span>
	            <?php endif ;?>
	    		<?php if($showme !='price') : ?>
		            <a href="<?php echo esc_url ($offer_url) ?>" class="btn_offer_block re_track_btn" target="_blank" rel="nofollow">
			            <?php if($offer_btn_text !='') :?>
			            	<?php _e('View Product', 'rehub_framework') ?>
			            <?php elseif(rehub_option('rehub_btn_text') !='') :?>
			            	<?php _e('View Product', 'rehub_framework') ?>
			            <?php else :?>
			            	<?php _e('View Product', 'rehub_framework') ?>
			            <?php endif ;?>
		            </a>
	            <?php endif;?>
		    	<?php if ($coupon_mask_enabled =='1') :?>
		    		<?php if($showme !='price') : ?>
			    		<div class="post_offer_anons">
			    			<?php wp_enqueue_script('zeroclipboard'); ?>
		                	<span class="coupon_btn re_track_btn btn_offer_block rehub_offer_coupon masked_coupon <?php if(!empty($offer_coupon_date)) {echo $coupon_style ;} ?>" data-clipboard-text="<?php echo esc_html ($offer_coupon) ?>" data-codeid="<?php echo get_the_ID()?>" data-dest="<?php echo esc_url($offer_url) ?>">
		                		<?php if($offer_btn_text !='') :?>
			            			<?php echo esc_html ($offer_btn_text) ; ?>
		                		<?php elseif(rehub_option('rehub_mask_text') !='') :?>
		                			<?php echo rehub_option('rehub_mask_text') ; ?>
		                		<?php else :?>
		                			<?php _e('Reveal coupon', 'rehub_framework') ?>
		                		<?php endif ;?>
		                	</span>
		            	</div>
	            	<?php endif;?>
		    	<?php else : ?>
					<?php if(!empty($offer_coupon) && $showme !='price') : ?>
						<?php wp_enqueue_script('zeroclipboard'); ?>
					  	<div class="rehub_offer_coupon not_masked_coupon <?php if(!empty($offer_coupon_date)) {echo $coupon_style ;} ?>" data-clipboard-text="<?php echo $offer_coupon ?>"><i class="fa fa-scissors fa-rotate-180"></i><span class="coupon_text"><?php echo $offer_coupon ?></span>
					  	</div>
				  	<?php endif;?>
		        <?php endif; ?>
	        </div>
		<?php elseif(!empty($multiofferrows[0]['multioffer_url'])):?>
	        <div class="priced_block clearfix">
                <?php
                	$min_price = get_post_meta(get_the_ID(), 'rehub_main_product_price', true );
                 	if ($min_price !='' && $showme !='button') : ?>
                		<span class="rh_price_wrapper">
                			<span class="price_count">
                				<ins><?php echo rehub_option('rehub_currency'); echo $min_price; ?></ins>
                			</span>
                		</span>
                <?php endif ;?>
	            <?php if($showme !='price') : ?>
	            	<div>
	            		<a href="<?php the_permalink();?>#multiofferlist" class="btn_offer_block">
	            			<?php if(rehub_option('rehub_btn_text_aff_links') !='') :?>
	            				<?php echo rehub_option('rehub_btn_text_aff_links') ; ?>
	            			<?php else :?>
	            				<?php _e('Choose offer', 'rehub_framework') ?>
	            			<?php endif ;?>
	            		</a>
	            	</div>
	            <?php endif ;?>
	        </div>
		<?php elseif (!empty($aff_url_exist)) : ?>

			<?php if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
				include(rh_locate_template( 'inc/parts/affeggbutton.php' ) );
			} ?>
		<?php elseif (vp_metabox('rehub_post.rehub_framework_post_type') == 'review' && vp_metabox('rehub_post.review_post.0.review_post_schema_type') == 'review_post_review_product') : ?>
			<?php $review_aff_link = vp_metabox('rehub_post.review_post.0.review_post_product.0.review_aff_link');
			if(function_exists('thirstyInit') && !empty($review_aff_link)) :?>
				<?php
					$linkpost = get_post($review_aff_link);
				 	$offer_price = get_post_meta( $linkpost->ID, 'rehub_aff_price', true );
				 	$offer_btn_text = get_post_meta( $linkpost->ID, 'rehub_aff_btn_text', true );
				 	$offer_url = get_post_permalink($review_aff_link) ;
				 	$offer_price_old = get_post_meta( $linkpost->ID, 'rehub_aff_price_old', true );
				?>
			<?php else :?>
		        <?php $offer_price = vp_metabox('rehub_post.review_post.0.review_post_product.0.review_post_product_price') ?>
		        <?php $offer_url = vp_metabox('rehub_post.review_post.0.review_post_product.0.review_post_product_url') ?>
		        <?php $offer_btn_text = vp_metabox('rehub_post.review_post.0.review_post_product.0.review_post_btn_text') ?>
		        <?php $offer_price_old = vp_metabox('rehub_post.review_post.0.review_post_product.0.review_post_product_price_old') ?>
	    	<?php endif;?>
	        <div class="priced_block clearfix">
	            <?php if(!empty($offer_price) && $showme !='button') : ?>
	            	<span class="rh_price_wrapper">
	            		<span class="price_count"><ins><?php echo esc_html($offer_price) ?></ins>
	            		<?php if($offer_price_old !='') :?> <del><?php echo esc_html($offer_price_old) ; ?></del><?php endif ;?>
	            		</span>
	            	</span>
	            <?php endif ;?>
	            <?php if($showme !='price') : ?><div><a href="<?php echo esc_url ($offer_url) ?>" class="re_track_btn btn_offer_block" target="_blank" rel="nofollow"><?php if($offer_btn_text !='') :?><?php echo $offer_btn_text ; ?><?php elseif(rehub_option('rehub_btn_text') !='') :?><?php echo rehub_option('rehub_btn_text') ; ?><?php else :?><?php _e('Buy It Now', 'rehub_framework') ?><?php endif ;?></a></div><?php endif ;?>
	        </div>
	    <?php elseif (vp_metabox('rehub_post.rehub_framework_post_type') == 'review' && vp_metabox('rehub_post.review_post.0.review_post_schema_type') == 'review_aff_product') :?>
			<?php $rehub_aff_post_ids = vp_metabox('rehub_post.review_post.0.review_aff_product.0.review_aff_links');
			if(function_exists('thirstyInit') && !empty($rehub_aff_post_ids)) :?>
		        <div class="priced_block clearfix">
	                <?php $min_aff_price_count = get_post_meta(get_the_ID(), 'rehub_min_aff_price', true); if ($min_aff_price_count !='' && $showme !='button') : ?>
	                	<span class="rh_price_wrapper"><span class="price_count"><ins><?php echo rehub_option('rehub_currency'); echo esc_html($min_aff_price_count); ?></ins></span></span>
	                <?php endif ;?>
		            <?php if($showme !='price') : ?><div><a href="<?php the_permalink();?>#aff-link-list" class="btn_offer_block" target="_blank" rel="nofollow"><?php if(rehub_option('rehub_btn_text_aff_links') !='') :?><?php echo rehub_option('rehub_btn_text_aff_links') ; ?><?php else :?><?php _e('Choose offer', 'rehub_framework') ?><?php endif ;?></a></div><?php endif ;?>
		        </div>
	    	<?php endif ;?>
	    <?php elseif (vp_metabox('rehub_post.rehub_framework_post_type') == 'review' && vp_metabox('rehub_post.review_post.0.review_post_schema_type') == 'review_woo_list') :?>
			<?php $review_woo_list_links = vp_metabox('rehub_post.review_post.0.review_woo_list.0.review_woo_list_links');
			if(class_exists('Woocommerce') && !empty($review_woo_list_links)) :?>
		        <div class="priced_block clearfix">
	                <?php $min_woo_price_count = get_post_meta(get_the_ID(), 'rehub_min_woo_price', true); if ($min_woo_price_count !='' && $showme !='button') : ?>
	                	<p><span class="price_count"><ins><?php echo rehub_option('rehub_currency'); echo $min_woo_price_count; ?></ins></span></p>
	                <?php endif ;?>
		            <?php if($showme !='price') : ?><div><a href="<?php the_permalink();?>#woo-link-list" class="btn_offer_block"><?php if(rehub_option('rehub_btn_text_aff_links') !='') :?><?php echo rehub_option('rehub_btn_text_aff_links') ; ?><?php else :?><?php _e('Choose offer', 'rehub_framework') ?><?php endif ;?></a></div><?php endif ;?>
		        </div>
	    	<?php endif ;?>

		<?php elseif (vp_metabox('rehub_post.rehub_framework_post_type') == 'review' && vp_metabox('rehub_post.review_post.0.review_post_schema_type') == 'review_woo_product') :?>
        	<?php $review_woo_link = vp_metabox('rehub_post.review_post.0.review_woo_product.0.review_woo_link');?>
        	<?php if(rehub_option('rehub_btn_text') !='') :?><?php $btn_txt = rehub_option('rehub_btn_text') ; ?><?php else :?><?php $btn_txt = __('Buy It Now', 'rehub_framework') ;?><?php endif ;?>
        	<?php global $woocommerce; global $post;$backup=$post; if($woocommerce) :?>
				<?php
					$args = array(
						'post_type' 		=> 'product',
						'posts_per_page' 	=> 1,
						'no_found_rows' 	=> 1,
						'post_status' 		=> 'publish',
						'p'					=> $review_woo_link,

					);
				?>
				<?php $products = new WP_Query( $args ); if ( $products->have_posts() ) : ?>
					<?php while ( $products->have_posts() ) : $products->the_post(); global $product?>
					<?php $offer_price = $product->get_price_html() ?>
					<div class="priced_block clearfix">
		                <?php if(!empty($offer_price) && $showme !='button') : ?><span class="rh_price_wrapper"> <span class="price_count"><?php echo $offer_price ?></span></span><?php endif ;?>
		                <?php if($showme !='price') : ?>
			                <div>
			                	<?php if ($product->product_type =='external' && $product->add_to_cart_url() =='') :?>
			                		<a class='re_track_btn btn_offer_block' href="<?php the_permalink();?>" target="_blank"><?php _e('Prices', 'rehub_framework') ;?></a>
			                	<?php else :?>
						            <?php if ( $product->is_in_stock() &&  $product->add_to_cart_url() !='') : ?>
						             <?php  echo apply_filters( 'woocommerce_loop_add_to_cart_link',
						                    sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="woo_loop_btn btn_offer_block %s %s product_type_%s"%s>%s</a>',
						                    esc_url( $product->add_to_cart_url() ),
						                    esc_attr( $product->id ),
						                    esc_attr( $product->get_sku() ),
					    					$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
					    					$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
						                    esc_attr( $product->product_type ),
						                    $product->product_type =='external' ? ' target="_blank"' : '',
						                    esc_html( $product->add_to_cart_text() )
						                    ),
						            $product );?>
						            <?php endif; ?>
								<?php endif; ?>
			                </div>
		                <?php endif; ?>
		            </div>
				<?php endwhile; endif;  wp_reset_postdata(); $post=$backup; ?>
        	<?php endif ;?>

        <?php else :?>
        	<?php if ($btn_more =='yes' && $showme !='price') :?>

	        	<?php if (vp_metabox('rehub_post_side.read_more_custom')): ?>
			  		<a href="<?php the_permalink();?>" class="btn_more btn_more_custom"><?php echo strip_tags(vp_metabox('rehub_post_side.read_more_custom'));?></a>
				<?php elseif (rehub_option('rehub_readmore_text') !=''): ?>
			  		<a href="<?php the_permalink();?>" class="btn_more"><?php echo strip_tags(rehub_option('rehub_readmore_text'));?></a>
			  	<?php else: ?>
					<a href="<?php the_permalink();?>" class="btn_more"><?php _e('READ MORE  +', 'rehub_framework') ;?></a>
			  	<?php endif ?>

        	<?php endif ;?>

	    <?php endif ;?>

	<?php
}

function wpsm_toprating_shortcode( $atts, $content = null ) {

	extract(shortcode_atts(array(
			'id' => '',
			'postid' => '',
			'full_width' => '0',
		), $atts));

	if(isset($atts['id']) || isset($atts['postid'])):

		if(!empty($atts['id'])){
			$toppost = get_post($atts['id']);
			$module_cats = get_post_meta( $toppost->ID, 'top_review_cat', true );
	    	$module_tag = get_post_meta( $toppost->ID, 'top_review_tag', true );
	    	$module_fetch = get_post_meta( $toppost->ID, 'top_review_fetch', true );
	    	$module_ids = get_post_meta( $toppost->ID, 'manual_ids', true );
	    	$order_choose = get_post_meta( $toppost->ID, 'top_review_choose', true );
	    	$module_desc = get_post_meta( $toppost->ID, 'top_review_desc', true );
	    	$module_desc_fields = get_post_meta( $toppost->ID, 'top_review_custom_fields', true );
	    	$rating_circle = get_post_meta( $toppost->ID, 'top_review_circle', true );
	    	$module_field_sorting = get_post_meta( $toppost->ID, 'top_review_field_sort', true );
	    	$module_order = get_post_meta( $toppost->ID, 'top_review_order', true );
	    	if ($module_fetch ==''){$module_fetch = '10';};
	    	if ($module_desc ==''){$module_desc = 'post';};
	    	if ($rating_circle ==''){$rating_circle = '1';};
		}
		elseif(!empty($atts['postid'])){
			$module_cats = $module_tag = '';
	    	$module_fetch = 1;
	    	$module_ids = explode(',', $atts['postid']);
	    	$order_choose = 'manual_choose';
	    	$module_desc = 'post';
	    	$module_desc_fields = '';
	    	$rating_circle = 1;
	    	$module_field_sorting = '';
	    	$module_order = '';
		}
		ob_start();

    	?>
            <div class="clearfix"></div>

            <?php if ($order_choose == 'cat_choose') :?>
                <?php $query = array(
                    'cat' => $module_cats,
                    'tag' => $module_tag,
                    'posts_per_page' => $module_fetch,
                    'nopaging' => 0,
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'meta_key' => 'rehub_review_overall_score',
                    'orderby' => 'meta_value_num',
                    'meta_query' => array(
                        array(
                        'key' => 'rehub_framework_post_type',
                        'value' => 'review',
                        'compare' => 'LIKE',
                        )
                    )
                );
                ?>
                <?php if(!empty ($module_field_sorting)) {$query['meta_key'] = $module_field_sorting;} ?>
                <?php if($module_order =='asc') {$query['order'] = 'ASC';} ?>
        	<?php elseif ($order_choose == 'manual_choose' && $module_ids !='') :?>
                <?php $query = array(
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'posts_per_page'=> -1,
                    'meta_key' => 'rehub_review_overall_score',
                    'orderby' => 'meta_value_num',
                    'post__in' => $module_ids
                );
                ?>
        	<?php else :?>
                <?php $query = array(
                    'posts_per_page' => $module_fetch,
                    'nopaging' => 0,
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'meta_key' => 'rehub_review_overall_score',
                    'orderby' => 'meta_value_num',
                    'meta_query' => array(
                        array(
                        'key' => 'rehub_framework_post_type',
                        'value' => 'review',
                        'compare' => 'LIKE',
                        )
                    )
                );
                ?>
                <?php if(!empty ($module_field_sorting)) {$query['meta_key'] = $module_field_sorting;} ?>
                <?php if($module_order =='asc') {$query['order'] = 'ASC';} ?>
        	<?php endif ;?>

			<?php
					$title1 = get_the_title();
					$title2 = substr(strstr($title1," "), 1);
					$title3 = substr($title2, 0, -1);
			?>

	        <?php
	        if(class_exists('MetaDataFilter') AND MetaDataFilter::is_page_mdf_data()){
	            $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
	            $_REQUEST['mdf_get_query_args_only'] = true;
	            do_shortcode('[meta_data_filter_results]');
	            $args = $_REQUEST['meta_data_filter_args'];
	            global $wp_query;
	            $wp_query=new WP_Query($args);
	            $_REQUEST['meta_data_filter_found_posts']=$wp_query->found_posts;
	        }
	        else { $wp_query = new WP_Query($query); }
	        ?>
            <?php $wp_query = new WP_Query($query); $i=0; if ($wp_query->have_posts()) :?>
            <div class="top_rating_block<?php if(isset($atts['full_width']) && $atts['full_width']=='1') : ?> full_width_rating<?php else :?> with_sidebar_rating<?php endif;?> list_style_rating">
            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); $i ++?>

	<?php if ($i == 4) : ?>

		<script src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=d832b0fd-64df-497d-84d9-72f7a5634551"></script>

	<?php endif; ?>

                <div class="top_rating_item" id='rank_<?php echo $i?>' >
                    <div class="product_image_col">
                        <figure>
                        	<span class="rank_count"><?php if (($i) == '1') :?><i class="fa fa-trophy"></i><?php else:?><?php echo $i?><?php endif ?></span>
                        	<a href="<?php echo rehub_create_affiliate_link();?>">
                                <?php
                                $showimg = new WPSM_image_resizer();
                                $showimg->use_thumb = true;
                                $width_figure_rating = apply_filters( 'wpsm_top_rating_figure_width', 120 );
                                $height_figure_rating = apply_filters( 'wpsm_top_rating_figure_height', 120 );
                                $showimg->height = $height_figure_rating;
                                $showimg->crop = true;
                                $showimg->show_resized_image();
                                ?>
                        	</a>
                        </figure>
                    </div>
                <div class="desc_col">
                    <h2><a href="<?php echo rehub_create_affiliate_link();?>"><?php the_title();?></a></h2>
						<?php echo re_badge_create('labelsmall'); ?>
					<span class="re-line-badge re-line-small-label badge_1 our-rating-custom">Our Score: <?php echo rehub_get_overall_score();?>/10 </span>
					
					</br>
					
					<?php
				        	$score = rehub_get_overall_score()/2;
                    		$shorty= '[star rating="'.$score.'"]';
                    ?>
                    
                    <?php echo do_shortcode($shorty); ?> 
                    
                    <p>
                        <?php if (($i) == '1') :?>

                    		<?php
                    	    	$prosvalues = vp_metabox('rehub_post.review_post.0.review_post_pros_text');
                    		?>

                    		<?php $pros_cons_wrap = (!empty($prosvalues) || !empty($consvalues) ) ? ' class="pros_cons_values_in_rev"' : ''?>

                    		<!-- PROS CONS BLOCK-->

                    		<div<?php echo $pros_cons_wrap;?> style="border-bottom: 0px !important;">

                    		<?php if(!empty($prosvalues)):?>

                    		<div <?php if(!empty($prosvalues) && !empty($consvalues)):?>class="wpsm-one-half wpsm-column-first"

                    		<?php endif;?>>
                    			
                    			<div class="wpsm_pros">

                    				<ul>
                    					<?php $prosvalues = explode(PHP_EOL, $prosvalues);?>
                    					<?php for($prosvaluescount = 0; $prosvaluescount<3;$prosvaluescount++)
                    					{
                    						echo '<li>'.$prosvalues[$prosvaluescount].'</li>';
                    					}?>
                    				
                    				</ul>
                    				<p style="font-size: 12px;font-weight: 700;color: #a93971;"><a href="<?php echo rehub_create_affiliate_link();?>">LEARN MORE ></a></p>
                    				
                    			</div>
                    		</div>
                    		<?php endif;?>

                    		</div>
               
<?php endif ?>
                    	<?php if ($module_desc =='post') :?>
                    		<?php if ($full_width == 1):?>
                    			<?php kama_excerpt('maxchar=250'); ?>
                    		<?php else:?>
                    			<?php kama_excerpt('maxchar=120'); ?>
                    		<?php endif;?>
                    	<?php elseif ($module_desc =='review') :?>
                    		<?php echo wp_kses_post(vp_metabox('rehub_post.review_post.0.review_post_summary_text')); ?>
                        <?php elseif ($module_desc =='field') :?>
                            <?php if ( get_post_meta(get_the_ID(), $module_desc_fields, true) ) : ?>
                                <?php echo get_post_meta(get_the_ID(), $module_desc_fields, true) ?>
                            <?php endif; ?>
                    	<?php elseif ($module_desc =='none') :?>
                    	<?php else :?>
                    		<?php if ($full_width == 1):?>
                    			<?php kama_excerpt('maxchar=250'); ?>
                    		<?php else:?>
                    			<?php kama_excerpt('maxchar=120'); ?>
                    		<?php endif;?>
                		<?php endif;?>
                    </p>
                    <div class="star"><?php rehub_get_user_results('small', 'yes') ?></div>
                </div>
                <div class="rating_col">
                <?php if ($rating_circle =='1'):?>
                    <?php $rating_score_clean = rehub_get_overall_score(); ?>
                    <div class="top-rating-item-circle-view">
                        <div class="radial-progress" data-rating="<?php echo $rating_score_clean?>">
                            <div class="circle">
                                <div class="mask full">
                                    <div class="fill"></div>
                                </div>
                                <div class="mask half">
                                    <div class="fill"></div>
                                    <div class="fill fix"></div>
                                </div>

                            </div>
                            <div class="inset">
                                <div class="percentage"><?php echo $rating_score_clean?>/10</div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($rating_circle =='2') :?>
                    <div class="score square_score"> <span class="it_score"><?php echo rehub_get_overall_score() ?></span></div>
                <?php else :?>
                    <div class="score"> <span class="it_score"><?php echo rehub_get_overall_score() ?></span></div>
                <?php endif ;?>
                </div>
                
                <div class="buttons_col">
                    <?php	$link = rehub_create_affiliate_link();
                    		$shorty= '[wpsm_button color="pink" size="medium" link="'.$link.'" icon="none" class=""]View Product[/wpsm_button]';
                    ?>

		            <?php echo do_shortcode($shorty); ?></br></br>
		            
		            <a href="<?php echo rehub_create_affiliate_link();?>"><img src="https://freakingbest.com/wp-content/uploads/2018/09/amazon-logo.png"></br>On Amazon.com</a>
		            
                   <!-- <a href="<?php echo rehub_create_affiliate_link();?>" class="read_full"><?php if(rehub_option('rehub_review_text') !='') :?><?php echo rehub_option('rehub_review_text') ; ?><?php else :?><?php _e('Check Price', 'rehub_framework'); ?><?php endif ;?></a> -->
                </div>
                </div>
            <?php endwhile; ?>
            </div>
            <?php wp_reset_query(); ?>
            <?php else: ?><?php _e('No posts for this criteria.', 'rehub_framework'); ?>
            <?php endif; ?>
            <?php

			$short = '[wpsm_button color="pink" size="big" link="https://www.amazon.com/gp/search?ie=UTF8&tag=freakingbest-20&linkCode=ur2&linkId=9d3eb0363a4724a9d2ed01b95240f623&camp=1789&creative=9325&index=aps&keywords='.$title3.'" icon="none" class=""]View All '.$title2.'[/wpsm_button]';
 			?>

		<div align="center">
		            <?php echo do_shortcode($short); ?>
		</div>

 <?php  $wp_query = new WP_Query($query); $i=0; if ($wp_query->have_posts()) :?>

   <h2 style="text-align: center;"><?php echo $title1; ?> of <?php echo date('Y'); ?></h2>

            <div class="top_rating_block<?php if(isset($atts['full_width']) && $atts['full_width']=='1') : ?> full_width_rating<?php else :?> with_sidebar_rating<?php endif;?> list_style_rating">
            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); $i ++?>
                                <div class="top_rating_item" id='rank_<?php echo $i?>'>

                <div class="desc_col">

                            <h2><a href="<?php echo rehub_create_affiliate_link();?>"><?php echo "#".$i?> <?php the_title();?></a></h2>

                            <p style="margin-left: 10px;">
                    			<?php echo wp_kses_post(vp_metabox('rehub_post.review_post.0.review_post_summary_text')); ?>
                    		</p>

                    		<?php
                    	    	$prosvalues = vp_metabox('rehub_post.review_post.0.review_post_pros_text');
                    			$consvalues = vp_metabox('rehub_post.review_post.0.review_post_cons_text');
                    		?>

                    		<?php $pros_cons_wrap = (!empty($prosvalues) || !empty($consvalues) ) ? ' class="pros_cons_values_in_rev"' : ''?>

                    		<!-- PROS CONS BLOCK-->

                    		<div<?php echo $pros_cons_wrap;?> style="border-bottom: 0px !important;">

                    		<?php if(!empty($prosvalues)):?>

                    		<div <?php if(!empty($prosvalues) && !empty($consvalues)):?>class="wpsm-one-half wpsm-column-first"

                    		<?php endif;?>>
                    			
                    			<div class="wpsm_pros">
                    				<div class="title_pros"><?php _e('PROS', 'rehub_framework');?></div>
                    				<ul>
                    					<?php $prosvalues = explode(PHP_EOL, $prosvalues);?>
                    					<?php foreach ($prosvalues as $prosvalue) {
                    						echo '<li>'.$prosvalue.'</li>';
                    					}?>
                    				</ul>
                    			</div>
                    		</div>
                    		<?php endif;?>

                    		<?php if(!empty($consvalues)):?>
                    		<div class="wpsm-one-half wpsm-column-last">
                    			<div class="wpsm_cons">
                    				<div class="title_cons"><?php _e('CONS', 'rehub_framework');?></div>
                    				<ul>
                    					<?php $consvalues = explode(PHP_EOL, $consvalues);?>
                    					<?php foreach ($consvalues as $consvalue) {
                    						echo '<li>'.$consvalue.'</li>';
                    					}?>
                    				</ul>
                    			</div>
                    		</div>
                    		<?php endif;?>
                    		</div>
                    		<?php
                    		$link = rehub_create_affiliate_link();

			$buybutton = '[wpsm_button color="green" size="big" link="'.$link.'" icon="none" class=""]Buy Now[/wpsm_button]';
 			?>

					<div align="center">

		            <?php echo do_shortcode($buybutton); ?>
		            </div>
                    	
                    		<!-- PROS CONS BLOCK END-->
                </div>


                </div>
            <?php endwhile; ?>
            </div>
            <?php wp_reset_query(); ?>
            <?php else: ?><?php _e('No posts for this criteria.', 'rehub_framework'); ?>
            <?php endif; ?>

    	<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	endif;

}
add_shortcode('wpsm_top', 'wpsm_toprating_shortcode');


function best_prod_shortcode( $atts, $content = null )
{

	extract(shortcode_atts(array(
			'id' => '',
			'postid' => '',
			'full_width' => '0',
		), $atts));

	if(isset($atts['id']) || isset($atts['postid'])):

		if(!empty($atts['id'])){
			$toppost = get_post($atts['id']);
			$module_cats = get_post_meta( $toppost->ID, 'top_review_cat', true );
	    	$module_tag = get_post_meta( $toppost->ID, 'top_review_tag', true );
	    	$module_fetch = get_post_meta( $toppost->ID, 'top_review_fetch', true );
	    	$module_ids = get_post_meta( $toppost->ID, 'manual_ids', true );
	    	$order_choose = get_post_meta( $toppost->ID, 'top_review_choose', true );
	    	$module_desc = get_post_meta( $toppost->ID, 'top_review_desc', true );
	    	$module_desc_fields = get_post_meta( $toppost->ID, 'top_review_custom_fields', true );
	    	$rating_circle = get_post_meta( $toppost->ID, 'top_review_circle', true );
	    	$module_field_sorting = get_post_meta( $toppost->ID, 'top_review_field_sort', true );
	    	$module_order = get_post_meta( $toppost->ID, 'top_review_order', true );
	    	if ($module_fetch ==''){$module_fetch = '10';};
	    	if ($module_desc ==''){$module_desc = 'post';};
	    	if ($rating_circle ==''){$rating_circle = '1';};
		}
		elseif(!empty($atts['postid'])){
			$module_cats = $module_tag = '';
	    	$module_fetch = 1;
	    	$module_ids = explode(',', $atts['postid']);
	    	$order_choose = 'manual_choose';
	    	$module_desc = 'post';
	    	$module_desc_fields = '';
	    	$rating_circle = 1;
	    	$module_field_sorting = '';
	    	$module_order = '';
		}
		ob_start();

    	?>
            <div class="clearfix"></div>

            <?php if ($order_choose == 'cat_choose') :?>
                <?php $query = array(
                    'cat' => $module_cats,
                    'tag' => $module_tag,
                    'posts_per_page' => $module_fetch,
                    'nopaging' => 0,
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'meta_key' => 'rehub_review_overall_score',
                    'orderby' => 'meta_value_num',
                    'meta_query' => array(
                        array(
                        'key' => 'rehub_framework_post_type',
                        'value' => 'review',
                        'compare' => 'LIKE',
                        )
                    )
                );
                ?>
                <?php if(!empty ($module_field_sorting)) {$query['meta_key'] = $module_field_sorting;} ?>
                <?php if($module_order =='asc') {$query['order'] = 'ASC';} ?>
        	<?php elseif ($order_choose == 'manual_choose' && $module_ids !='') :?>
                <?php $query = array(
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'posts_per_page'=> -1,
                    'meta_key' => 'rehub_review_overall_score',
                    'orderby' => 'meta_value_num',
                    'post__in' => $module_ids
                );
                ?>
        	<?php else :?>
                <?php $query = array(
                    'posts_per_page' => $module_fetch,
                    'nopaging' => 0,
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'meta_key' => 'rehub_review_overall_score',
                    'orderby' => 'meta_value_num',
                    'meta_query' => array(
                        array(
                        'key' => 'rehub_framework_post_type',
                        'value' => 'review',
                        'compare' => 'LIKE',
                        )
                    )
                );
                ?>
                <?php if(!empty ($module_field_sorting)) {$query['meta_key'] = $module_field_sorting;} ?>
                <?php if($module_order =='asc') {$query['order'] = 'ASC';} ?>
        	<?php endif ;?>

			<?php
					$title1 = get_the_title();
					$title2 = substr(strstr($title1," "), 1);
					$title3 = substr($title2, 0, -1);
			?>

	        <?php
	        if(class_exists('MetaDataFilter') AND MetaDataFilter::is_page_mdf_data()){
	            $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
	            $_REQUEST['mdf_get_query_args_only'] = true;
	            do_shortcode('[meta_data_filter_results]');
	            $args = $_REQUEST['meta_data_filter_args'];
	            global $wp_query;
	            $wp_query=new WP_Query($args);
	            $_REQUEST['meta_data_filter_found_posts']=$wp_query->found_posts;
	        }
	        else { $wp_query = new WP_Query($query); }
	        ?>
            <?php $wp_query = new WP_Query($query); $i=0; if ($wp_query->have_posts()) :?>
            <div class="top_rating_block<?php if(isset($atts['full_width']) && $atts['full_width']=='1') : ?> full_width_rating<?php else :?> with_sidebar_rating<?php endif;?> list_style_rating">
            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); $i ++?>


                <div class="top_rating_item" id='rank_<?php echo $i?>'>
                    <p style="display: table-caption; margin-bottom: -5px; text-align: left;">
                        <?php echo re_badge_create('labelsmall'); ?>
                    </p>
                                   		
                    <div class="product_image_col">
                        <figure>
                        	<span class="rank_count"><?php if (($i) == '1') :?><i class="fa fa-trophy"></i><?php else:?><?php echo $i?><?php endif ?></span>
							<a href="<?php echo rehub_create_affiliate_link();?>">

                                <?php
                                $showimg = new WPSM_image_resizer();
                                $showimg->use_thumb = true;
                                $width_figure_rating = apply_filters( 'wpsm_top_rating_figure_width', 200 );
                                $height_figure_rating = apply_filters( 'wpsm_top_rating_figure_height', 200 );
                                $showimg->height = $height_figure_rating;
                                $showimg->crop = true;
                                $showimg->show_resized_image();
                                ?>
                        	</a>
                        </figure>
                    </div>
                <div class="desc_col">
                            <h2><a href="<?php echo rehub_create_affiliate_link();?>"><?php echo "#".$i?> <?php the_title();?></a></h2>
						
					<span class="re-line-badge re-line-small-label badge_1 our-rating-custom">Our Score: <?php echo rehub_get_overall_score();?>/10 </span> </br>
					
					<?php
				        	$score = rehub_get_overall_score()/2;
                    		$shorty= '[star rating="'.$score.'"]';
                    ?>
                    
                    <?php echo do_shortcode($shorty); ?> 

                    <p>

                    		<?php
                    	    	$prosvalues = vp_metabox('rehub_post.review_post.0.review_post_pros_text');
                    		?>

                    		<?php $pros_cons_wrap = (!empty($prosvalues) || !empty($consvalues) ) ? ' class="pros_cons_values_in_rev"' : ''?>

                    		<!-- PROS CONS BLOCK-->

                    		<div<?php echo $pros_cons_wrap;?> style="border-bottom: 0px !important;">

                    		<?php if(!empty($prosvalues)):?>

                    		<div <?php if(!empty($prosvalues) && !empty($consvalues)):?>class="wpsm-one-half wpsm-column-first"

                    		<?php endif;?>>
                    			
                    			<div class="wpsm_pros">

                    				<ul>
                    					<?php $prosvalues = explode(PHP_EOL, $prosvalues);?>
                    					<?php for($prosvaluescount = 0; $prosvaluescount<5;$prosvaluescount++)
                    					{
                    						echo '<li>'.$prosvalues[$prosvaluescount].'</li>';
                    					}?>
                    		
										<p style="font-size: 12px;font-weight: 700;color: #a93971;"><a href="<?php echo rehub_create_affiliate_link();?>">LEARN MORE ></a></p>

                    				</ul>
                    				
                    			</div>
                    		</div>
                    		<?php endif;?>

                    		</div>
               

                    	<?php if ($module_desc =='post') :?>
                    		<?php if ($full_width == 1):?>
                    			<?php kama_excerpt('maxchar=250'); ?>
                    		<?php else:?>
                    			<?php kama_excerpt('maxchar=120'); ?>
                    		<?php endif;?>
                    	<?php elseif ($module_desc =='review') :?>
                    		<?php echo wp_kses_post(vp_metabox('rehub_post.review_post.0.review_post_summary_text')); ?>
                        <?php elseif ($module_desc =='field') :?>
                            <?php if ( get_post_meta(get_the_ID(), $module_desc_fields, true) ) : ?>
                                <?php echo get_post_meta(get_the_ID(), $module_desc_fields, true) ?>
                            <?php endif; ?>
                    	<?php elseif ($module_desc =='none') :?>
                    	<?php else :?>
                    		<?php if ($full_width == 1):?>
                    			<?php kama_excerpt('maxchar=250'); ?>
                    		<?php else:?>
                    			<?php kama_excerpt('maxchar=120'); ?>
                    		<?php endif;?>
                		<?php endif;?>
                    </p>
                    
                </div>
                                
                <div class="buttons_col">
	            	<?php if(get_post_type($post->ID) == 'product'):?>
	            		<div class="priced_block">
	                        <a href="<?php the_permalink();?>" class="btn_offer_block">
	                            <?php if(rehub_option('rehub_btn_text_aff_links') !='') :?>
	                                <?php echo rehub_option('rehub_btn_text_aff_links') ; ?>
	                            <?php else :?>
	                                <?php _e('Choose offer', 'rehub_framework') ?>
	                            <?php endif ;?>
	                        </a>
                    	</div>
	            	<?php else:?>	
	            		<?php rehub_create_btn('') ;?>
	            	<?php endif;?>                 
                </div>
                </div>
            <?php endwhile; ?>
            </div>
            <?php wp_reset_query(); ?>
            <?php else: ?><?php _e('No posts for this criteria.', 'rehub_framework'); ?>
            <?php endif; ?>
            
 <?php  $wp_query = new WP_Query($query); $i=0; if ($wp_query->have_posts()) :?>

            </div>
            <?php wp_reset_query(); ?>
            <?php else: ?><?php _e('No posts for this criteria.', 'rehub_framework'); ?>
            <?php endif; ?>

    	<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	endif;

}
add_shortcode('best_prod', 'best_prod_shortcode');

function wpsm_toptable_shortcode( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
			'id' => '',
			'full_width' => '0',
		), $atts));
		
	if(isset($atts['id']) && $atts['id']):

		$toppost = get_post($atts['id']);
		$module_cats = get_post_meta( $toppost->ID, 'top_review_cat', true );
		$disable_filters = get_post_meta( $toppost->ID, 'top_review_filter_disable', true ); 
    	$module_tag = get_post_meta( $toppost->ID, 'top_review_tag', true ); 
    	$module_fetch = intval(get_post_meta( $toppost->ID, 'top_review_fetch', true ));  
    	$module_ids = get_post_meta( $toppost->ID, 'manual_ids', true ); 
    	$order_choose = get_post_meta( $toppost->ID, 'top_review_choose', true ); 
	    $module_custom_post = get_post_meta( $toppost->ID, 'top_review_custompost', true );
	    $catalog_tax = get_post_meta( $toppost->ID, 'catalog_tax', true );
	    $catalog_tax_slug = get_post_meta( $toppost->ID, 'catalog_tax_slug', true ); 
    	$catalog_tax_sec = get_post_meta( $toppost->ID, 'catalog_tax_sec', true );
    	$catalog_tax_slug_sec = get_post_meta( $toppost->ID, 'catalog_tax_slug_sec', true );  
    	$image_width = get_post_meta( $toppost->ID, 'image_width', true );    
    	$image_height = get_post_meta( $toppost->ID, 'image_height', true ); 
    	$disable_crop = get_post_meta( $toppost->ID, 'disable_crop', true ); 	       	
    	$module_field_sorting = get_post_meta( $toppost->ID, 'top_review_field_sort', true );
    	$module_order = get_post_meta( $toppost->ID, 'top_review_order', true );
	    $first_column_enable = get_post_meta( $toppost->ID, 'first_column_enable', true );
	    $first_column_rank = get_post_meta( $toppost->ID, 'first_column_rank', true ); 
	    $last_column_enable = get_post_meta( $toppost->ID, 'last_column_enable', true );
	    $first_column_name = (get_post_meta( $toppost->ID, 'first_column_name', true ) !='') ? esc_html(get_post_meta( $toppost->ID, 'first_column_name', true )) : __('Product', 'rehub_framework') ;
	    $last_column_name = (get_post_meta( $toppost->ID, 'last_column_name', true ) !='') ? esc_html(get_post_meta( $toppost->ID, 'last_column_name', true )) : '' ;
	    $affiliate_link = get_post_meta( $toppost->ID, 'first_column_link', true );
	    $rows = get_post_meta( $toppost->ID, 'columncontents', true ); //Get the rows     	    	
    	if ($module_fetch ==''){$module_fetch = '10';}; 
		
		ob_start(); 
    	?>
        <div class="clearfix"></div>
        <?php 
            if ( get_query_var('paged') ) { 
                $paged = get_query_var('paged'); 
            } 
            else if ( get_query_var('page') ) {
                $paged = get_query_var('page'); 
            } 
            else {
                $paged = 1; 
            }        
        ?>        
        <?php if ($order_choose == 'cat_choose') :?>
            <?php $args = array( 
                'cat' => $module_cats, 
                'tag' => $module_tag, 
                'posts_per_page' => $module_fetch, 
                'paged' => $paged,  
                'post_status' => 'publish', 
                'ignore_sticky_posts' => 1, 
            );
            ?> 
            <?php if(!empty ($module_field_sorting)) {$args['meta_key'] = $module_field_sorting; $args['orderby'] = 'meta_value_num';} ?>
            <?php if($module_order =='asc') {$args['order'] = 'ASC';} ?>	                
    	<?php elseif ($order_choose == 'manual_choose' && $module_ids !='') :?>
            <?php $args = array( 
                'post_status' => 'publish', 
                'ignore_sticky_posts' => 1,
                'posts_per_page'=> -1, 
                'orderby' => 'post__in',
                'post__in' => $module_ids

            );
            ?>
	    <?php elseif ($order_choose == 'custom_post') :?>
	        <?php $args = array(  
	            'posts_per_page' => $module_fetch,  
	            'post_status' => 'publish', 
	            'ignore_sticky_posts' => 1,
	            'paged' => $paged, 
	            'post_type' => $module_custom_post, 
	        );
	        ?> 
	        <?php if (!empty ($catalog_tax_slug) && !empty ($catalog_tax)) : ?>
	            <?php $args['tax_query'] = array (
	                array(
	                    'taxonomy' => $catalog_tax,
	                    'field'    => 'slug',
	                    'terms'    => $catalog_tax_slug,
	                ),
	            );?>
	        <?php endif ?>
            <?php if (!empty ($catalog_tax_slug_sec) && !empty ($catalog_tax_sec)) : ?>
                <?php 
                    $args['tax_query']['relation'] = 'AND';
                    $args['tax_query'][] = 
                    array(
                        'taxonomy' => $catalog_tax_sec,
                        'field'    => 'slug',
                        'terms'    => $catalog_tax_slug_sec,
                    );
                ;?>
            <?php endif ?> 	         
            <?php if(!empty ($module_field_sorting)) {$args['meta_key'] = $module_field_sorting; $args['orderby'] = 'meta_value_num';} ?>
            <?php if($module_order =='asc') {$args['order'] = 'ASC';} ?>	                    
    	<?php else :?>
            <?php $args = array( 
                'posts_per_page' => $module_fetch, 
                'paged' => $paged,
                'post_status' => 'publish', 
                'ignore_sticky_posts' => 1, 
            );
            ?>
            <?php if(!empty ($module_field_sorting)) {$args['meta_key'] = $module_field_sorting; $args['orderby'] = 'meta_value_num';} ?>
            <?php if($module_order =='asc') {$args['order'] = 'ASC';} ?>	                             		
    	<?php endif ;?>	

        <?php 
		    $args = apply_filters('rh_module_args_query', $args);
		    $wp_query = new WP_Query($args);
		    do_action('rh_after_module_args_query', $wp_query);
        ?>
        <?php $i=0; if ($wp_query->have_posts()) :?>
        <?php wp_enqueue_script('tablesorter'); wp_enqueue_style('tabletoggle'); ?>
        <?php $sortable_col = ($disable_filters !=1) ? ' data-tablesaw-sortable-col' : '';?>
        <?php $sortable_switch = ($disable_filters !=1) ? ' data-tablesaw-sortable-switch' : '';?>
        <div class="rh-top-table">
            <?php if ($image_width || $image_height):?>
                <style scoped>.rh-top-table .top_rating_item figure > a img{max-height: <?php echo $image_height;?>px; max-width: <?php echo $image_width;?>px;}.rh-top-table .top_rating_item figure > a, .rh-top-table .top_rating_item figure{height: auto;width: auto; border:none;}</style>
            <?php endif;?>        
	        <table data-tablesaw-sortable<?php echo $sortable_switch; ?> class="tablesaw top_table_block<?php if ($full_width =='1') : ?> full_width_rating<?php else :?> with_sidebar_rating<?php endif;?> tablesorter" cellspacing="0">
	            <thead> 
	            <tr class="top_rating_heading">
					<th class="buttons_col_name" <?php echo $sortable_col; ?> data-tablesaw-priority="1">RANK</th>
	                <?php if ($first_column_enable):?><th class="product_col_name" data-tablesaw-priority="persist"></th><?php endif;?>
	                <th class="buttons_col_name" <?php echo $sortable_col; ?> data-tablesaw-priority="1">PRODUCT NAME</th>
					<?php if (!empty ($rows)) {
	                    $nameid=0;                       
	                    foreach ($rows as $row) {                       
	                    $col_name = $row['column_name'];
	                    echo '<th class="col_name"'.$sortable_col.' data-tablesaw-priority="1">'.esc_html($col_name).'</th>';
	                    $nameid++;
	                    } 
	                }
	                ?>
	                <?php if ($last_column_enable):?><th class="buttons_col_name" <?php echo $sortable_col; ?> data-tablesaw-priority="1"><?php echo $last_column_name; ?></th><?php endif;?>                      
	            </tr>
	            </thead>
	            <tbody>
	        <?php while ($wp_query->have_posts()) : $wp_query->the_post(); $i ++?>     
	        <?php $offer_coupon = get_post_meta( get_the_ID(), 'rehub_offer_product_coupon', true );
                    $link_on_thumb = ($affiliate_link =='1') ? rehub_create_affiliate_link() : get_the_permalink(); ?>
	            <tr class="top_rating_item" id='rank_<?php echo $i?>' onclick="window.open('<?php echo $link_on_thumb ?>', '_blank');">
					<td class="prod_rank">
						
	                        <p class="brg_product-card__rank">
								
						<?php echo $i?></p><!--<?php echo re_badge_create('tablelabel'); ?>-->
	                    </td>
	                <?php if ($first_column_enable):?>
	                    <td class="product_image_col">
	                        <figure>
	                            <?php $link_on_thumb_target = ($affiliate_link =='1') ? ' class="btn_offer_block" target="_blank" rel="nofollow"' : '' ; ?>
	                            <a href="<?php echo $link_on_thumb;?>" <?php echo $link_on_thumb_target;?>>
	                                <?php 
		                                $showimg = new WPSM_image_resizer();
		                                $showimg->use_thumb = true;
		                                if(!$image_height) $image_height = 120;
		                                $showimg->height =  $image_height;
		                                if($image_width) {
		                                    $showimg->width =  $image_width;
		                                }
		                                if($disable_crop) {
		                                    $showimg->crop = false;
		                                }else{
		                                    $showimg->crop = true;
		                                }
		                                $showimg->show_resized_image();                                    
	                                ?>  
	                            </a></br>
	                        
	                        <!--<?php
				            	$score = rehub_get_overall_score()/2;
                    	    	$shorty= '[star rating="'.$score.'"]';
                            ?>
                    
                            <?php echo do_shortcode("$shorty"); ?>-->
                        </figure>
	                    </td>
			<td class="prod_title">
						
	                        <p class="brg_product-card__title">
								
								<?php echo do_shortcode("[rehub_title]"); ?></br></p>
								By Saksham
	                    </td>
	                <?php endif;?>
	                <?php 
	                $pbid=0; 
	                if (!empty ($rows)) {
	                                          
	                    foreach ($rows as $row) {
	                    $centered = ($row['column_center']== '1') ? ' centered_content' : '' ;
	                    echo '<td class="column_'.$pbid.' column_content'.$centered.'">';
	                    echo do_shortcode(wp_kses_post($row['column_html']));                       
	                    $element = $row['column_type'];
	                        if ($element == 'meta_value') {
	                            include(rh_locate_template('inc/top/metacolumn.php'));
	                        } else if ($element == 'review_function') {
	                            include(rh_locate_template('inc/top/reviewcolumn.php'));
	                        } else if ($element == 'taxonomy_value') {
	                            include(rh_locate_template('inc/top/taxonomyrow.php'));                            
	                        } else if ($element == 'user_review_function') {
	                            include(rh_locate_template('inc/top/userreviewcolumn.php')); 
	                        } else if ($element == 'static_user_review_function') {
	                            include(rh_locate_template('inc/top/staticuserreviewcolumn.php'));
	                        } else if ($element == 'woo_review') {
	                            include(rh_locate_template('inc/top/wooreviewrow.php'));
	                        } else if ($element == 'woo_btn') {
	                            include(rh_locate_template('inc/top/woobtn.php')); 
	                        } else if ($element == 'woo_vendor') {
	                            include(rh_locate_template('inc/top/woovendor.php')); 
	                        } else if ($element == 'woo_attribute') {
	                            include(rh_locate_template('inc/top/wooattribute.php'));                             
	                        } else {
	                            
	                        };
	                        if ($pbid == 0 && $i == 1):
	                            $prod_name= '[wpsm_custom_meta field="rehub_offer_name"]';

                   //echo '<div class="no_people_used_message">Over 4,000 people have picked '.do_shortcode($prod_name).' today</div>';
                            endif;
	                    echo '</td>';
	                    $pbid++;
	                    } 
	                }
	                ?>
	                <?php if ($last_column_enable):?>
	                    <td class="buttons_col">
	                        <?php if ('product' == get_post_type(get_the_ID())):?>
	                            <?php include(rh_locate_template('inc/top/woobtn.php'));?>
	                        <?php else:?>
	                    	   <?php rehub_create_btn('') ;?>
	                        <?php endif ;?>  
							</br>
		            
		            <img src="https://freakingbest.com/wp-content/uploads/2018/09/amazon-logo.png">

	                    </td>
	                <?php endif ;?>
	            </tr>

	        <?php endwhile; ?>
		        </tbody>
		    </table>
		</div>
        <?php else: ?><?php _e('No posts for this criteria.', 'rehub_framework'); ?>
        <?php endif; ?>
        <?php wp_reset_query(); ?>

    	<?php 
		$output = ob_get_contents();
		ob_end_clean();
		return $output;   
	endif;	

}
add_shortcode('wpsm_toptable', 'wpsm_toptable_shortcode');

?>
