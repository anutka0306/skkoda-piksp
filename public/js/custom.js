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
    $('.brands__list_slider').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1
    });
});