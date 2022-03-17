<?php
/**
 * content.php
 *
 * The default template for displaying content.
 */
$blog_author_show = (isset($settings['show_author'])) 
						? $settings['show_author'] 
						: digiqole_option( 'blog_author_show', 'yes' );

$blog_date_show	= (isset($settings['show_date'])) 
						? $settings['show_date'] 
                  : digiqole_option( 'blog_date_show', 'yes' );
                  
$show_view_count	= (isset($settings['show_view_count'])) 
						? $settings['show_view_count'] 
                  : digiqole_option( 'show_view_count', 'yes' );
                  
$blog_cat_show	    = (isset($settings['show_cat'])) 
						? $settings['show_cat'] 
						: digiqole_option( 'blog_cat_show', 'yes' );
$thumb 					= (isset($thumb))
						? $thumb
                  : [1000, 500];
$crop					= (isset($settings['feature_post_title_crop']))
						? $settings['feature_post_title_crop']
                  : 20;
$show_rating      =   (isset($settings['show_rating']))
                      ? $settings['show_rating']
                      :'no'; 
$loadmore_class = isset($loadmore_class)?$loadmore_class:'';       
$ts_image_size			= (isset($settings['ts_image_size']))
                      ? $settings['ts_image_size']
                      : 'full';    
$show_author_avator = isset($settings['show_author_avator'])?
                        $settings['show_author_avator'] 
                        :'no';                          

?>

   <div class="item ts-overlay-style" style="background-image:url(<?php echo esc_attr(esc_url(get_the_post_thumbnail_url(null, 'digiqole-medium'))); ?>)">
   <a href="<?php the_permalink(); ?>" class="img-link"><?php echo get_the_title(); ?></a>
      <?php if($show_rating=='yes'): ?> 
         <?php digiqole_review_score_limit(); ?>
      <?php endif; ?>   
    
      
         <div class="post-content">
               <?php if($blog_cat_show == 'yes'): ?> 
               
                  <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style.php'; ?>
                     
               <?php endif; ?>
               
               <h3 class="post-title">
                  <a href="<?php the_permalink(); ?>">
                  <?php echo esc_html(wp_trim_words(get_the_title(), $crop,'')); ?>
                  </a>
               </h3>
            
               <ul class="post-meta-info <?php echo esc_attr($show_author_avator == 'yes'?'ts-avatar-container':''); ?>">
                  <?php if($show_author_avator == 'yes'): ?>
                     <?php printf('<li class="ts-author-avatar">%1$s<a href="%2$s">%3$s</a></li>',
                           get_avatar( get_the_author_meta( 'ID' ), 45 ), 
                           esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), 
                           get_the_author()
                        ); ?>
                  <?php endif; ?>  
                  <?php if($blog_author_show == 'yes'): ?>
                     <li class="author">
                     <i class="ts-icon ts-icon-user-solid"></i>
                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                           <?php echo esc_html(get_the_author_meta('display_name')); ?>
                        </a>
                     </li>
                  <?php endif; ?>
                  <?php if($blog_date_show == 'yes'): ?>
                     <li>
                        <i class="ts-icon ts-icon-clock-regular"></i> <?php echo get_the_date(get_option('date_format')); ?>
                     </li>
                  <?php endif; ?>

                  <?php if($show_view_count=='yes'): ?>
                     <li class="active">
                        <i class="ts-icon ts-icon-fire"></i>
                        <?php echo digiqole_get_postview(get_the_ID()); ?>
                     </li>
                  <?php endif; ?>
               </ul>
         </div>
         
   </div>

