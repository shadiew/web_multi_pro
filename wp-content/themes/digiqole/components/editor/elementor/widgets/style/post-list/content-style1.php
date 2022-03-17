 
   <div class="item">
      <div class="post-content">
         <?php if($settings['show_cat'] == 'yes'): $cat = get_the_category(); ?>
            <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style.php'; ?>
         <?php endif; ?>
         <h3 class="post-title">
               <a href="<?php the_permalink(); ?>">  <?php echo esc_html(wp_trim_words(get_the_title(), $settings['post_title_crop'],'')); ?></a>
         </h3>
         <?php if($settings['show_date'] == 'yes'): ?>
            <span class="post-date">
               <i class="ts-icon ts-icon-clock-regular"></i> <?php echo get_the_date(get_option('date_format')); ?>
            </span>
         <?php endif; ?>
      </div>
   </div>