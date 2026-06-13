$(document).ready(function () {
    /*$('.main_slide:first-child').addClass('active')
    $('.main_slide:first-child').find('.main_slide-prev').slideUp(0)
    $('.main_slide:first-child').find('.main_slide-wrapper').slideDown(0)
    $('.main_slide:first-child').find('.main_slide-counter_prev').remove()
    $('.main_slide:last-child').find('.main_slide-counter_next').remove()
    $('.main_slide:last-child').find('.main_slide-arrow.next').addClass('disabled')
    $('.main_slide:first-child').find('.main_slide-arrow.prev').addClass('disabled')
    $('.main_img:first-child').slideDown(0)

    $('.main_img').css('height', $('.main_slider').height())

    $('.main_slide-counter_next').each( function (){
        $(this).text('0' + ($(this).closest('.main_slide').index() + 2))
    })
    $('.main_slide-counter_prev').each( function (){
        $(this).text('0' + $(this).closest('.main_slide').index())
    })*/
    
        $(document).on('af_complete', function(event, response) {
        if(response.success){
        $.fancybox.close();
        }
    });


    $('.main_imgs').slick({
        arrows: false,
        vertical: true,
        infinite: false,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    fade: true,
                    vertical: false,
                }
            },
        ]
    })
    
       $(document).on('click', '.popup_btn', function (event) {
        event.preventDefault();

        const idPopup = ($(this).attr('href') || '').replace(/^\.?\//, '#');
        if (!idPopup || !$(idPopup).length || !$.fancybox) {
            return;
        }

        $.fancybox.close();
        $.fancybox.open({
            src: idPopup,
            type: 'inline',
            touch: false,
            autoFocus: false,
        });
    });

    $('.about_slider').slick({
        dots: false,
        prevArrow: '<button type="button" class="slick-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.8791 15.4468L7.4285 9.99617L12.8791 4.54558C13.2045 4.22017 13.2045 3.6925 12.8791 3.36708C12.5537 3.04167 12.026 3.04167 11.7006 3.36708L5.66075 9.40692C5.50442 9.56325 5.41667 9.77517 5.41667 9.99617C5.41667 10.2172 5.50442 10.4292 5.66075 10.5854L11.7006 16.6253C11.7413 16.666 11.7851 16.7016 11.8313 16.7321C12.1548 16.9457 12.5943 16.9101 12.8791 16.6253C13.2045 16.2999 13.2045 15.7722 12.8791 15.4468Z" fill="white"/></svg></button>',
        nextArrow: '<button type="button" class="slick-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.12091 15.4468L12.5715 9.99617L7.12091 4.54558C6.79547 4.22017 6.79547 3.6925 7.12091 3.36708C7.44635 3.04167 7.97398 3.04167 8.29941 3.36708L14.3392 9.40692C14.4956 9.56325 14.5833 9.77517 14.5833 9.99617C14.5833 10.2172 14.4956 10.4292 14.3392 10.5854L8.29941 16.6253C8.25874 16.666 8.2149 16.7016 8.16869 16.7321C7.84523 16.9457 7.40566 16.9101 7.12091 16.6253C6.79547 16.2999 6.79547 15.7722 7.12091 15.4468Z" fill="white"/></svg></button>',
    })



    $('.main_slide-arrow').click(function () {
        if ($(this).hasClass('next') && !$(this).hasClass('disabled')) {
            $(this).closest('.main_slide').removeClass('active')
            $(this).closest('.main_slide').find('.main_slide-prev').slideDown(400)
            $(this).closest('.main_slide').find('.main_slide-wrapper').slideUp(400)
            $(this).closest('.main_slide').next().addClass('active')
            $(this).closest('.main_slide').next().find('.main_slide-prev').slideUp(400)
            $(this).closest('.main_slide').next().find('.main_slide-wrapper').slideDown(400)
            $('.main_imgs').slick('slickNext');
        }
        else if ($(this).hasClass('prev') && !$(this).hasClass('disabled')) {
            $(this).closest('.main_slide').removeClass('active')
            $(this).closest('.main_slide').find('.main_slide-prev').slideDown(400)
            $(this).closest('.main_slide').find('.main_slide-wrapper').slideUp(400)
            $(this).closest('.main_slide').prev().addClass('active')
            $(this).closest('.main_slide').prev().find('.main_slide-prev').slideUp(400)
            $(this).closest('.main_slide').prev().find('.main_slide-wrapper').slideDown(400)
            $('.main_imgs').slick('slickPrev');
        }
        $('.main_img').not($('.main_slide.active').index()).removeClass('active')
        $('.main_img').eq($('.main_slide.active').index()).addClass('active')

    })

    $('.form_inp').keyup(function () {
        if ($(this).val().length > 0) {
            $(this).addClass('active')
        }
        else {
            $(this).removeClass('active')
        }
    })
    
        $('.form_area').keyup(function () {
        if ($(this).val().length > 0) {
            $(this).addClass('active')
        }
        else {
            $(this).removeClass('active')
        }
    })

    $('.inp_clean').click(function () {
        $(this).prev().val('')
        $(this).prev().removeClass('active')
    })

$('.gallery_slider').slick({
    dots: true,
    arrows: true,
    infinite: false,
    slidesToShow: 4,
    rows: 2,
    slidesToScroll: 2,
    prevArrow: '<button type="button" class="slick-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.8791 15.4468L7.4285 9.99617L12.8791 4.54558C13.2045 4.22017 13.2045 3.6925 12.8791 3.36708C12.5537 3.04167 12.026 3.04167 11.7006 3.36708L5.66075 9.40692C5.50442 9.56325 5.41667 9.77517 5.41667 9.99617C5.41667 10.2172 5.50442 10.4292 5.66075 10.5854L11.7006 16.6253C11.7413 16.666 11.7851 16.7016 11.8313 16.7321C12.1548 16.9457 12.5943 16.9101 12.8791 16.6253C13.2045 16.2999 13.2045 15.7722 12.8791 15.4468Z" fill="#1B1A18"/></svg></button>',
    nextArrow: '<button type="button" class="slick-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.12091 15.4468L12.5715 9.99617L7.12091 4.54558C6.79547 4.22017 6.79547 3.6925 7.12091 3.36708C7.44635 3.04167 7.97398 3.04167 8.29941 3.36708L14.3392 9.40692C14.4956 9.56325 14.5833 9.77517 14.5833 9.99617C14.5833 10.2172 14.4956 10.4292 14.3392 10.5854L8.29941 16.6253C8.25874 16.666 8.2149 16.7016 8.16869 16.7321C7.84523 16.9457 7.40566 16.9101 7.12091 16.6253C6.79547 16.2999 6.79547 15.7722 7.12091 15.4468Z" fill="#1B1A18"/></svg></button>',
    responsive: [
        {
            breakpoint: 1320,
            settings: {
                slidesToShow: 3,
            }
        },
        {
            breakpoint: 767,
            settings: {
                dots: false,
                slidesToShow: 2,
                slidesToScroll: 1,
                variableWidth: true,
                arrows: true,
            }
        },
        {
            breakpoint: 480,
            settings: {
                dots: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                variableWidth: true,
                arrows: true,
            }
        },
    ]
});


    $('.customers_slider').slick({
        dots: true,
        slidesToShow: 4,
        slidesToScroll: 2,
        prevArrow: '<button type="button" class="slick-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.8791 15.4468L7.4285 9.99617L12.8791 4.54558C13.2045 4.22017 13.2045 3.6925 12.8791 3.36708C12.5537 3.04167 12.026 3.04167 11.7006 3.36708L5.66075 9.40692C5.50442 9.56325 5.41667 9.77517 5.41667 9.99617C5.41667 10.2172 5.50442 10.4292 5.66075 10.5854L11.7006 16.6253C11.7413 16.666 11.7851 16.7016 11.8313 16.7321C12.1548 16.9457 12.5943 16.9101 12.8791 16.6253C13.2045 16.2999 13.2045 15.7722 12.8791 15.4468Z" fill="#1B1A18"/></svg></button>',
        nextArrow: '<button type="button" class="slick-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.12091 15.4468L12.5715 9.99617L7.12091 4.54558C6.79547 4.22017 6.79547 3.6925 7.12091 3.36708C7.44635 3.04167 7.97398 3.04167 8.29941 3.36708L14.3392 9.40692C14.4956 9.56325 14.5833 9.77517 14.5833 9.99617C14.5833 10.2172 14.4956 10.4292 14.3392 10.5854L8.29941 16.6253C8.25874 16.666 8.2149 16.7016 8.16869 16.7321C7.84523 16.9457 7.40566 16.9101 7.12091 16.6253C6.79547 16.2999 6.79547 15.7722 7.12091 15.4468Z" fill="#1B1A18"/></svg></button>',
        responsive: [
            {
                breakpoint: 1320,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 640,
                settings: {
                    slidesToShow: 1,
                }
            },
        ]
    })

    // Закрепление топ-меню, разобрать код и совместить с функцией SHOW
    let headerTop = $('.header_inner').offset().top
    // Основное меню
    $(window).scroll(function () {
        if ($(window).scrollTop() > headerTop) {
            $('.header_inner').addClass('fixed')
        }
        else {
            $('.header_inner').removeClass('fixed')
        }
    })
    // Субменю
    $(window).scroll(function () {
        if ($(window).scrollTop() > headerTop) {
            $('.header_inner_subcontacts').addClass('fixed-2')
        }
        else {
            $('.header_inner_subcontacts').removeClass('fixed-2')
        }
    })
    // Исчезание/проявление при прокрутке страницы
    $(document).ready(function() {
        let lastScrollTop = 0;
        const headerTop = $('.header_inner').offset().top;
        const subcontactsTop = $('.header_inner_subcontacts').offset().top;
        const headerTopMobile = $('.header').offset().top; // Положение элемента header  
        let menuVisible = false;
        let subcontactsVisible = false;
        let headerVisible = false; // Параметр для отслеживания видимости header
    
        $(window).scroll(function () {
            let scrollTop = $(this).scrollTop();
    
            // Обработка основного меню  
            if (scrollTop > headerTop) {
                if (scrollTop > lastScrollTop && menuVisible) {
                    $('.header_inner').removeClass('show');
                    menuVisible = false;
                } else if (scrollTop < lastScrollTop && !menuVisible) {
                    $('.header_inner').addClass('show');
                    menuVisible = true;
                }
                $('.header_inner').addClass('fixed');
            } else {
                $('.header_inner').removeClass('fixed');
                if (menuVisible) {
                    $('.header_inner').removeClass('show');
                    menuVisible = false;
                }
            }
    
            // Обработка подменю  
            if (scrollTop > subcontactsTop) {
                if (scrollTop > lastScrollTop && subcontactsVisible) {
                    $('.header_inner_subcontacts').removeClass('show');
                    subcontactsVisible = false;
                } else if (scrollTop < lastScrollTop && !subcontactsVisible) {
                    $('.header_inner_subcontacts').addClass('show');
                    subcontactsVisible = true;
                }
                $('.header_inner_subcontacts').addClass('fixed');
            } else {
                $('.header_inner_subcontacts').removeClass('fixed');
                if (subcontactsVisible) {
                    $('.header_inner_subcontacts').removeClass('show');
                    subcontactsVisible = false;
                }
            }
    
            // Обработка header для мобильных устройств  
            if ($(window).width() < 980) {
                if (scrollTop > headerTopMobile) {
                    if (scrollTop > lastScrollTop && headerVisible) {
                        $('.header').removeClass('show');
                        headerVisible = false;
                    } else if (scrollTop < lastScrollTop && !headerVisible) {
                        $('.header').addClass('show');
                        headerVisible = true;
                    }
                    $('.header').addClass('fixed');
                } else {
                    $('.header').removeClass('fixed');
                    if (headerVisible) {
                        $('.header').removeClass('show');
                        headerVisible = false;
                    }
                }
            }
    
            lastScrollTop = scrollTop;
        });
    })

    //Менюбургер в моб. версии
    $('.menu_burger').click(function () {
        $('.menu_burger, .header_inner').toggleClass('active')
    })

    $('.form_area').keyup(function () {
        $(this).next().find('.form_inp-count span').text($(this).val().length)
    })


    $('.menu  li').click(function (e) {
        if ($(this).is(e.target)) {
            $(this).toggleClass('active')
            $(this).find('.dropdown').eq(0).slideToggle(400)
        }

    })
    
    // Удаляем активность у всех пунктов меню при перезагрузке
    $(document).ready(function() {
      $('.menu li').removeClass('active');
    });

    $('.scheme_slider').slick({
        slidesToShow: 1,
        prevArrow: '<button type="button" class="slick-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.8791 15.4468L7.4285 9.99617L12.8791 4.54558C13.2045 4.22017 13.2045 3.6925 12.8791 3.36708C12.5537 3.04167 12.026 3.04167 11.7006 3.36708L5.66075 9.40692C5.50442 9.56325 5.41667 9.77517 5.41667 9.99617C5.41667 10.2172 5.50442 10.4292 5.66075 10.5854L11.7006 16.6253C11.7413 16.666 11.7851 16.7016 11.8313 16.7321C12.1548 16.9457 12.5943 16.9101 12.8791 16.6253C13.2045 16.2999 13.2045 15.7722 12.8791 15.4468Z" fill="#1B1A18"/></svg></button>',
        nextArrow: '<button type="button" class="slick-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.12091 15.4468L12.5715 9.99617L7.12091 4.54558C6.79547 4.22017 6.79547 3.6925 7.12091 3.36708C7.44635 3.04167 7.97398 3.04167 8.29941 3.36708L14.3392 9.40692C14.4956 9.56325 14.5833 9.77517 14.5833 9.99617C14.5833 10.2172 14.4956 10.4292 14.3392 10.5854L8.29941 16.6253C8.25874 16.666 8.2149 16.7016 8.16869 16.7321C7.84523 16.9457 7.40566 16.9101 7.12091 16.6253C6.79547 16.2999 6.79547 15.7722 7.12091 15.4468Z" fill="#1B1A18"/></svg></button>',
    })

    })

    //Переключение табов
    $(document).on('click', '.ourProduction__block .tabs__block .tab', function(){
        $('.ourProduction__block .tabs__block .tab').removeClass('active');
        $('.ourProduction__block .tab__content').removeClass('active');

        $(this).addClass('active');
        $( '.ourProduction__block ' + $(this).attr('data-tab') ).addClass('active');
    })

    //Анимация цифр
    if( $(".numbers").length ){
        var flag = true;
        $(document).scroll(function () {
            var s_top = $(window).scrollTop()+900;
            var yes = $(".numbers").offset().top;
            if(s_top > yes){
                if( flag ){
                    flag = false;
                    $('.numbers .numbers_items .numbers_item .numbers_item-title span').each(function () {
                        $(this).prop('Counter',0).animate({
                            Counter: $(this).text().replace(/\s+/g, '')
                        }, {
                            duration: 2500,
                            easing: 'swing',
                            step: function (now) {
                                $(this).text( Math.ceil(now) );
                                //$('.counter__block .counter__list .item:nth-child(3) .number span').text( number_format($('.counter__block .counter__list .item:nth-child(3) .number span').text(), 0, ',', ' ') );
                                //$('.counter__block .counter__list .item:nth-child(4) .number span').text( number_format($('.counter__block .counter__list .item:nth-child(4) .number span').text(), 0, ',', ' ') );
                            }
                        });
                    });
                }
            }
        });
    }
