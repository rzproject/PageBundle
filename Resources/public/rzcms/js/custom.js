jQuery(document).ready(function () {
    $('.switch-button').click(function () {
        if (jQuery(this).is('.open')) {
            jQuery(this).addClass('closed');
            jQuery(this).removeClass('open');
            jQuery('.rzcms-admin-cmsmenu').animate({
                'left': '-222px'
            });
        } else {
            jQuery(this).addClass('open');
            jQuery(this).removeClass('closed');
            jQuery('.rzcms-admin-cmsmenu').animate({
                'left': '0px'
            });
        }
    });

    jQuery('.switch-button').addClass('closed');
    jQuery('.rzcms-admin-cmsmenu').animate({
        'left': '-222px'
    });

    jQuery('.carousel').carousel({
        interval: false
    });

});
