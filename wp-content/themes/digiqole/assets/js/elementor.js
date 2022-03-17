
( function ($, elementor) {
	"use strict";

   
    var DIGIQOLE = {

        init: function () {
            
            var widgets = {
               'newszone-post-grid-slider.default': DIGIQOLE.Newszone_post_grid_slider,
               'newszone-post-block-slider.default': DIGIQOLE.Newszone_post_block_slider,
               'newszone-post-slider.default': DIGIQOLE.Newszone_post_slider,
               'newszone-editor-pick-post-slider.default': DIGIQOLE.Newszone_editor_pick_post_slider,
               'newszone-video-post-slider2.default': DIGIQOLE.Newszone_video_slider2,
               'newszone-post-grid-loadmore.default': DIGIQOLE.Newszone_post_grid_loadmore,
               //'digiqole-newsticker.default': DIGIQOLE.Newszone_news_ticker,
               'newszone-main-slider.default': DIGIQOLE.MainSlider,
               
		          
            };
            $.each(widgets, function (widget, callback) {
                elementor.hooks.addAction('frontend/element_ready/' + widget, callback);
            });
           
      },

      /* ----------------------------------------------------------- */
      /*  Grid slider
      /* ----------------------------------------------------------- */
      Newszone_post_grid_slider:function($scope){
         let $container = $scope.find( '.weekend-top' );
         if ( $container.length > 0 ) {
            let controls = $container.data( 'controls' );
            let slide_loop = false;
            let slide_autoplay = Boolean(controls.auto_nav_slide?true:false);
            let slides_to_show = parseInt( controls.item_count );
            let slide_autoplay_delay = 2500;
            let slider_space_between = 30;
            let widget_id = controls.widget_id;
			
				// eslint-disable-next-line
				$($container).each(function (index, element) {
					let $element = $( element ).find( '.swiper-container' );
					new Swiper( $element, {
						slidesPerView: slides_to_show,
						centeredSlides: false,
						spaceBetween: slider_space_between,
						loop: slide_loop,
						slidesPerGroup: slides_to_show,
						wrapperClass: 'swiper-wrapper',
						slideClass: 'swiper-slide',
						grabCursor: false,
						allowTouchMove: true,
						speed: 1200, //slider transition speed
						parallax: true,
						autoplay: slide_autoplay ? { delay: slide_autoplay_delay } : false,
						effect: 'slide',
						mousewheelControl: 1,
						pagination: {
							el: '.swiper-pagination',
							type: 'bullets',
							dynamicBullets: true,
							clickable: true,
						},
						navigation: {
							nextEl: `.swiper-next-${widget_id}`,
							prevEl: `.swiper-prev-${widget_id}`,
						},
                  breakpoints: {
							0: {
								slidesPerView: 1,
								slidesPerGroup: 1,
							},
							767: {
								slidesPerView: 2,
								slidesPerGroup: 2,
							},
							1024: {
								slidesPerView: slides_to_show,
								slidesPerGroup: slides_to_show,
							},
						},
						
					} );
				} );
			}         
      
      },

      /* ----------------------------------------------------------- */
      /*  Post block slider
      /* ----------------------------------------------------------- */
      Newszone_post_block_slider:function($scope){

         let $container = $scope.find( '.block-slider' );
         if ( $container.length > 0 ) {
            let controls = $container.data( 'controls' );
            let slide_loop = true;
            let slide_autoplay = Boolean(controls.auto_nav_slide?true:false);
            let slides_to_show = parseInt( controls.item_count );
            let slide_autoplay_delay = 2500;
            let slider_space_between = 30;
         
            // eslint-disable-next-line
            $($container).each(function (index, element) {
               let $element = $( element ).find( '.swiper-container' );
               new Swiper( $element, {
                  slidesPerView: slides_to_show,
                  centeredSlides: false,
                  spaceBetween: slider_space_between,
                  loop: slide_loop,
                  slidesPerGroup: slides_to_show,
                  wrapperClass: 'swiper-wrapper',
                  slideClass: 'swiper-slide',
                  grabCursor: false,
                  allowTouchMove: true,
                  speed: 1200, //slider transition speed
                  parallax: true,
                  autoplay: slide_autoplay ? { delay: slide_autoplay_delay } : false,
                  effect: 'slide',
                  mousewheelControl: 1,
                  pagination: {
                     el: '.swiper-pagination',
                     type: 'bullets',
                     dynamicBullets: true,
                     clickable: true,
                  },
                  breakpoints: {
                     0: {
                        slidesPerView: 1,
                        slidesPerGroup: 1,
                     },
                     767: {
                        slidesPerView: 2,
                        slidesPerGroup: 2,
                     },
                     1024: {
                        slidesPerView: slides_to_show,
                        slidesPerGroup: slides_to_show,
                     },
                  },
                  
               } );
            } );
         }               
      
      },
           
         /* ----------------------------------------------------------- */
         /*  post slider
         /* ----------------------------------------------------------- */
         Newszone_post_slider:function($scope){

            let $container = $scope.find( '.post-slider' );
            if ( $container.length > 0 ) {
               let controls = $container.data( 'controls' );
               let slide_loop = Boolean(controls.slider_loop?true:false);
               let slide_autoplay = Boolean(controls.auto_nav_slide?true:false);
               let slides_to_show = parseInt( controls.item_count );
               let slide_autoplay_delay = 2500;
               let slider_space_between = parseInt(controls.margin);
               let widget_id = controls.widget_id;
            
               // eslint-disable-next-line
               $($container).each(function (index, element) {
                  let $element = $( element ).find( '.swiper-container' );
                  new Swiper( $element, {
                     slidesPerView: slides_to_show,
                     centeredSlides: false,
                     spaceBetween: slider_space_between,
                     loop: slide_loop,
                     slidesPerGroup: slides_to_show,
                     wrapperClass: 'swiper-wrapper',
                     slideClass: 'swiper-slide',
                     grabCursor: false,
                     allowTouchMove: true,
                     speed: 3000, //slider transition speed
                     parallax: true,
                     autoplay: slide_autoplay ? { delay: slide_autoplay_delay } : false,
                     effect: 'slide',
                     mousewheelControl: 1,
                     pagination: {
                        el: '.swiper-pagination',
                        type: 'bullets',
                        dynamicBullets: true,
                        clickable: true,
                     },
                     navigation: {
                        nextEl: `.swiper-next-${widget_id}`,
                        prevEl: `.swiper-prev-${widget_id}`,
                     },
                     breakpoints: {
                        0: {
                           slidesPerView: 1,
                           slidesPerGroup: 1,
                        },
                        767: {
                           slidesPerView: 2,
                           slidesPerGroup: 2,
                        },
                        1024: {
                           slidesPerView: slides_to_show,
                           slidesPerGroup: slides_to_show,
                        },
                     },
                     
                  } );
               } );
            }
          
         },

         /* ----------------------------------------------------------- */
         /*video  post slider
         /* ----------------------------------------------------------- */
         // Newszone_video_slider2
         Newszone_video_slider2: function( $scope ) {
            let $container = $scope.find( '.digiqole-video-slider' );
            
            if ( $container.length > 0 ) {
               let slides_to_show = 4;
               let slider_space_between = 0;			
               // eslint-disable-next-line
               var galleryTop = new Swiper(".digiqole-video-slider-container", {
                  loop: true,
                  loopedSlides: 5, //looped slides should be the same
                  thumbs: {
                     swiper: new Swiper( '.digiqole-video-slider2-container', {
                        spaceBetween: slider_space_between,
                        slidesPerView: slides_to_show,
                        loop: true,
                        freeMode: true,
                        loopedSlides: 5, //looped slides should be the same
                        watchSlidesVisibility: true,
                        watchSlidesProgress: true,
                        breakpoints: {
                           0: {
                              slidesPerView: 1,
                           },
                           767: {
                              slidesPerView: 2,
                           },
                           1024: {
                              slidesPerView: 4,
                           },
                        },
                     } ),
                  },
               } );
            }
		   },         
         
         /* ----------------------------------------------------------- */
         /*   Post grid ajax load
         /* ----------------------------------------------------------- */
         
         Newszone_post_grid_loadmore:function($scope){
            var $container = $scope.find('.digiqole-post-grid-loadmore');
            if ($container.length > 0) {
               $container.on('click',function(event){
                  event.preventDefault();
                  if ($.active > 0) {
                     return;    
                   }
                  var $that = $(this);
                  var ajaxjsondata = $that.data('json_grid_meta');
                  var digiqole_json_data = Object (ajaxjsondata);

                  var contentwrap = $scope.find('.grid-loadmore-content'), // item contentwrap
                     postperpage = parseInt(digiqole_json_data.posts_per_page), // post per page number
                     showallposts = parseInt(digiqole_json_data.total_post); // total posts count

                     var items = contentwrap.find('.grid-item'),
                     totalpostnumber = parseInt(items.length),
                     paged =  parseInt( totalpostnumber / postperpage ) + 1; // paged number

                     $.ajax({
                        url: digiqole_ajax.ajax_url,
                        type: 'POST',
                        data: {action: 'digiqole_post_ajax_loading',ajax_json_data: ajaxjsondata,paged:paged},
                        beforeSend: function(){

                           $('<i class="ts-icon ts-icon-spinner fa-spin" style="margin-left:10px"></i>').appendTo( "#digiqole-post-grid-loadmore" ).fadeIn(100);
                        },
                        complete:function(){
                           $scope.find('.digiqole-post-grid-loadmore .fa-spinner ').remove();
                        }
                     })

                     .done(function(data) {

                           var $pstitems = $(data);
                           $scope.find('.grid-loadmore-content').append( $pstitems );
                           var newLenght  = contentwrap.find('.grid-item').length;

                           if(showallposts <= newLenght){
                              $scope.find('.digiqole-post-grid-loadmore').fadeOut(300,function(){
                                 $scope.find('.digiqole-post-grid-loadmore').remove();
                              });
                           }

                     })

                     .fail(function() {
                        $scope.find('.digiqole-post-grid-loadmore').remove();
                     });

               });
         }


         },
         
      /* ----------------------------------------------------------- */
      /*  Editor pick post slider
      /* ----------------------------------------------------------- */
      Newszone_editor_pick_post_slider:function($scope){

         let $container = $scope.find( '.editor-pick-post-slider' );
         if ( $container.length > 0 ) {
            let controls = $container.data( 'controls' );
            let slide_loop = true;
            let slide_autoplay = Boolean(controls.auto_nav_slide?true:false);
            let slides_to_show = parseInt( controls.item_count );
            let slide_autoplay_delay = 2500;
            let slider_space_between = 0;
            let widget_id = controls.widget_id;
         
            // eslint-disable-next-line
            $($container).each(function (index, element) {
               let $element = $( element ).find( '.swiper-container' );
               new Swiper( $element, {
                  slidesPerView: slides_to_show,
                  centeredSlides: false,
                  spaceBetween: slider_space_between,
                  loop: slide_loop,
                  slidesPerGroup: slides_to_show,
                  wrapperClass: 'swiper-wrapper',
                  slideClass: 'swiper-slide',
                  grabCursor: false,
                  allowTouchMove: true,
                  speed: 3000, //slider transition speed
                  parallax: true,
                  autoplay: slide_autoplay ? { delay: slide_autoplay_delay } : false,
                  effect: 'slide',
                  mousewheelControl: 1,
                  pagination: {
                     el: '.swiper-pagination',
                     type: 'bullets',
                     dynamicBullets: true,
                     clickable: true,
                  },
                  navigation: {
                     nextEl: `.swiper-next-${widget_id}`,
                     prevEl: `.swiper-prev-${widget_id}`,
                  },
                  breakpoints: {
                     0: {
                        slidesPerView: 1,
                        slidesPerGroup: 1,
                     },
                     767: {
                        slidesPerView: 1,
                        slidesPerGroup: 1,
                     },
                     1024: {
                        slidesPerView: slides_to_show,
                        slidesPerGroup: slides_to_show,
                     },
                  },
                  
               } );
            } );
         }
          
      },

      // mainSlider
		MainSlider: function( $scope ) {
			let $container = $scope.find( '.main-slider' );
         if ( $container.length > 0 ) {
            let controls = $container.data( 'controls' );
            let slide_loop = false;
            let slide_autoplay = Boolean(controls.auto_nav_slide?true:false);
            let slide_autoplay_delay = 2500;
            let slider_space_between = 0;
            let widget_id = controls.widget_id;
			
				// eslint-disable-next-line
				$($container).each(function (index, element) {
					let $element = $( element ).find( '.swiper-container' );
					new Swiper( $element, {
						slidesPerView: 1,
						centeredSlides: false,
						spaceBetween: slider_space_between,
						loop: slide_loop,
						slidesPerGroup: 1,
						wrapperClass: 'swiper-wrapper',
						slideClass: 'swiper-slide',
						grabCursor: false,
						allowTouchMove: true,
						speed: 1200, //slider transition speed
						parallax: true,
						autoplay: slide_autoplay ? { delay: slide_autoplay_delay } : false,
						effect: 'slide',
						mousewheelControl: 1,
						pagination: {
							el: '.main-pagination',
							type: 'bullets',
							dynamicBullets: true,
							clickable: true,
						},
						navigation: {
							nextEl: `.swiper-next-${widget_id}`,
							prevEl: `.swiper-prev-${widget_id}`,
						},
						
					} );
				} );
			}

         let $container2 = $scope.find( '.main-slide' );
         if ( $container2.length > 0 ) {            
            let controls2 = $container2.data( 'controls' );
            let slide_loop = true;
            let slide_autoplay = Boolean(controls2.auto_nav_slide?true:false);
            let slide_autoplay_delay = 2500;
            let slider_space_between2 = 30;
            let widget_id2 = controls2.widget_id;
         
				// eslint-disable-next-line
				$($container2).each(function (index, element) {
					let $element = $( element ).find( '.swiper-container' );
					new Swiper( $element, {
						slidesPerView: 'auto',
						centeredSlides: true,
						spaceBetween: slider_space_between2,
						loop: slide_loop,
						slidesPerGroup: 1,
						wrapperClass: 'swiper-wrapper',
						slideClass: 'swiper-slide',
						grabCursor: false,
						allowTouchMove: true,
						speed: 1200, //slider transition speed
						parallax: true,
						autoplay: slide_autoplay ? { delay: slide_autoplay_delay } : false,
						effect: 'slide',
						mousewheelControl: 1,
						pagination: {
							el: '.swiper-pagination',
							type: 'bullets',
							dynamicBullets: true,
							clickable: true,
						},
						navigation: {
							nextEl: `.swiper-next-${widget_id2}`,
							prevEl: `.swiper-prev-${widget_id2}`,
						},
						
					} );
				} );
			}

		},

    };
    $(window).on('elementor/frontend/init', DIGIQOLE.init);

    /*==========================================================
                        Preloader
      ======================================================================*/
      $(window).on('load', function () {
         
         setTimeout(() => {
            $('#preloader').addClass('loaded');
         }, 1000);
         
         });
         
      // preloader close
      $('.preloader-cancel-btn').on('click', function (e) {
         e.preventDefault();
         if (!($('#preloader').hasClass('loaded'))) {
            $('#preloader').addClass('loaded');
         }
      });
    
}(jQuery, window.elementorFrontend) ); 