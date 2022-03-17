 
<div class="ts-posts-toptitle-item-1">
   <?php foreach($query as $post): ?>
   <div class="ts-posts-toptitle-item">
      <div class="row">
         <div class="col-md-12">
            <h3 class="post-title md"><a href="<?php echo esc_url( get_permalink($post->ID)); ?>" rel="bookmark" title="<?php the_title_attribute($post->ID); ?>"><?php echo esc_html(wp_trim_words( get_the_title($post->ID) ,$post_title_crop,'') );  ?></a></h3>

            <div class="post-meta <?php echo esc_attr($show_author_avator=='yes'?'ts-avatar-container':''); ?>">
               <?php if($show_author_avator=='yes'): ?>
                  <?php printf('<span class="ts-author-avatar">%1$s<a href="%2$s">%3$s</a></span>',
                        get_avatar( get_the_author_meta( 'ID' ), 45 ), 
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
                     <span class="post-date"> <i class="ts-icon ts-icon-clock-regular"> </i>  <?php echo get_the_date(get_option('date_format'),$post->ID); ?></span>
                     
               <?php } ?>

               <?php if($show_comment == 'yes') { ?>
                     <span class="post-comment"> <i class="ts-icon ts-icon-comments"> </i>  <?php echo get_comments_number($post->ID); ?></span>                     
               <?php } ?>

               <?php if($show_view_count == 'yes'){ ?>
                     <span class="post-view">
                     <i class="ts-icon ts-icon-fire" aria-hidden="true"></i>
                        <?php echo digiqole_get_postview($post->ID); ?>
                     </span>   
               <?php } ?>   
            </div>
         </div>
      </div>
      <div class="align-items-center post-block-style post-float row <?php echo esc_attr(($col_reverse == 'yes')? 'flex-row-reverse' : ''); ?>">
         <div class="col-md-6 order-md-last">
            <div class="post-thumb">
               <a href="<?php echo esc_url( get_permalink($post->ID) ); ?>" rel="bookmark" title="<?php the_title_attribute($post->ID); ?>"><?php echo get_the_post_thumbnail($post->ID, 'digiqole-medium'); ?></a>
            </div>
         </div>
         <div class="col-md-6">
            <div class="post-content">                 
                  <?php if($show_desc=='yes'): ?>
                     <p> <?php echo esc_html(wp_trim_words(get_the_excerpt($post->ID),$post_content_crop,'')); ?> </p>
                  <?php endif; ?>

                  <?php if($readmore != '') { ?>
                     <a class="post-readmore" href="<?php echo esc_url( get_permalink($post->ID)); ?>" > <?php echo esc_html($readmore); ?> <i class="ts-icon ts-icon-arrow-right"></i> </a>
                  <?php } ?>
            </div><!-- Post content end -->
         </div>
         
   </div><!-- Post block style end -->
   </div>

   <?php endforeach; ?>
</div>  
<?php wp_reset_postdata(); ?>