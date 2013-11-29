/*
 * Author: Digital Zoom Studio
 * Website: http://digitalzoomstudio.net/
 * Portfolio: http://codecanyon.net/user/ZoomIt/portfolio
 *
 * Version: 2.00
 */

(function($) {
    $.fn.prependOnce = function(arg, argfind) {
        var _t = $(this[0]) // It's your element
        if(_t.children(argfind).length<1){
            _t.prepend(arg);
        }
    };
    $.fn.appendOnce = function(arg, argfind) {
        var _t = $(this[0]) // It's your element
        if(_t.children(argfind).length<1){
            _t.append(arg);
        }
    };
    $.fn.dzsportfolio = function(o) {
        var defaults = {
            settings_slideshowTime: '5' //in seconds
            , settings_autoHeight: 'on'
            , settings_skin: 'skin-default'
            , settings_mode: 'masonry'
            , settings_disableCats: 'off'
            , settings_clickaction: 'none'
            , title: ''
            , design_item_width: '0'
            , design_item_height: '0'
            , design_item_height_same_as_width: 'off'
            , design_thumbw: '100%'
            , design_thumbh: '180'
            , design_categories_pos: 'top' // top or bottom
            , settings_ajax_enabled: 'off'
            , settings_ajax_loadmoremethod: 'scroll'// ==choose between scroll and button mode
            , settings_ajax_pages: []
            , settings_lightboxlibrary: 'prettyphoto'
            , settings_preloadall: 'off'
            , disable_itemmeta: "off"
            ,wall_settings: {}
            ,audioplayer_swflocation: 'ap.swf'
            ,videoplayer_swflocation: 'preview.swf'
            ,settings_makeFunctional: true

        }
        o = $.extend(defaults, o);
        this.each(function() {
            var cthis = $(this);
            var cclass = '';
            var cchildren = cthis.children();
            var nr_children = cthis.children('.items').children().length
                ,nr_loaded = 0
                ;
            var currNr = -1;
            var busy = true;
            var i = 0;
            var ww
                , wh
                , tw
                , th
                ;
            var _pageCont
                , _theitems
                , _selectorCon
                ;
            var arr_cats = [];
            var busy = false
                ,busy_ajax = false
                ;
            var sw = false;
            var the_skin = 'skin-default';
            var isotope_settings = {};
            var inter_reset;
            var ind_ajaxPage = 0;

            //===thumbsize
            var st_tw = 0
                , st_th = 0
                , thumbh_dependent_on_w = false
                ;

            var is_already_inited="off";

            //console.info(nr_children);

            //o.item_width = parseInt(o.item_width, 10);

            if ((o.design_item_width).indexOf('%') == -1) {
                o.design_item_width = parseInt(o.design_item_width, 10);
            }
            if ((o.design_item_height).indexOf('%') == -1) {
                o.design_item_height = parseInt(o.design_item_height, 10);
            }
            if ((o.design_thumbw).indexOf('%') == -1) {
                o.design_thumbw = parseInt(o.design_thumbw, 10);
            }


            //console.info(o.design_thumbh);
            if(o.design_item_height_same_as_width == 'on'){
                o.design_thumbh = '1/1';
            }

            if(String(o.design_thumbh).indexOf('/')>-1){
                thumbh_dependent_on_w = true;
                o.design_thumbh = eval(o.design_thumbh);
            }else{
                if ((o.design_thumbh).indexOf('%') == -1) {
                    o.design_thumbh = parseInt(o.design_thumbh, 10);

                }
            }
            //console.info(o.design_thumbh);
            //var aux = eval("3/4"); console.log(aux);


            init();
            function init() {


                if (typeof(cthis.attr('class')) == 'string') {
                    cclass = cthis.attr('class');
                } else {
                    cclass = cthis.get(0).className;
                }
                if (cclass.indexOf("skin-") == -1) {
                    cthis.addClass(o.settings_skin);
                }
                if (cclass.indexOf("-sortable") == -1) {
                    cthis.addClass('is-sortable');
                }


                if (cthis.hasClass('skin-default')) {
                    o.settings_skin = 'skin-default';
                }


                if (cthis.hasClass('skin-black')) {
                    o.settings_skin = 'skin-black';
                    skin_tableWidth = 192;
                    skin_normalHeight = 158;
                }
                if (cthis.hasClass('skin-blog')) {
                    o.settings_skin = 'skin-blog';
                }
                if (cthis.hasClass('skin-accordion')) {
                    o.settings_skin = 'skin-accordion';
                }
                if (cthis.hasClass('skin-boxed')) {
                    o.settings_skin = 'skin-boxed';
                }
                cthis.addClass('mode-' + o.settings_mode);

                //console.log(cthis);
                //console.log(jQuery.appendOnce('ceva'));

                init_ready();
            }
            function init_ready(){
                //====can be called on reinit



                if(cthis.children('.selector-con').length>0){
                    is_already_inited='on';
                }
                if (o.design_categories_pos == 'top') {
                    cthis.prependOnce('<div class="selector-con"><div class="clear"></div></div>', '.selector-con');
                    cthis.appendOnce('<div class="pageContent"></div>', '.pageContent');

                } else {
                    cthis.appendOnce('<div class="selector-con"><div class="clear"></div></div>', '.selector-con');
                    cthis.prependOnce('<div class="pageContent"></div>', '.pageContent');

                }

                _selectorCon = cthis.children('.selector-con');
                _theitems = cthis.children('.items');
                _pageCont = cthis.children('.pageContent');


                if (o.title != '') {
                    _selectorCon.prepend('<div class="portfolio-title">' + o.title + '</div>')
                }



                cthis.find('.portitem-tobe').each(function($) {
                    var _t = jQuery(this);
                    //console.log(_t.css('width'));
                    var _t_w = 0;
                    var _t_h = 0;

                    //====items sizes
                    if (o.design_item_width != 0) {
                        _t_w = o.design_item_width;
                    }
                    if (o.design_item_height != 0) {
                        _t_h = o.design_item_height;
                    }

                    if (_t.attr('data-forcewidth') != undefined) {
                        _t_w = _t.attr('data-forcewidth');
                    }
                    if (_t.attr('data-forceheight') != undefined) {
                        _t.css('height', _t.attr('data-forceheight'));
                    }
                    if(_t_w!=0){
                        _t.css('width', _t_w);
                    }

                    if(_t_h!=0){
                        _t.css('height', _t_h);
                    }


                    var type_featuredarea = 'thumb';

                    if(_t.attr('data-typefeaturedarea')=='gallery'){
                        type_featuredarea = 'gallery';
                    }
                    if(_t.attr('data-typefeaturedarea')=='audio'){
                        type_featuredarea = 'audio';
                    }
                    if(_t.attr('data-typefeaturedarea')=='video'){
                        type_featuredarea = 'video';
                    }
                    if(_t.attr('data-typefeaturedarea')=='youtube'){
                        type_featuredarea = 'video';
                        _t.attr('data-videotype', 'youtube');
                    }
                    if(_t.attr('data-typefeaturedarea')=='vimeo'){
                        type_featuredarea = 'video';
                        _t.attr('data-videotype', 'vimeo');
                    }
                    if(_t.attr('data-typefeaturedarea')=='testimonial'){
                        type_featuredarea = 'testimonial';
                    }
                    if(_t.attr('data-typefeaturedarea')=='link'){
                        type_featuredarea = 'thumb';
                        _t.attr('donotopenimageinlightbox', 'on');
                        _t.attr('data-bigimage', _t.attr('data-link'))
                    }
                    _t.addClass('type-' + type_featuredarea);


                    //====items thumbs sizes
                    st_tw = o.design_thumbw;
                    if (String(o.design_thumbw).indexOf('%') == '-1') {
                        st_tw = o.design_thumbw + 'px';
                    }
                    st_th = o.design_thumbh;
                    console.log(o, String(o.design_thumbh));
                    if (String(o.design_thumbh).indexOf('%') == '-1') {
                        st_th = o.design_thumbh + 'px';
                    }
                    if (_t.attr('data-forcethumbwidth') != undefined) {
                        st_tw = _t.attr('data-forcethumbwidth');
                        if(_t.attr('data-forcethumbwidth').indexOf('%') == '-1' && _t.attr('data-forcethumbwidth').indexOf('px') == '-1'){
                            st_tw = _t.attr('data-forcethumbwidth') + 'px';
                        }
                    }
                    if (_t.attr('data-forcethumbheight') != undefined) {
                        st_th = _t.attr('data-forcethumbheight');
                        if(_t.attr('data-forcethumbheight').indexOf('%') == '-1' && _t.attr('data-forcethumbheight').indexOf('px') == '-1'){
                            st_th = _t.attr('data-forcethumbheight') + 'px';
                        }
                    }

                    if(type_featuredarea=='gallery' || type_featuredarea=='audio' || type_featuredarea=='testimonial'){
                        st_th = 'auto';
                    }

                    var str_tw = 'width: ' + st_tw + ';';
                    var str_th = 'height: ' + st_th + ';';
                    //console.log(str_tw, st_tw, str_th, st_th);
                    if(st_tw=="NaNpx" || st_th == 0 || st_th==''){
                        str_tw = '';
                    }
                    if(st_th=="NaNpx" || st_th == 0 || st_th==''){
                        str_th = '';
                    }
                    //console.log(st_th, _t.attr('data-forcethumbheight'));

                    _t.prepend('<div class="the-feature-con" style="' + str_tw + str_th + '"><div class="the-overlay"></div></div>');




                    if(type_featuredarea=='gallery'){
                        var aux = '<div class="the-feature advancedscroller skin-inset type-'+type_featuredarea+'" style=""><ul class="items">';
                        var len = _t.find('.the-feature-data').eq(0).children().length;
                        for(i=0; i<len;i++){
                            aux+='<li class="item-tobe needs-loading"></li>';
                        }
                        aux+='</ul></div>';
                        _t.find('.the-feature-con').eq(0).prepend(aux);
                        for(i=0; i<len;i++){
                            var _c2 = _t.find('.the-feature-data').eq(0).children().eq(0);
                            //console.log(_c2, _t.find('.the-feature').eq(0).find('.items').eq(0));
                            _t.find('.the-feature').eq(0).find('.items').eq(0).children().eq(i).append(_c2);
                        }
                        _t.find('.the-overlay').hide();

                        if (jQuery.fn.advancedscroller != undefined) {
                            _t.find('.the-feature').eq(0).advancedscroller({
                                settings_mode: "onlyoneitem"
                                ,design_arrowsize: "0"
                                ,settings_swipe: "on"
                                ,settings_swipeOnDesktopsToo: "on"
                                ,settings_slideshow: "on"
                                ,settings_slideshowTime: "8"
                                ,design_bulletspos: "none"
                            });
                        } else {
                            if (window.console) { console.info('dzsportfolio.js - warning: advancedscroller not included'); }
                        }
                    }
                    if(type_featuredarea=='audio'){
                        var aux = '<div class="the-feature audioplayer-tobe skin-default type-'+type_featuredarea+'" style=""  data-source="'+_t.attr('data-thumbnail')+'">';
                        aux+='</div>';
                        _t.find('.the-feature-con').eq(0).prepend(aux);
//console.log(typeof(dzsap_init))
                        _t.find('.the-overlay').hide();
                        if (typeof(dzsap_init) == 'function') {
                            dzsap_init(_t.find('.the-feature').eq(0), {
                                autoplay: "off"
                                ,swf_location : o.audioplayer_swflocation
                            });
                        } else {
                            if (window.console) { console.info('dzsportfolio.js - warning: audio player not included'); }
                        }
                    }

                    if(type_featuredarea=='video'){
                        var aux = '<div class="the-feature vplayer-tobe skin_pro type-'+type_featuredarea+'" style=""  data-src="'+_t.attr('data-thumbnail')+'"';
                        if(_t.attr('data-sourceogg')!=undefined){
                            aux+=' data-sourceogg="'+_t.attr('data-sourceogg')+'"';
                        }
                        if(_t.attr('data-videotype')!=undefined){
                            aux+=' data-type="'+_t.attr('data-videotype')+'"';
                        }
                        aux+='>';
                        aux+='</div>';
                        _t.find('.the-feature-con').eq(0).prepend(aux);
//console.log(typeof(dzsap_init))
                        _t.find('.the-overlay').hide();
                        if (jQuery.fn.vPlayer != undefined) {

                            var videoplayersettings = {
                                autoplay : "off",
                                videoWidth : "100%",
                                videoHeight : 180,
                                constrols_out_opacity : 0.9,
                                constrols_normal_opacity : 0.9
                                ,settings_hideControls : "off"
                                ,settings_swfPath : o.videoplayer_swflocation
                            };
                            //console.log( _t.find('.the-feature').eq(0));
                            _t.find('.the-feature').eq(0).vPlayer(videoplayersettings);

                        } else {
                            if (window.console) { console.info('dzsportfolio.js - warning: video player not included'); }
                        }
                    }
                    if(type_featuredarea=='testimonial'){

                        _t.find('.the-feature-con').eq(0).prepend('<div class="the-feature type-'+type_featuredarea+'" style=""></div>');

                        _t.find('.the-overlay').hide();
                        _t.find('.the-feature').eq(0).prepend(_t.find('.the-feature-data').eq(0));

                    }
                    if(type_featuredarea=='thumb'){
                        if (_t.attr('data-thumbnail') != undefined && _t.attr('data-thumbnail') != '') {

                            _t.find('.the-feature-con').eq(0).prepend('<div class="the-feature" style="background-image: url('+_t.attr('data-thumbnail')+');"></div>');
                            console.log(_t.find('.the-feature-con').eq(0));
                            //return;
                        } else {
                            _t.find('.the-feature-con').eq(0).prepend(_t.find('.the-feature-content').eq(0));
                        }
                    }

                    var ind_r = 0;
                    if (_t.attr('data-bigimage') != undefined && _t.attr('data-bigimage') != '') {
                        var str_zoombox = ' zoombox';
                        if(o.settings_lightboxlibrary == 'zoombox'){
                            str_zoombox = ' zoombox';
                        }
                        var str_pp = '';
                        if(o.settings_lightboxlibrary == 'prettyphoto'){
                            str_pp = ' rel="prettyPhoto[zoomfolio]"';
                        }


                        if (_t.attr('data-donotopenbigimageinzoombox') == 'on') {
                            str_zoombox = '';
                            str_pp = '';
                        }
                        _t.find('.the-overlay').eq(0).append('<a class="the-overlay-anchor' + str_zoombox + '" href="' + _t.attr('data-bigimage') + '"'+str_pp+'></a>')
                        _t.find('.the-overlay').eq(0).find('.the-overlay-anchor').append('<div class="plus-image"></div>');

                        if(o.settings_lightboxlibrary == 'prettyphoto'){
                            _t.find('.the-overlay').eq(0).find('.the-overlay-anchor').append('<img class="aux-prettyPhoto-thumb" src="'+_t.attr('data-thumbnail')+'" alt="thumbnail"/>');
                        }

                        ind_r += 31;
                    }
                    //console.log(_t);
                    if (_t.attr('data-link') != undefined && _t.attr('data-link') != '') {
                        _t.find('.the-overlay').eq(0).append('<a class="the-overlay-anchor-link" href="' + _t.attr('data-link') + '" style="right: ' + ind_r + 'px;"></a>')
                        _t.find('.the-overlay').eq(0).find('.the-overlay-anchor-link').append('<div class="plus-link"></div>');
                        ind_r += 31;
                    }
                    if (_t.find('.the-content').length > 0 && _t.find('.the-content').eq(0).html() != '') {
                        _t.find('.the-overlay').eq(0).append('<a class="the-overlay-anchor-content" style="right: ' + ind_r + 'px;"></a>')
                        _t.find('.the-overlay').eq(0).find('.the-overlay-anchor-content').append('<div class="plus-content"></div>');
                    }


                    if (_t.find('.the-title').eq(0).parent().hasClass("item-meta") == false) {
                        if (_t.attr('data-link') != undefined && _t.attr('data-link') != '') {
                            _t.find('.the-title, .the-desc').wrapAll('<a class="item-meta" href="' + _t.attr('data-link') + '"></a>');
                        } else {
                            _t.find('.the-title, .the-desc').wrapAll('<div class="item-meta"></div>');
                        }
                    }
                    if (o.settings_skin == 'skin-blog') {
                        var aux = _t.find('.the-feature-con').eq(0).height() - 70;
                        if(_t.find('.item-meta').eq(0).attr('data-inittop')!=undefined){
                            aux = parseInt(_t.find('.item-meta').eq(0).attr('data-inittop'), 10);
                        }
                        //console.info(_t.find('.item-meta').eq(0), _t.find('.item-meta').eq(0).attr('data-inittop'), aux)
                        _t.find('.item-meta').eq(0).css('top', aux);
                        _t.find('.item-meta').eq(0).attr('data-inittop', aux);
                    }
                    if (o.settings_skin == 'skin-boxed') {
                        if(_t.find('.item-meta').length==0){
                            _t.prepend('<div class="item-meta"></div>');
                        }
                        _t.find('.item-meta').eq(0).prepend('<div class="hero-icon"></div>');
                        if(_t.attr('data-color_highlight')!=undefined){
                            _t.find('.item-meta').eq(0).find('.hero-icon').css({
                                'background-color' : _t.attr('data-color_highlight')
                            });
                            _t.css({
                                'border-bottom-color' : _t.attr('data-color_highlight')
                            });
                        }
                        if(_t.attr('data-heroimage')!=undefined){
                            _t.find('.item-meta').eq(0).find('.hero-icon').css({
                                'background-image' : 'url(' + _t.attr('data-heroimage') + ')'
                            });
                        }

                        if(_t.hasClass('layout-left')){
                            _t.find('.item-meta').eq(0).css({

                            })
                            _t.find('.the-feature-con').eq(0).css({
                                'width' : 'auto'
                            })
                        }
                    }
                    if(o.disable_itemmeta=='on'){
                        _t.find('.item-meta').eq(0).hide();
                    }

                    _t.addClass('portitem').removeClass('portitem-tobe');
                    var the_cats = _t.attr('data-category');
                    if (the_cats != undefined && the_cats != '') {

                        the_cats = the_cats.split(';');
                        //console.log(the_cats);
                        for (i = 0; i < the_cats.length; i++){
                            the_cat = the_cats[i];
                            var the_cat_unsanatized = the_cats[i];
                            if (the_cat != undefined) {
                                the_cat = the_cat.replace(' ', '-');
                                _t.addClass('cat-' + the_cat);

                            }
                            sw = false;
                            //console.log(the_cats, arr_cats, the_cat_unsanatized)
                            for (j = 0; j < arr_cats.length; j++) {
                                if (arr_cats[j] == the_cat_unsanatized) {
                                    sw = true;
                                }
                            }
                            if (sw == false) {
                                arr_cats.push(the_cat_unsanatized);
                            }
                        }
                    }
                });

                if (is_already_inited!='on' && o.settings_mode=='masonry' && arr_cats.length > 1 && o.settings_disableCats != 'on') {
                    _selectorCon.prepend('<div class="categories"><div style="display:inline-block;"></div></div>');
                    _selectorCon.children('.categories').append('<div class="a-category allspark active">All</div>');
                    for (i = 0; i < arr_cats.length; i++) {
                        _selectorCon.children('.categories').append('<div class="a-category">' + arr_cats[i] + '</div>');

                    }
                    _selectorCon.find('.a-category').bind('click', click_category);
                }


                //===multi point mode set
                if(o.settings_mode=='advancedscroller'){
                    cthis.removeClass('skin-default');
                    cthis.addClass('advancedscroller skin-white');
                }
                if(o.settings_mode=='wall'){
                    cthis.removeClass('skin-default');
                    cthis.addClass('wall');
                }
                cthis.find('.portitem').each(function($) {
                    var _t = jQuery(this);

                    if (o.settings_clickaction == '' || o.settings_clickaction == 'none') {

                    }
                    if (o.settings_clickaction == 'slide') {

                        _t.find('.the-feature').eq(0).unbind('click');
                        _t.find('.the-feature').eq(0).bind('click', click_item);
                    }
                    if(o.settings_mode=='advancedscroller'){

                        _t.addClass('item-tobe');
                        //_t.prepend('<img class="fullwidth" src="'+_t.attr('data-thumbnail')+'"/>')
                    }
                    if(o.settings_mode=='wall'){

                        _t.addClass('wall-item');
                        //_t.prepend('<img class="fullwidth" src="'+_t.attr('data-thumbnail')+'"/>')
                    }
                });

                //console.log($(window).scrollTop())


                cthis.find('.the-overlay-anchor-content').unbind('click', click_anchorContent);
                cthis.find('.the-overlay-anchor-content').bind('click', click_anchorContent);
                cthis.find('.portitem').unbind('mouseover', mouse_portitem);
                cthis.find('.portitem').bind('mouseover', mouse_portitem);
                cthis.find('.portitem').unbind('mouseout', mouse_portitem);
                cthis.find('.portitem').bind('mouseout', mouse_portitem);

                if(o.settings_skin == 'skin-accordion'){
                    cthis.find('.portitem').unbind('click', mouse_portitem);
                    cthis.find('.portitem').bind('click', mouse_portitem);
                }


                if(o.settings_preloadall=='on'){
                    //console.log(cthis.find('.items').eq(0).children());
                    cthis.find('.items').eq(0).children().each(function(){
                        var _t = jQuery(this);
                        if(_t.attr('data-typefeaturedarea')!='video' && _t.attr('data-typefeaturedarea')!='audio' && _t.attr('data-typefeaturedarea')!='gallery' && _t.attr('data-thumbnail')!=undefined && _t.attr('data-thumbnail')!=''){
                            //console.log(_t.attr('data-thumbnail'))
                            var auxImage = new Image();
                            auxImage.src=_t.attr('data-thumbnail');
                            auxImage.onload=imageLoaded;
                        }else{
                            imageLoaded();
                        }

                    })
                    //===failsafe
                    setTimeout(handle_loaded, 7500);

                }else{
                    setTimeout(handle_loaded, 2000);
                }




            }
            function imageLoaded(e){
                nr_loaded++;
                //console.log(this, this.naturalWidth, this.naturalHeight, this.complete, nr_loaded, nr_children);
                if(nr_loaded>=nr_children){
                    //==leave some time for the width to be set in the items
                    setTimeout(handle_loaded, 1000);
                    //handle_loaded();
                }
            }
            function handle_loaded() {
                if(is_already_inited=='on'){
                    return;
                }

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


                if(o.settings_mode=='masonry'){
                    if (jQuery.fn.isotope != undefined) {
                        isotope_settings = {
                            masonry: {
                                columnWidth: 1
                            }
                            , layoutMode: 'masonry'
                        };
                        //console.log('ceva');
                        cthis.children('.items').isotope(isotope_settings);
                        if (cthis.css('opacity') == 0) {
                            cthis.animate({
                                'opacity': 1
                            }, 2000);
                        }
                    } else {
                        if (window.console) { console.info('dzsportfolio.js - warning: isotope not included') }
                        ;
                        cthis.removeClass('is-sortable');
                        cthis.addClass('is-not-sortable');
                    }
                }
                if(o.settings_mode=='advancedscroller'){
                    //console.log('hmm', cthis);

                    cthis.children('.items').children().addClass('portitem');
                    if (jQuery.fn.advancedscroller != undefined) {
                        cthis.advancedscroller({
                            design_itemwidth : o.item_width
                        });
                    } else {
                        if (window.console) { console.info('dzsportfolio.js - warning: advancedscroller not included'); }
                    }
                }
                if(o.settings_mode=='wall'){
                    //console.log('hmm', cthis);

                    if (jQuery.fn.wall != undefined) {
                        cthis.wall(o.wall_settings);
                    } else {
                        if (window.console) { console.info('dzsportfolio.js - warning: wall not included'); }
                    }
                }

                if(o.settings_lightboxlibrary == 'zoombox'){
                    if ($.fn.zoomBox != undefined) {
                        cthis.find('.zoombox').zoomBox({});
                    } else {
                        if (window.console) { console.info('zoombox not here...') } ;
                    }
                }
                if(o.settings_lightboxlibrary == 'prettyphoto'){
                    if ($.fn.prettyPhoto != undefined) {
                        cthis.find("a[rel^='prettyPhoto']").prettyPhoto({
                            theme: 'pp_default'
                            ,overlay_gallery : false, social_tools: false,  deeplinking: false,
                        });
                    } else {
                        if (window.console) { console.info('prettyphoto not here...') } ;
                    }
                }

                if(cthis.get(0)!=undefined){
                    //cthis.get(0).fn_change_mainColor = fn_change_mainColor; cthis.get(0).fn_change_mainColor('#aaa');
                    cthis.get(0).fn_change_size = fn_change_size; //cthis.get(0).fn_change_mainColor('#aaa');
                }

                cthis.children('.preloader').fadeOut('slow');
                cthis.addClass('loaded');


                jQuery(document).delegate(".btn-close", "click", click_pageContent_close);

                if(o.settings_ajax_enabled=='on'){
                    if(o.settings_ajax_loadmoremethod=='scroll'){
                        $(window).bind('scroll', handle_scroll);
                    }else{
                        //console.info(cthis, o.settings_ajax_loadmoremethod);
                        cthis.append('<div class="btn_ajax_loadmore">Load More</div>');
                        cthis.children('.btn_ajax_loadmore').bind('click', click_btn_ajax_loadmore);
                    }

                }

                jQuery(window).unbind('resize', handleResize);
                jQuery(window).bind('resize', handleResize);
                handleResize();

                is_already_inited='on';
            }
            function handle_scroll(){
                var _t = $(this);
                wh = $(window).height();
                //console.log(_t.scrollTop(), wh, cthis.offset().top, cthis.height(), ind_ajaxPage, o.settings_ajax_pages);

                if(busy_ajax==true || ind_ajaxPage >= o.settings_ajax_pages.length){
                    return;
                }

                if(_t.scrollTop() + wh > cthis.offset().top + cthis.height() - 10){
                    ajax_load_nextpage();
                }
            }
            function click_btn_ajax_loadmore(e){

                if(busy_ajax==true || ind_ajaxPage >= o.settings_ajax_pages.length){
                    return;
                }
                ajax_load_nextpage();
            }
            function ajax_load_nextpage(){

                cthis.children('.preloader').fadeIn('slow');

                $.ajax({
                    url : o.settings_ajax_pages[ind_ajaxPage],
                    success: function(response) {
                        if(window.console !=undefined ){ console.log('Got this from the server: ' + response); }
                        setTimeout(function(){

                            cthis.children('.items').append(response);
                            init_ready();
                            reset_isotope();
                            setTimeout(function(){
                                busy_ajax = false ;
                                cthis.children('.preloader').fadeOut('slow');
                                ind_ajaxPage++;

                                if(ind_ajaxPage >= o.settings_ajax_pages.length){
                                    cthis.children('.btn_ajax_loadmore').fadeOut('slow');
                                }


                                if(o.settings_lightboxlibrary == 'zoombox'){
                                    if ($.fn.zoomBox != undefined) {
                                        cthis.find('.zoombox').zoomBox({});
                                    } else {
                                        if (window.console) { console.info('zoombox not here...') } ;
                                    }
                                }
                                if(o.settings_lightboxlibrary == 'prettyphoto'){
                                    if ($.fn.prettyPhoto != undefined) {
                                        cthis.find("a[rel^='prettyPhoto']").prettyPhoto({
                                            theme: 'pp_default'
                                            ,overlay_gallery : false
                                        });
                                    } else {
                                        if (window.console) { console.info('prettyphoto not here...') } ;
                                    }
                                }

                            }, 1000);
                        }, 1000);
                    },
                    error:function (xhr, ajaxOptions, thrownError){
                        if(window.console !=undefined ){ console.error('not found ' + ajaxOptions); }
                        ind_ajaxPage++;
                        cthis.children('.preloader').fadeOut('slow');

                    }
                });

                busy_ajax = true ;
            }
            function fn_change_mainColor(arg){
            }
            function fn_change_size(arg1, arg2){
                //console.log(arg1, arg2);
                if(arg1!=undefined){
                    cthis.find('.portitem').css({'width' : arg1});
                }
                if(arg2!=undefined){
                    cthis.find('.portitem').height(arg2);
                }
                reset_isotope();



            }
            function reset_isotope(){

                if(o.settings_mode=='masonry'){
                    if (jQuery.fn.isotope != undefined) {
                        //console.log('ceva');
                        //cthis.children('.items').children().css({ 'position' : 'relative', 'transform' : '' });
                        //isotope_settings.sortBy = 'name';
                        setTimeout(function(){
                            cthis.children('.items').isotope( 'reloadItems' ).isotope( 'reLayout' ).isotope( isotope_settings );

                        }, 700);
                        setTimeout(function(){
                            cthis.children('.items').isotope( 'reloadItems' ).isotope( 'reLayout' ).isotope( isotope_settings );

                        }, 1200);
                    } else {
                        if (window.console) { console.info('dzsportfolio.js - warning: isotope not included'); }
                    }
                }
            }
            function add_color_style(){
                if(cthis.find('style.custom-style').length==0){
                    //cthis.append('<style class="custom-style"></style>');
                }
            }
            function mouse_portitem(e) {
                var _t = jQuery(this);
                //console.log(e);
                if (e.type == 'mouseover') {
                    if (o.settings_skin == 'skin-blog') {
                        //console.log(_t);
                        _t.find('.item-meta').eq(0).css('top', 0);
                    }
                }
                if (e.type == 'mouseout') {

                    var aux = _t.find('.item-meta').eq(0).attr('data-inittop');
                    //console.log(e,_t.find('.item-meta').eq(0), _t.find('.item-meta').eq(0).attr('data-inittop'), aux);
                    if (String(aux).indexOf('px') == -1) {
                        aux += 'px';

                    }
                    _t.find('.item-meta').eq(0).css('top', aux);
                }
                //console.log(e.type);
                if (e.type == 'click') {
                    if(_t.hasClass('opened')){

                    }else{
                        _theitems.children('.portitem').removeClass('opened');
                        _t.addClass('opened');
                        e.stopPropagation();
                        e.preventDefault();

                        if(_t.find('.btn-close').length==0){
                            _t.find('.the-content').eq(0).append('<div class="btn-close">Close</div>')
                        }
                        jQuery(window).trigger('resize');
                        reset_isotope();
                        return false;
                    }

                }
            }
            function click_pageContent_close() {
                var _t = jQuery(this);
                if (cthis.has(_t).length == 0) {
                    return false;
                }
                if(o.settings_skin=='skin-accordion'){
                    _t.parent().parent().removeClass('opened');
                    reset_isotope();
                    return false;
                }
                //console.log(_pageCont.height());
                _pageCont.addClass('non-anim');
                _pageCont.css('height', _pageCont.height());
                //_pageCont.removeClass('active');
                //_pageCont.css('height', 0);
                setTimeout(function() {
                    //_pageCont.css('height', 0);
                    _pageCont.removeClass('non-anim');
                }, 100);
                setTimeout(function() {
                    _pageCont.css('height', 0);
                }, 200);
                setTimeout(function() {
                    _pageCont.html('');
                    _pageCont.removeClass('active');
                }, 1000);
            }
            function click_anchorContent() {
                var _t = jQuery(this);
                var _it = _t.parent().parent().parent();
                //console.log(_t, _it); console.log(_pageCont);
                _pageCont.css('height', 'auto');
                var aux = _it.find('.the-content').eq(0).html() + '<div class="btn-close">Close</div>';
                //console.log();
                _pageCont.html(aux);
                _pageCont.addClass('active');


                var scrollElem = scrollableElement('html', 'body');
                var targetOffY = _pageCont.offset().top;


                $(scrollElem).animate({scrollTop: targetOffY}, 400);
                //console.log(scrollElem, targetOffY);

            }
            function click_category() {
                var _t = jQuery(this);
                //console.log(_t);
                var cat = _t.html();

                var options = {};
                var key = "filter";
                var value = '.cat-' + cat;
                if (cat == "All") {
                    value = "*";
                }
                _selectorCon.find('.a-category').removeClass('active');
                _t.addClass('active');
                value = value === "false" ? false : value;

                value = value.replace(" ", "-");
                //console.log(key, value);
                isotope_settings[ key ] = value;
                cthis.children('.items').isotope(isotope_settings);
            }
            function click_item() {
                var _t = jQuery(this);
                var _it = _t.parent();
                //console.log(_it, _it.children('.the-content').height());


                cthis.animate({
                    'height': _it.children('.the-content').height()
                }, {queue: false});
                _pageCont.html('<div class="button-back-con"><div class="button-back">back</div><div class="page-title">' + _it.find('.the-title').eq(0).html() + '</div></div>' + _it.children('.the-content').html());
                _theitems.css({
                    //'left' : '-100%'
                })
                _pageCont.addClass('focused');

                _pageCont.find('.button-back').bind('click', click_back);

            }
            function click_back() {

                cthis.animate({
                    'height': _theitems.height()
                }, {queue: false, complete: complete_backanimation});
                _pageCont.removeClass('focused');
            }
            function complete_backanimation() {
                cthis.css({
                    'height': 'auto'
                })
            }
            function handleResize() {
                ww = $(window).width();
                wh = $(window).height();
                tw = cthis.width();

                //console.log(ww,tw);

                cthis.removeClass('under-800').removeClass('under-480');

                if(tw<800){
                    cthis.addClass('under-800');
                }
                if(tw<480){
                    cthis.addClass('under-480');
                }
                //if(String(cthis.attr('class')).indexOf('special-grid')>-1){
                clearTimeout(inter_reset);
                inter_reset = setTimeout(function(){
                    reset_isotope();

                    if(thumbh_dependent_on_w==true){

                        cthis.find('.portitem').each(function() {
                            var _t = jQuery(this);
                            _t.find('.the-feature-con').eq(0).css({
                                'height' : (_t.find('.the-feature-con').eq(0).outerWidth(false) * o.design_thumbh)
                            })

                            if(_t.find('.the-thumb-content').length>0){
                                _t.find('.the-thumb-content').eq(0).css({
                                    'width' : _t.find('.the-feature-con').eq(0).outerWidth(false)
                                    ,'height' : _t.find('.the-feature-con').eq(0).outerHeight(false)
                                })
                            }
                        });

                    }
                }, 100);
                //}

                cthis.find('.portitem').each(function() {
                    var _t = jQuery(this);

                    if (_t.width() > tw) {
                        if (_t.attr('data-origwidth') == undefined) {
                            _t.attr('data-origwidth', _t.css('width'));
                        }
                        _t.css('width', tw);
                    } else {

                        if (_t.attr('data-origwidth') != undefined) {
                            _t.css('width', _t.attr('data-origwidth'));
                        }
                    }








                    if (o.settings_skin == 'skin-blog') {
                    }


                });
                if (_pageCont.hasClass('active')) {

                }
            }
            return this;
        })
    }


    window.dzsp_init = function(selector, settings) {
        $(selector).dzsportfolio(settings);
    };
})(jQuery);

function scrollableElement(els) {
    for (var i = 0, argLength = arguments.length; i < argLength; i++) {
        var el = arguments[i],
            $scrollElement = jQuery(el);
        if ($scrollElement.scrollTop() > 0) {
            return el;
        } else {
            $scrollElement.scrollTop(1);
            var isScrollable = $scrollElement.scrollTop() > 0;
            $scrollElement.scrollTop(0);
            if (isScrollable) {
                return el;
            }
        }
    }
    return [];
}


window.requestAnimFrame = (function() {
    //console.log(callback);
    return window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function(/* function */callback, /* DOMElement */element) {
            window.setTimeout(callback, 1000 / 60);
        };
})();