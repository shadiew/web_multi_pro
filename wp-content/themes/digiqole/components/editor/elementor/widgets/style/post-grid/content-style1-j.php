
<div class="block-item-post style9">
   <?php 
   $ts_image_size	= (isset($settings['ts_image_size']))
                     ? $settings['ts_image_size']
                     : 'full';  
   $show_author_avator = isset($settings['show_author_avator'])?
                         $settings['show_author_avator'] 
                         :'no';
   ?>                     
   <?php while ($query->have_posts()) : $query->the_post();?>
         <?php  $cat = get_the_category();?>
         <div class="row">
            <div class="col-md-12">
               <div class="post-block-style post-content feature-contents text-center">
                  <?php if($show_cat == 'yes'): ?> 
                     <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style.php'; ?>
                  <?php  endif; ?>
                        
                  <h3 class="post-title md"><a href="<?php echo esc_url( get_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo wp_trim_words( get_the_title() ,$post_title_crop,''); ?></a></h3>
               
                  <div class="post-meta <?php echo esc_attr($show_author_avator == 'yes'?'ts-avatar-container':''); ?>">
                  <?php if($show_author_avator=='yes'): ?>
                        <?php printf('<span class="ts-author-avatar">%1$s<a href="%2$s">%3$s</a></span>',
                              get_avatar( get_the_author_meta( 'ID' ), 45 ), 
                              esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), 
                              get_the_author()
                           ); ?>
                        <?php endif; ?>  
                        <?php if( $show_author == 'yes') { ?>
                           <?php if ( get_the_author_meta('first_name') != "" || get_the_author_meta('last_name') != "" ) { ?>
                              <span class="post-author"><i class="ts-icon ts-icon-user-solid"></i><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author_meta('first_name');?> <?php echo get_the_author_meta('last_name');?></a></span>
                           <?php } else { ?>
                              <span class="post-author"> <i class="ts-icon ts-icon-user-solid"></i> <?php the_author_posts_link() ?></span>
                           <?php }?>
                        <?php } ?>
                        <?php if($show_date == 'yes') { ?>
                           <span class="post-date"><i class="ts-icon ts-icon-clock-regular"></i> <?php echo get_the_date(get_option('date_format')); ?></span>
                        <?php } ?>
                        <?php if($show_view_count == 'yes'){ ?>
                           <span class="post-view">
                           <i class="ts-icon ts-icon-fire"></i>
                              <?php echo digiqole_get_postview(get_the_ID()); ?>
                           </span>   
                        <?php } ?>
                        
                  </div>
                 <div class="post-thumb-wrap">
                     <?php if (  (has_post_thumbnail())  ) { ?>
                           <div class="post-thumb ts-resize">
                                 <?php if(get_post_format()=='video'): ?>
                                 <?php $video = digiqole_meta_option($query->posts[0]->ID,'featured_video','#');  
                                 
                                 ?>
                                       <div class="post-video-content">
                                          <a href="<?php echo esc_url($video); ?>" class="ts-play-btn">
                                             <i class="ts-icon ts-icon-play-solid" aria-hidden="true"></i>
                                          </a>
                                       </div> 
                                 <?php endif; ?> 
                                 <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('digiqole-medium'); ?></a>
                           </div>
                        <?php } else { ?>
                           <div class="post-thumb block6img-blank"></div>
                        <?php } ?>
                  </div><!-- thumb end -->
                  <p><?php echo esc_html( wp_trim_words(get_the_excerpt(),$post_content_crop,'') );?></p>
                  
                  <?php if($readmore != '') { ?>
                     <a class="post-readmore" href="<?php echo esc_url( get_permalink()); ?>" > <?php echo esc_html($readmore); ?> <i class="ts-icon ts-icon-arrow-right"></i> </a>
                  <?php } ?>
                  
               </div><!-- Post content end -->
            </div>   <!-- col end --> 
         </div><!-- row end -->
        
   <?php endwhile; ?>
                        
 </div><!-- block-item6 -->