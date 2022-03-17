<?php
  $show_cat = (isset($settings['show_cat'])) 
                     ? $settings['show_cat'] 
                     : ''; 
  $show_author = (isset($settings['show_author'])) 
                     ? $settings['show_author'] 
                     : 'no'; 
  $show_desc = (isset($settings['show_desc'])) 
                     ? $settings['show_desc'] 
                     : 'yes'; 
  $show_date = (isset($settings['show_date'])) 
                     ? $settings['show_date'] 
                     : 'no'; 
  $post_content_crop = (isset($settings['desc_limit'])) 
                     ? $settings['desc_limit'] 
                     : '20'; 
  $post_title_crop = (isset($settings['post_title_crop'])) 
                     ? $settings['post_title_crop'] 
                     : '20'; 
 $readmore = (isset($settings['post_readmore'])) 
                     ? $settings['post_readmore'] 
                     : ''; 
 $ts_image_size	= (isset($settings['ts_image_size']))
                      ? $settings['ts_image_size']
                      : 'full';  
$show_author_avator = isset($settings['show_author_avator'])?
                     $settings['show_author_avator'] 
                     :'no'; 

?>

<div class="post-block-style post-float row">
      <div class="col-md-6">
         <div class="post-thumb">
            <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('digiqole-medium'); ?></a>
         </div>
      </div>
      <div class="col-md-6">
         <div class="post-content">
               <?php if($show_cat == 'yes'): ?> 
                  <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style.php'; ?>
               <?php  endif; ?>
               <h3 class="post-title md"><a href="<?php echo esc_url( get_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo esc_html(wp_trim_words( get_the_title() ,$post_title_crop,'') );  ?></a></h3>
               <div class="post-meta <?php echo esc_attr($show_author_avator=='yes'?'ts-avatar-container':''); ?>">
                  <?php if($show_author_avator=='yes'): ?>
                     <?php printf('<span class="ts-author-avatar">%1$s<a href="%2$s">%3$s</a></span>',
                           get_avatar( get_the_author_meta( 'ID' ), 55 ), 
                           esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), 
                           get_the_author()
                        ); ?>
                  <?php endif; ?>  
               <?php if( $show_author == 'yes') { ?>
                     <?php if ( get_the_author_meta('first_name') != "" || get_the_author_meta('last_name') != "" ) { ?>
                        <span class="post-author">
                        <i class="ts-icon ts-icon-user-solid"></i>  
                        <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author_meta('first_name');?> <?php echo get_the_author_meta('last_name');?></a></span>
                     <?php } else { ?>
                           
                        <span class="post-author"> 
                           <i class="ts-icon ts-icon-user-solid"></i> 
                           <?php the_author_posts_link() ?>
                        </span>
                     <?php }?>
               <?php }?>      
               <?php if($show_date == 'yes') { ?>
                     <span class="post-date"> <i class="ts-icon ts-icon-clock-regular"> </i>  <?php echo get_the_date(get_option('date_format')); ?></span>
                     
               <?php } ?>
         
               </div>
               <?php if($show_desc=='yes'): ?>
                  <p> <?php echo esc_html(wp_trim_words(get_the_excerpt(),$post_content_crop,'')); ?> </p>
               <?php endif; ?>

               <?php if($readmore != '') { ?>
                  <a class="post-readmore" href="<?php echo esc_url( get_permalink()); ?>" > <?php echo esc_html($readmore); ?> <i class="ts-icon ts-icon-arrow-right"></i> </a>
               <?php } ?>
         </div><!-- Post content end -->
      </div>
      
      
</div><!-- Post block style end -->