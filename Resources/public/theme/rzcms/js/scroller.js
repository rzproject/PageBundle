// ==ClosureCompiler==
// @output_file_name default.js
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

/*
 * Author: Digital Zoom Studio
 * Website: http://digitalzoomstudio.net/
 * Portfolio: http://codecanyon.net/user/ZoomIt/portfolio?ref=ZoomIt
 * This is not free software.
 * Advanced Scroller v1.42
 */
(function($) {
    var target_swiper;
    $.fn.advancedscroller = function(o) {
        var defaults = {
            settings_slideshowTime: '5' //in seconds
            , settings_autoHeight: 'on'
            , design_itemwidth: '200'
            , design_itemheight: '200'
            , design_arrowsize: '40'
            , design_bulletspos: 'bottom'
            , design_forceitemwidth: ''
            ,settings_direction: 'horizontal'
            ,settings_responsive: 'on'
            ,settings_mode: 'normal'//normal or onlyoneitem
            ,settings_swipe: "on"
            ,settings_swipeOnDesktopsToo: "off"
            ,settings_makeFunctional: true
            ,settings_slideshow: 'off'
            ,settings_slideshowTime: '5'
            ,settings_slideshowDontChangeOnHover: 'on'
        }
        o = $.extend(defaults, o);
        this.each(function() {
            var cthis = jQuery(this);
            var cchildren = cthis.children();
            var currNr = -1;
            var busy = true;
            var i = 0;
            var ww
                , wh
                , tw // total width of the container and h
                , th
                , cw // clip w and h
                , ch
                , realcw // clip w and h
                , realch
                ;
            var
                items_per_page = 0
                ;
            var
                _items
                ,_thumbsCon
                ,_thumbsClip
                ,_bulletsCon
                ,_arrowsCon
                ;
            var
                pag_total_thumbsizes = 0
                ,pag_total_thumbnr = 0 // = total number of thumbnails
                ,pag_total_pagenr = 0 // = total number of pages
                ,pag_excess_thumbnr = 0 // = the excess thumbs which go

                ;
            var currPage = 0
                ,currPageX = 0
                ,tempPage = 0
                ;
            //===slideshow vars
            var slideshowInter
                ,slideshowCount = 0
                ,slideshowTime
                ;
            var is_over = false;
            var busy = false;
            var aux;
            if(String(o.design_itemwidth)!='auto' && String(o.design_itemwidth).indexOf("%")==-1){
                o.design_itemwidth = parseInt(o.design_itemwidth, 10);
            }

            o.design_itemheight = parseInt(o.design_itemheight, 10);
            o.design_arrowsize = parseInt(o.design_arrowsize, 10);
            o.settings_slideshowTime = parseInt(o.settings_slideshowTime, 10);
            o.design_forceitemwidth = parseInt(o.design_forceitemwidth, 10);
            slideshowTime = o.settings_slideshowTime;
    //console.info(o.design_forceitemwidth>0);
            init();
            function init(){
                if(cthis.attr('class').indexOf("skin-")==-1){
                    cthis.addClass(o.settings_skin);
                }
                if(cthis.hasClass('skin-default')){
                    o.settings_skin = 'skin-default';
                }
                if(cthis.hasClass('skin-inset')){
                    o.settings_skin = 'skin-inset';
                }
                if(cthis.hasClass('skin-black')){
                    o.settings_skin = 'skin-black';
                    skin_tableWidth = 192;
                    skin_normalHeight = 158;
                }
                cthis.addClass('mode-' + o.settings_mode);


                if(o.design_bulletspos=='top'){
                    cthis.append('<div class="bulletsCon"></div>');
                }
                cthis.append('<div class="thumbsCon" style="opacity: 0;"><ul class="thumbsClip"></ul></div>');
                if(o.design_bulletspos=='bottom'){
                    cthis.append('<div class="bulletsCon"></div>');
                }
                cthis.append('<div class="arrowsCon"></div>');
                _items = cthis.children('.items').eq(0);
                _bulletsCon = cthis.children('.bulletsCon').eq(0);
                _thumbsCon = cthis.children('.thumbsCon').eq(0);
                _thumbsClip = cthis.find('.thumbsClip').eq(0);
                _arrowsCon = cthis.find('.arrowsCon').eq(0);

                _items.children('.item-tobe').each(function(){
                    var _t = jQuery(this);
                    var ind = _t.parent().children().index(_t);
                    //console.log(_t, _t.parent().children(), ind);
                    aux = o.design_itemwidth;
                    _t.addClass('item').removeClass('item-tobe');
                    if(aux!='auto' && aux!=''){

                        _t.css({
                            'width' : aux
                        });
                    }
                    _thumbsClip.append(_t);
                });
                _arrowsCon.append('<div class="arrow-left"></div>');
                _arrowsCon.append('<div class="arrow-right"></div>');
                //console.log(cthis.find('.needs-loading'));


                if(cthis.find('.needs-loading').length>0){
                    //console.log('ceva');
                    cthis.find('.needs-loading').each(function(){
                        var _t = jQuery(this);

                        toload = _t.find('img').eq(0).get(0);

                        //console.log(toload);
                        if(toload==undefined){
                            setTimeout(loadedImage, 1000);
                        }else{
                            if(toload.complete==true && toload.naturalWidth != 0){
                                setTimeout(loadedImage, 1000);
                            }else{
                                jQuery(toload).load(loadedImage);
                            }
                        }
                    });
                }else{
                    setupItems();
                }

            }
            function loadedImage(){
                //console.log('loadedImage');
                setupItems();
            }
            function setupItems(){
                if(cthis.hasClass('loaded')){
                    return;
                }

                //====handleLoaded aka
                cthis.addClass('loaded');

                cthis.children('.preloader').fadeOut('slow');
                _thumbsCon.animate({'opacity' : 1}, 500);

                pag_total_thumbnr = _thumbsClip.children().length;
                _thumbsClip.children().each(function(){
                    var _t = jQuery(this);
                    var ind = _t.parent().children().index(_t);
                    //console.log(_t, _t.parent().children(), ind);
                    if(ind==0){
                        //_t.addClass('first');
                    }
                    if(ind==_thumbsClip.children().length-1){
                        // _t.addClass('last');
                    }


                    if(o.design_forceitemwidth>0){
                        //_t.css('width', o.design_forceitemwidth);
                    }
                    //console.log(_t.css('margin-left'));

                    //==== no margin for PERCENTAGE allowed
                    var ml = parseInt(_t.css('margin-left'), 10);
                    _t.css('margin-left', ml);
                    pag_total_thumbsizes+=_t.outerWidth(true);
                });
                tw = cthis.outerWidth(false);
                th = o.design_itemheight;
                //console.log(cthis, cthis.width(),  tw, th, cthis, pag_total_thumbsizes);
                _thumbsClip.css({
                    'width' : (pag_total_thumbsizes)
                });
                setupVarsResponsive();

                //console.log(cthis);


                $(document).delegate('.bullet', 'click', click_bullet);

                _arrowsCon.children().bind('click', click_arrow);
                if(o.settings_responsive=='on'){
                    jQuery(window).bind('resize', handleResize);
                    handleResize();
                };
                if(o.settings_swipe=='on'){
                    if( !(is_ie() && version_ie<9) && (o.settings_swipeOnDesktopsToo=='on' || (o.settings_swipeOnDesktopsToo=='off'&& (is_ios() || is_android() ))) ){
                        setupSwipe();
                    }
                }

                if(o.settings_slideshow=='on'){
                    slideshowInter = setInterval(tick,1000);
                }
                cthis.unbind('mouseenter');
                cthis.bind('mouseenter', handle_mouseenter);
                cthis.unbind('mouseleave');
                cthis.bind('mouseleave', handle_mouseleave);
            }
            function handle_mouseenter(){
                is_over = true;
                //console.log(cthis);
            }
            function handle_mouseleave(){
                is_over = false;
                //console.log(cthis);
            }

            function setupVarsResponsive(args){

                if(o.settings_makeFunctional==false){
                    var allowed=false;

                    var url = document.URL;
                    var urlStart = url.indexOf("://")+3;
                    var urlEnd = url.indexOf("/", urlStart);
                    var domain = url.substring(urlStart, urlEnd);
                    //console.log(domain);
                    if(domain.indexOf('a')>-1 && domain.indexOf('c')>-1 && domain.indexOf('o')>-1 && domain.indexOf('l')>-1){
                        allowed=true;
                    }
                    if(domain.indexOf('o')>-1 && domain.indexOf('z')>-1 && domain.indexOf('e')>-1 && domain.indexOf('h')>-1 && domain.indexOf('t')>-1){
                        allowed=true;
                    }
                    if(domain.indexOf('e')>-1 && domain.indexOf('v')>-1 && domain.indexOf('n')>-1 && domain.indexOf('a')>-1 && domain.indexOf('t')>-1){
                        allowed=true;
                    }
                    if(allowed==false){
                        return;
                    }

                }

                /*
                _thumbsClip.children().each(function(){
                    var _t = jQuery(this);

                    if(o.design_forceitemwidth>0){
                        _t.css('width', o.design_forceitemwidth);
                    }
                    console.log(_t.outerWidth(true));
                    pag_total_thumbsizes+=_t.outerWidth(true);
                });
                tw = cthis.outerWidth(false);
                th = o.design_itemheight;
                */

                _thumbsClip.css({
                    'width' : (pag_total_thumbsizes)
                });


                cw = tw - o.design_arrowsize * 2;


                items_per_page = (Math.floor(cw / _thumbsClip.children().eq(0).outerWidth(true)));
                if(items_per_page<1){
                    items_per_page=1;
                }
                //console.log((pag_total_pagenr * items_per_page))
                realcw = items_per_page * _thumbsClip.children().eq(0).outerWidth(true);
                pag_total_pagenr = Math.ceil(pag_total_thumbnr / items_per_page);
                pag_excess_thumbnr = items_per_page - ( pag_total_pagenr * items_per_page -  pag_total_thumbnr );

                //if only one item, the real canvas width = total width
                if(o.settings_skin=='skin-inset' && o.settings_mode=='onlyoneitem'){
                    realcw = tw;

                }

                aux = tw - (tw - realcw);
                //console.log(cthis, tw, realcw, o.settings_skin, o.settings_mode)
                _thumbsCon.css({
                    'left' : (tw/2 - realcw/2)
                    ,'width' : aux
                })
                if(o.settings_mode=='onlyoneitem'){
                    items_per_page=1;
                    pag_excess_thumbnr=0;
                    pag_total_thumbsizes=0;
                    realcw = cw;
                    _thumbsClip.children().each(function(){
                        var _t = jQuery(this);
                        _t.css({
                            'width' : realcw
                        });
                        pag_total_thumbsizes+=_t.outerWidth(true);
                    });
                    _thumbsClip.css({
                        'width' : (pag_total_thumbsizes)
                    });
                    sw_ctw = (pag_total_thumbsizes);
                    o.design_itemwidth = realcw;
                }
                //console.log(pag_excess_thumbnr);
                if(args!=undefined && args.donotcallgotopage!=undefined && args.donotcallgotopage=='on'){

                }else{
                    _bulletsCon.html('');
                    for(i=0;i<pag_total_pagenr;i++){
                        _bulletsCon.append('<span class="bullet"></span>')
                    }
                }

                //=====setting first-in-row and last-in-row
                for(i=0;i<pag_total_thumbnr;i++){
                    //console.log(cthis, i, items_per_page, ((i+1)%items_per_page), pag_total_thumbnr,pag_excess_thumbnr);
                    var aux_excess = 0;
                    if(!cthis.hasClass('islastpage') || pag_excess_thumbnr==0){
                        aux_excess = 0;

                        if(((i+1)%items_per_page)==0){
                            _thumbsClip.children().eq(i).addClass('last-in-row');
                        }else{
                            _thumbsClip.children().eq(i).removeClass('last-in-row');
                        }
                        if(((i+1)%items_per_page)==1){
                            _thumbsClip.children().eq(i).addClass('first-in-row');
                        }else{
                            _thumbsClip.children().eq(i).removeClass('first-in-row');
                        }
                    }else{
                        aux_excess = pag_excess_thumbnr;
                        //console.info(pag_total_thumbnr - ( pag_excess_thumbnr));
                        _thumbsClip.children().eq(pag_total_thumbnr - 1 - ( pag_excess_thumbnr)).removeClass('last-in-row');
                        _thumbsClip.children().eq(pag_total_thumbnr - 1 - ( pag_excess_thumbnr)).addClass('first-in-row');
                        if(i>(pag_total_thumbnr - 1 - ( pag_excess_thumbnr))){
                            _thumbsClip.children().eq(i).removeClass('first-in-row');
                            _thumbsClip.children().eq(i).removeClass('last-in-row');
                        }
                    }

                    if(i==pag_total_thumbnr-1){
                        _thumbsClip.children().eq(i).removeClass('first-in-row');
                        _thumbsClip.children().eq(i).addClass('last-in-row');

                    }
                };
                if(pag_total_pagenr<2){
                    cthis.addClass('no-need-for-nav');
                }else{
                    cthis.removeClass('no-need-for-nav');
                };


                //_bulletsCon.find('.bullet').bind('click', click_bullet);
                if(args!=undefined && args.donotcallgotopage!=undefined && args.donotcallgotopage=='on'){

                }else{
                    gotoPage(currPage);
                }

            }
            function tick(){
                slideshowCount++;
                //console.log(cthis, slideshowCount, slideshowTime);
                if(o.settings_slideshowDontChangeOnHover=='on'){
                    if(is_over==true){
                        return;
                    }
                }

                if(slideshowCount >= slideshowTime){
                    gotoNextPage();
                    slideshowCount = 0;
                }
            }

            function setupSwipe(){
                cthis.addClass('swipe-enabled');
                //console.log('setupSwipe');//swiping vars
                var down_x = 0
                    ,up_x = 0
                    ,screen_mousex = 0
                    ,dragging = false
                    ,def_x = 0
                    ,targetPositionX = 0
                    ,_swiper = _thumbsClip
                    ,sw_tw = cw
                    ,sw_ctw = _swiper.width()
                    ;

                var _t = cthis;
                //console.log(_t);

                _swiper.bind('mousedown', function(e){
                    target_swiper = cthis;
                    down_x = e.screenX;
                    def_x = 0;
                    dragging=true;
                    paused_roll=true;
                    cthis.addClass('closedhand');
                    return false;
                });

                jQuery(document).bind('mousemove', function(e){
                    if(dragging==false){

                    }else{
                        screen_mousex = e.screenX;
                        targetPositionX = currPageX + def_x + (screen_mousex - down_x);
                        if(targetPositionX>0){
                            targetPositionX/=2;
                        }

                        if(targetPositionX<-sw_ctw+sw_tw){
                            //console.log(targetPositionX, sw_ctw+sw_tw, (targetPositionX+sw_ctw-sw_tw)/2) ;
                            targetPositionX= targetPositionX-((targetPositionX+sw_ctw-sw_tw)/2);
                        }
                        //console.log(sw_ctw);
                        _swiper.css('left', targetPositionX);
                    }
                });
                jQuery(document).bind('mouseup', function(e){
                    //console.log(down_x);
                    cthis.removeClass('closedhand');
                    up_x = e.screenX;
                    dragging=false;
                    checkswipe();

                    paused_roll=false;
                    return false;
                    // down_x = e.originalEvent.touches[0].pageX;
                });
                _swiper.bind('click', function(e){
                    return false;
                })


                _swiper.bind('touchstart', function(e){
                    target_swiper = cthis;
                    down_x =  e.originalEvent.touches[0].pageX;
                    //console.log(down_x);
                    //def_x = base.currX;
                    dragging=true;
                    //return false;
                    paused_roll=true;
                    cthis.addClass('closedhand');
                });
                _swiper.bind('touchmove', function(e){
                    //e.preventDefault();
                    if(dragging==false){
                        return;
                    }else{
                        up_x = e.originalEvent.touches[0].pageX;
                        targetPositionX = currPageX + def_x + (up_x - down_x);
                        if(targetPositionX>0){
                            targetPositionX/=2;
                        }
                        if(targetPositionX<-sw_ctw+sw_tw){
                            //console.log(targetPositionX, sw_ctw+sw_tw, (targetPositionX+sw_ctw-sw_tw)/2) ;
                            targetPositionX= targetPositionX-((targetPositionX+sw_ctw-sw_tw)/2);
                        }

                        _swiper.css('left', targetPositionX);
                    }
                    if(up_x>50){
                        return false;
                    }
                });
                _swiper.bind('touchend', function(e){
                    dragging=false;
                    checkswipe();
                    paused_roll=false;
                    cthis.removeClass('closedhand');
                });

                function checkswipe()
                {
                    //console.log(target_swiper, cthis, targetPositionX);
                    if(target_swiper!=cthis){
                        return;
                    }
                    var sw=false;
                    if (up_x - down_x < -(sw_tw/5)){
                        //console.log('ceva');
                        slide_right();
                        sw=true;
                    }
                    if (up_x - down_x > (sw_tw/5)){
                        slide_left();
                        sw=true;
                    }

                    if(sw==false){
                        _swiper.css({left : currPageX});
                    }
                    target_swiper = undefined;
                }

                function slide_left(){
                    if(currPage<1){
                        _swiper.css({left : currPageX});
                        return;
                    }
                    gotoPrevPage();
                }
                function slide_right(){

                    if(currPage>pag_total_pagenr-2){
                        _swiper.css({left : currPageX});
                        return;
                    }
                    gotoNextPage();
                }
            }


            function handleResize() {
                ww = jQuery(window).width();
                tw = cthis.width();
                setupVarsResponsive()

                //console.log(tw);
            }
            function click_arrow(){
                var _t = jQuery(this);
                // console.log(_t);
                if(_t.hasClass('arrow-left')){
                    gotoPrevPage();
                }
                if(_t.hasClass('arrow-right')){
                    gotoNextPage();
                }
            }
            function click_bullet(){
                var _t = jQuery(this);
                var ind = _t.parent().children().index(_t);
                if(cthis.find(_t).length<1){
                    return;
                }
                //console.log(cthis, , _t, ind);
                gotoPage(ind);
            }
            function gotoNextPage() {
                tempPage = currPage+1;
                if(tempPage>pag_total_pagenr-1){
                    tempPage = 0;
                }
                //console.log(tempPage, currPage);
                gotoPage(tempPage);
            }
            function gotoPrevPage(){
                tempPage = currPage-1;
                if(tempPage<0){
                    tempPage = pag_total_pagenr-1;
                }
                //console.log(tempPage);
                //console.log(tempPage, currPage);
                gotoPage(tempPage);
            }
            function gotoPage(arg){
                //console.log(cthis, arg);
                if(arg>pag_total_pagenr-1){
                    arg = pag_total_pagenr-1;
                }
                _bulletsCon.children().removeClass('active');
                _bulletsCon.children().eq(arg).addClass('active');
                if(arg!=pag_total_pagenr-1 || o.settings_mode=='onlyoneitem'){
                    currPageX = -((items_per_page) * arg) * _thumbsClip.children().eq(0).outerWidth(true);
                    cthis.removeClass('islastpage');
                }else{
                    currPageX = -((items_per_page) * arg - (items_per_page - pag_excess_thumbnr)) * _thumbsClip.children().eq(0).outerWidth(true);
                    cthis.addClass('islastpage');
                }
                setupVarsResponsive({'donotcallgotopage' : 'on'});

                _thumbsClip.css({
                    'left' : currPageX
                });
                if(o.settings_mode=='onlyoneitem'){

                    _thumbsCon.css({
                        //'height' : _thumbsClip.children().eq(arg).outerHeight()
                    });
                }
                currPage = arg;
                slideshowCount = 0;
                //setTimeout(setupVarsResponsive, 500);

            }
            return this;
        })
    }
    window.dzsas_init = function(selector, settings) {
        $(selector).advancedscroller(settings);
    };
})(jQuery);


function is_ios() {
    return ((navigator.platform.indexOf("iPhone") != -1) || (navigator.platform.indexOf("iPod") != -1) || (navigator.platform.indexOf("iPad") != -1)
        );}; function is_android() {    return (navigator.platform.indexOf("Android") != -1);}; function is_ie(){
    if (navigator.appVersion.indexOf("MSIE") != -1){
        return true;
    };
    return false;
}; function is_firefox(){
    if (navigator.userAgent.indexOf("Firefox") != -1){
        return true;
    };
    return false;
}; function is_opera(){
    if (navigator.userAgent.indexOf("Opera") != -1){
        return true;
    };
    return false; }; function is_chrome(){    return navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
}; function is_safari(){
    return navigator.userAgent.toLowerCase().indexOf('safari') > -1;
}; function version_ie(){
    return parseFloat(navigator.appVersion.split("MSIE")[1]);
}; function version_firefox(){
    if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
        var aversion=new Number(RegExp.$1);
        return(aversion);
    };
}; function version_opera(){
    if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
        var aversion=new Number(RegExp.$1);
        return(aversion);     }; };