<div class="post-quote-wrapper">
   <div class="post-quote-content text-center">
      <div class="entry-header">

           
            <i class="quote ts-icon ts-icon-quote1"></i>

            <h2 class="entry-title">
               <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
      <?php if ( is_sticky() ) {
            echo '<sup class="meta-featured-post"> <i class="ts-icon ts-icon-thumbtack"></i> ' . esc_html__( 'Sticky', 'digiqole' ) . ' </sup>';
      } ?>
      </div>
   </div>
</div>