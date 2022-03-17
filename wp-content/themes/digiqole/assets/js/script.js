jQuery( document ).ready( function($){
   "use strict";

 
   /**-------------------------------------------------
     *Fixed Header
     *----------------------------------------------------**/
    $(window).on('scroll', function () {

      /**Fixed header**/
      if ($(window).scrollTop() > 250) {
      $('.navbar-sticky').addClass('sticky fade_down_effect');
      } else {
      $('.navbar-sticky').removeClass('sticky fade_down_effect');
      }
    });
    


  /* ----------------------------------------------------------- */
  /*  Menu
  /* ----------------------------------------------------------- */
   $('.dropdown > a').on('click', function(e) {
      e.preventDefault();
      if($(window).width() > 991)
      {
         location.href = this.href; 
      } 
      var dropdown = $(this).parent('.dropdown');
      dropdown.find('>.dropdown-menu').slideToggle('show');
      $(this).toggleClass('opened');
      return false;
    });

    $('.digiqole-elementskit-menu').each(function(){
        $(this).parents('.navbar').addClass('digiqole-elementskit-menu-container');
    });

    /*==========================================================
                39. scroll bar
    ======================================================================*/


   /* ----------------------------------------------------------- */
   /*  tooltip
   /* ----------------------------------------------------------- */
   $(document).ready(function () {
      $('[data-toggle="tooltip"]').tooltip();
   });

    /*==========================================================
                   search popup
        ======================================================================*/
        if ($('.xs-modal-popup').length > 0) {
            $('.xs-modal-popup').magnificPopup({
                type: 'inline',
                fixedContentPos: false,
                fixedBgPos: true,
                overflowY: 'auto',
                closeBtnInside: false,
                callbacks: {
                    beforeOpen: function beforeOpen() {
                        this.st.mainClass = "my-mfp-slide-bottom xs-promo-popup";
                    }
                }
            });
        }

    /* ----------------------------------------------------------- */
   /*  Video popup
   /* ----------------------------------------------------------- */

  if ($('.ts-play-btn').length > 0) {
   $('.ts-play-btn').magnificPopup({
       type: 'iframe',
       mainClass: 'mfp-with-zoom',
       zoom: {
           enabled: true, // By default it's false, so don't forget to enable it

           duration: 300, // duration of the effect, in milliseconds
           easing: 'ease-in-out', // CSS transition easing function

           opener: function (openerElement) {
               return openerElement.is('img') ? openerElement : openerElement.find('img');
           }
       }
   });
}

  if ($('.play-btn').length > 0) {
   $('.play-btn').magnificPopup({
       type: 'iframe',
       mainClass: 'mfp-with-zoom',
       zoom: {
           enabled: true, // By default it's false, so don't forget to enable it

           duration: 300, // duration of the effect, in milliseconds
           easing: 'ease-in-out', // CSS transition easing function

           opener: function (openerElement) {
               return openerElement.is('img') ? openerElement : openerElement.find('img');
           }
       }
   });
}


   /* ----------------------------------------------------------- */
   /*  Back to top
   /* ----------------------------------------------------------- */

   $(window).on('scroll', function () {
    if ($(window).scrollTop() > $(window).height()) {
       $(".BackTo").fadeIn('slow');
    } else {
       $(".BackTo").fadeOut('slow');
    }

    });
    $("body, html").on("click", ".BackTo", function (e) {
        e.preventDefault();
        $('html, body').animate({
          scrollTop: 0
        }, 800);
    });

    /*==========================================================
                related post slider 
    ======================================================================*/

    related_post_slider($);


    /*==========================================================
                category feature slider
    ======================================================================*/
    $(function() {
        let slide_loop = false;
        let slide_autoplay = true;
        let slide_autoplay_delay = 2500;
        let slider_space_between = 30;
        new Swiper( '.category-feature-slider', {
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
                el: '.swiper-pagination',
                type: 'bullets',
                dynamicBullets: true,
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-next-button',
                prevEl: '.swiper-prev-button',
            },
            
        } );
    });

    

    /*==========================================================
                     scroll bar
        ======================================================================*/

        $('.video-tab-list .post-tab-list li').on('click',function(){
            $('.video-index').html($(this).data('index'));
        });

        if ($(".video-tab-scrollbar").length > 0) {

         $(".video-tab-scrollbar").mCustomScrollbar({
             mouseWheel: true,
             scrollButtons: {
                 enable: true
             }
         });
         
     }
    
          /*==========================================================
                    review rating circle
        ======================================================================*/
        $(function() {
            $('.review-chart').easyPieChart({
              scaleColor: "",
              lineWidth: 5,
              lineCap: 'butt',
              barColor: '#1abc9c',
              trackColor:	"rgba(34,34,34, .4)",
              size: 70,
              animate: 70
            });
          });

         /*==========================================================
                   reading progressbar
        ======================================================================*/  
       
        window.onscroll = function() { reading_progressbar() };

         function reading_progressbar() {
               var digiqole_winScroll = document.body.scrollTop || document.documentElement.scrollTop;
               var digiqole_height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
               var digiqole_scrolled = (digiqole_winScroll / digiqole_height) * 100;
               if (document.getElementById("readingProgressbar")) {
                document.getElementById("readingProgressbar").style.width = digiqole_scrolled + "%";
            }
         }
       
        reading_progress_bar_post();
  
         function reading_progress_bar_post() {
           
            var progressWrap = $('.digiqole_progress_container');
            var entry_top = $('.entry-content');
       
            if (entry_top.length > 0) {
                if ( progressWrap.length > 0 ) {
                    var didScroll = false,
                        windowWrap = $(window),

                        contentWrap = $('.entry-content'),
                        contentHeight = contentWrap.height(),
                        windowHeight = windowWrap.height() * .85;
        
                    $(window).scroll(function() {
                        didScroll = true;
                    });
        
                    $(window).on('resize', function(){
                        windowHeight = windowWrap.height() * .85;
                        progressReading();
                    });
        
                    setInterval(function() {
                        if ( didScroll ) {
                            didScroll = false;
                            progressReading();
                        }
                    }, 150);
        
                    var progressReading = function() {
        
                        var windowScroll = windowWrap.scrollTop(),
                            contentOffset = contentWrap.offset().top,
                            contentScroll = ( windowHeight - contentOffset ) + windowScroll,
                            progress = 0;
        
                        if ( windowHeight > contentHeight + contentOffset ) {
                            progressWrap.find('.progress-bar').width(0);
                        } else {
                            if ( contentScroll > contentHeight ) {
                                progress = 100;
                            } else if ( contentScroll > 0 ) {
                                progress = ( contentScroll / contentHeight ) * 100 ;
                            
                            }
        
                            progressWrap.find('.progress-bar').width(progress + '%');
                        }
                    }
                }
            } else{
                return false;
            }
        }

        /* ----------------------------------------------------------- */
        /*sticky sidebar
        /* ----------------------------------------------------------- */
        sticky_sidebar($);



    /* ----------------------------------------------------------- */
    /*single  post gallery  slider
    /* ----------------------------------------------------------- */
    $(function() {
        let $container = $( '.single-post-gallery-wrap' );            
        if ( $container.length > 0 ) {
            let slides_to_show = 4;
            let slider_space_between = 10;			
            // eslint-disable-next-line
            var galleryTop = new Swiper(".single-post-sync-slider1", {
                loop: true,
                loopedSlides: 5, //looped slides should be the same
                thumbs: {
                    swiper: new Swiper( '.sync-slider2', {
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
                            slidesPerView: 3,
                        },
                    },
                    } ),
                },
            } );
        }
    });

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


     /*==========================================================
                       scroll
      ======================================================================*/
   
    $.fn.isInViewport = function () {
        let elementTop = $(this).offset().top;
        let elementBottom = elementTop + $(this).outerHeight();
    
        let viewportTop = $(window).scrollTop();
        let viewportBottom = viewportTop + $(window).height();
    
        return elementBottom > viewportTop && elementTop < viewportBottom;
    };


    $(window).on('scroll', function () {

        $('.ajax-loader-current-url').each(function(){
            if ($(this).isInViewport()) {
                var current_url = $(this).data('current-url');
                if(window.location.href != current_url){
                    window.history.pushState('html', 'title', current_url);
                }
            }
        });

        $('.blog-ajax-load-more-trigger').each(function(index){
            if ($(this).isInViewport()) {
                var current_post = index + 1;
                var _this = $(this);
                var next_post_url = $(this).data('next-post-url');
                var max_posts = $(this).data('max-posts');
                var content_loaded = $(this).data('content-loaded');

                if( current_post >= max_posts){
                    _this.fadeOut(2000);
                    return;
                }

                if(content_loaded == 'no'){
                    $.ajax({
                        type: "GET",
                        url: next_post_url,
                        success: function (content) {
                            var content = $(content).find('#blog-ajax-load-more-container').html();
                            _this.after(content);
                            related_post_slider($);
                            sticky_sidebar($);
                        }
                    });
                }
                _this.fadeOut(2000);
                _this.data('content-loaded', 'yes');
            } 
        });
    });
    /*--------------scroll end---------*/

    
    
    /*==========================================================
                             Dark-light mode
    ======================================================================*/
    $(document).on('click', '.change-mode', function(){
        
		var defaultSkin = $('html').data('skin'),
		    siteSkin    = 'light';

		if( $('html').hasClass( 'dark-mode' ) ){
			siteSkin = 'dark';
		}

    var switchTo = ( siteSkin == 'dark' ) ? 'light' : 'dark';
            if( 'undefined' != typeof localStorage ){
                localStorage.setItem( 'digi-skin', switchTo );
            }

            if( defaultSkin == switchTo ){
                $('html').removeClass('digi-skin-switch');
            }
            else{
                $('html').addClass('digi-skin-switch');
            }

            if( switchTo == 'dark' ){
                $('html').addClass( 'dark-mode' );			
            }
            else{
                $('html').removeClass( 'dark-mode' );
            }
    });

    const loadScripts_PreloadTimer=setTimeout(triggerScriptLoader_Preload,8e3),userInteractionEvents_Preload=["mouseover","keydown","touchstart","touchmove","wheel"];
    function triggerScriptLoader_Preload(){
        document.querySelector("html").classList.add("is-active-page"),clearTimeout(loadScripts_PreloadTimer)
    }
    userInteractionEvents_Preload.forEach(function(e){
        window.addEventListener(e,triggerScriptLoader_Preload,{
            passive:!0
        })
    });


    if ( fontList ) {
    const observeFontList = fontList.map((fontName) => {
		const observer = new FontFaceObserver(fontName);
		return observer.load();
	});

	Promise.all(observeFontList).then(function(){
        document.documentElement.className += " fonts-loaded";
    });
    } else {
        const barlowObserver = new FontFaceObserver('Barlow');
        const robotoObserver = new FontFaceObserver('Roboto');
        Promise.all([
            barlowObserver.load(),
            robotoObserver.load()
            ]).then(function(){
            document.documentElement.className += " fonts-loaded";
        });

    }

} );

// Related post slider function

function related_post_slider($){
    let slide_loop = true;
    let slide_autoplay = true;
    let slide_autoplay_delay = 2500;
    let slider_space_between = 30;
    new Swiper( '.popular-grid-slider', {
        slidesPerView: 3,
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
            el: '.swiper-pagination',
            type: 'bullets',
            dynamicBullets: true,
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-next-button',
            prevEl: '.swiper-prev-button',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                slidesPerGroup: 1,
            },
            767: {
                slidesPerView: 2,
                slidesPerGroup: 1,
            },
            1024: {
                slidesPerView: 3,
                slidesPerGroup: 1,
            },
        },
        
    } );

}

function sticky_sidebar($){
    if(digiqole_ajax.blog_sticky_sidebar == 'yes'){               
    // Sticky sidebar
    $('.digiqole-content > .col-lg-4, .digiqole-content > .col-lg-8').theiaStickySidebar({
        additionalMarginTop: 30
      });
    }
}
