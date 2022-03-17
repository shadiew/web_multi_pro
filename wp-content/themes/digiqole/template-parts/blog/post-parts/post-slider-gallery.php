
   <?php

      $gallery_image =  fw_get_db_post_option(get_the_ID(), 'featured_gallery');

      if( $gallery_image){
      ?>
        <div class="single-post-gallery-wrap">
            <div id="single-post-sync-slider1" class="swiper-container single-post-sync-slider single-post-sync-slider1">
               <div class="swiper-wrapper">
                  <?php foreach ($gallery_image as $gallery): ?>
                     <div class="swiper-slide">
                        <div class="item">
                           <a class="xs-modal-popup" href="<?php echo esc_url($gallery['url']); ?>"><img src="<?php echo esc_url($gallery['url']); ?>" alt="<?php echo esc_attr(the_title_attribute()); ?>"></a>
                        </div>
                     </div>
                  <?php endforeach; ?>
               </div>
            </div>

            <div id="single-post-sync-slider2" class="swiper-container single-post-sync-slider sync-slider2">
               <div class="swiper-wrapper">
                  <?php foreach ($gallery_image as $gallery): ?>
                     <div class="swiper-slide">
                        <div class="item">
                           <img src="<?php echo esc_url($gallery['url']); ?>" alt="<?php echo esc_attr(the_title_attribute()); ?>">
                        </div>
                     </div>
                  <?php endforeach; ?>
               </div>
            </div>
         </div>

      <?php
      }
 ?>