jQuery(function($) {
    $(window).scroll(function(){
        if($(this).scrollTop()>100){
            $('.container_nav__fixed').addClass('show_block');
        }
        else if ($(this).scrollTop()<100){
            $('.container_nav__fixed').removeClass('show_block');
        }
    });
});

$(document).ready(function () {

    //placeholder на формах
    $('input#phoneN2').hover(function (){
        $(this).attr('placeholder', '+7-___-___-__-__');
    },
        function (){
            $(this).attr('placeholder', '');
        });

    $('input#phoneN3').hover(function (){
            $(this).attr('placeholder', '+7-___-___-__-__');
        },
        function (){
            $(this).attr('placeholder', '');
        });
    $('input#phoneN4').hover(function (){
            $(this).attr('placeholder', '+7-___-___-__-__');
        },
        function (){
            $(this).attr('placeholder', '');
        });
    $('input#phoneSP').hover(function (){
            $(this).attr('placeholder', '+7-___-___-__-__');
        },
        function (){
            $(this).attr('placeholder', 'Введите телефон');
        });
    $('input#phonePSP').hover(function (){
            $(this).attr('placeholder', '+7-___-___-__-__');
        },
        function (){
            $(this).attr('placeholder', 'Введите телефон');
        });
    $('.brands__list_slider').slick({
        rows: 4,
        slidesPerRow: 2,
        
        infinite: true,
        //slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1366,
                settings: {
                  	rows: 4,
                    slidesPerRow: 2,
                    //centerMode: true,
                    
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
            },

            {
                breakpoint: 600,
                settings: {
                  	rows: 1,
                    slidesPerRow: 2,
                    //centerMode: true,
                    
                    // slidesToShow: 2,
                    // slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
rows: 1,
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

    $('.nashiraboty__gallery').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        arrows: true,
        responsive: [

            {
                breakpoint: 1366,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
            },

            {
                breakpoint: 880,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
   /* $('.nashiraboty__gallery').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        dots: true,
        centerMode: true,
        focusOnSelect: true
    });*/

    $('.brand__gallery_slider').slick({
        infinite: true,
        slidesToShow: 4,
        autoplay: true,
        lazyLoad: 'ondemand',
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1366,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
            },

            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

    $('.brand__gallery_slider').magnificPopup({
        delegate: 'a',
        type: 'image',
        closeOnContentClick: false,
        closeBtnInside: false,
        mainClass: 'mfp-with-zoom mfp-img-mobile',
        image: {
            verticalFit: true,

        },
        gallery: {
            enabled: true
        },
        zoom: {
            enabled: true,
            duration: 300, // don't foget to change the duration also in CSS
            opener: function(element) {
                return element.find('img');
            }
        }

    });



    // nav
    $('.header-mark-service').click(function(e) {
        // $('.header-mark-service').removeClass('active');
        // $(this).addClass('active');

        $('.header-mark-service.active').not($(this)).removeClass('active');
        $(this).toggleClass('active');
        e.stopPropagation();
        

    });
});