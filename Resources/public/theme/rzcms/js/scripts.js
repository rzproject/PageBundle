$(document).ready(function() {
/***************************************************
	MENU
***************************************************/
	$('body').addClass('js');
		  var $menu = $('#menu, #menu2 '),
		  	  $menulink = $('.menu-link'),
		  	  $menuTrigger = $('.has-submenu > a');

		$menulink.click(function(e) {
			e.preventDefault();
			$menulink.toggleClass('active');
			$menu.toggleClass('active');
		});

		$menuTrigger.click(function(e) {
			e.preventDefault();
			var $this = $(this);
			$this.toggleClass('active').next('ul').toggleClass('active');
		});
			
/***************************************************
		PRETTYPHOTO
***************************************************/
$('a[data-rel]').each(function() {
$(this).attr('rel', $(this).attr('data-rel')).removeAttr('data-rel');
});
$("a[rel^='prettyPhoto']").prettyPhoto();
	jQuery("a[rel^='prettyPhoto'], a[rel^='lightbox']").prettyPhoto({
overlay_gallery: false, social_tools: false,  deeplinking: false
});

/***************************************************
	ABOUT PAGE - LATEST WORK
***************************************************/
	$("#zoom_latest").dzsportfolio({
		settings_slideshowTime:15,
		settings_mode: 'masonry',
		title: '',
		design_thumbw: '250',
		design_thumbh: '1/1',
		settings_disableCats: 'on',
		settings_lightboxlibrary: "prettyphoto",
		design_categories_pos: 'bottom'
	});	
		
/***************************************************
	SERVICES PAGE - CLIENT CAROUSEL
***************************************************/
	 $("#client_slider").dzsportfolio({
		settings_slideshowTime:3,
		settings_mode: 'advancedscroller',
		settings_skin:'skin-black',
		title: '',
		design_bulletspos: "none",
		settings_lightboxlibrary: "prettyphoto",
		disable_itemmeta: 'off'
	});
	
/***************************************************
	PORTFOILO
***************************************************/
	window.dzsp_init(("#zoom_portfolio"),{
		settings_slideshowTime:3,
		settings_mode: 'masonry',
		title: '',
		design_item_height_same_as_width: 'on',
		settings_lightboxlibrary: "prettyphoto",
		settings_preloadall: 'on',
		disable_itemmeta:'on'
	});
	
/***************************************************
	PORTFOILO 2
***************************************************/
	window.dzsp_init(("#zoom_portfolio2"),{
		settings_slideshowTime:3,
		settings_mode: 'masonry',
		title: '',
		design_item_height_same_as_width: 'on',
		settings_lightboxlibrary: "prettyphoto",
		settings_preloadall: 'on'
	});
	
/***************************************************
	GALLERY - CAPTION HOVER
***************************************************/
	$("#zoom_portfolio_caption").dzsportfolio({
		settings_slideshowTime: 15,
		settings_mode: 'masonry',
		title: '',
		design_item_width: '',
		design_thumbh: '300',
		settings_disableCats: 'off',
		design_categories_pos: 'top'
	});
	
/***************************************************
	BLOG
***************************************************/
	$("#zoom_blog").dzsportfolio({
		settings_slideshowTime: 15,
		settings_mode: 'masonry',
		title: '',
		design_item_width: '',
		design_thumbh: '292',
		settings_disableCats: 'on',
		design_categories_pos: 'top',
		settings_ajax_enabled: 'on',
		settings_ajax_loadmoremethod: 'button',
		settings_ajax_pages: ['ajax1.html']
	});
	
/***************************************************
	BLOG - RELATED POSTS CAROUSEL
***************************************************/
	$("#blog_carousel").dzsportfolio({
		settings_slideshowTime:3,
		settings_mode: 'advancedscroller',
		settings_skin:'skin-black',
		title: '',
		design_bulletspos: "none",
		settings_lightboxlibrary: "prettyphoto",
		disable_itemmeta: 'off'
	});
	
/***************************************************
	BLOG - RELATED POSTS
***************************************************/
	$("#blog_related").dzsportfolio({
		settings_slideshowTime:3,
		settings_mode: 'advancedscroller',
		title: '',
		settings_lightboxlibrary: "prettyphoto",
		disable_itemmeta: 'off'
	});
	
/***************************************************
	PARALLAX
***************************************************/
	
if( navigator.userAgent.match(/Android/i) || 
	navigator.userAgent.match(/webOS/i) ||
	navigator.userAgent.match(/iPhone/i) || 
	navigator.userAgent.match(/iPad/i)|| 
	navigator.userAgent.match(/iPod/i) || 
	navigator.userAgent.match(/BlackBerry/i)){
			$('.parallax').addClass('mobile');
		}
	});	

/***************************************************
	LARGE IMAGE FADE
***************************************************/
(function(e){e.fn.krioImageLoader=function(t){var n=e(this).find("img").css({opacity:0,visibility:"hidden"}).addClass("krioImageLoader"),r=n.size(),i=e.extend({},e.fn.krioImageLoader.defaults,t),s=setInterval(function(){r?n.filter(".krioImageLoader").each(function(){if(this.complete){o(this);r--}}):clearInterval(s)},i.loadedCheckEvery),o=function(t){e(t).css({visibility:"visible"}).animate({opacity:1},i.imageEnterDelay,function(){e(t).removeClass("krioImageLoader")})}};e.fn.krioImageLoader.defaults={loadedCheckEvery:350,imageEnterDelay:300}})(jQuery);

/***************************************************
	GOOGLE MAP - ADD YOUR ADDRESS HERE
***************************************************/
$(window).load(function() {
	$(".google-maps").gmap3({
    marker:{
     
address:"23, Mornington Crescent, London",options:{icon: "img/marker.png"}},
    map:{
      options:{
styles: [ {
stylers: [
{ "visibility": "on" }, { "saturation": -100 }, { "gamma": 1 }]
}],
        zoom: 14,
		scrollwheel: false,
		mapTypeControl: false,
		streetViewControl: false,
		scalControl: false,
		draggable: false,}
		}
	});	
});	

/***************************************************
	DRIBBBLE
***************************************************/	
(function(e){"use strict";e.jribbble={};var t=function(t,s){e.ajax({type:"GET",url:"http://api.dribbble.com"+t,data:s[1]||{},dataType:"jsonp",success:function(e){e===undefined?s[0]({error:!0}):s[0](e)}})},s={getShotById:"/shots/$/",getReboundsOfShot:"/shots/$/rebounds/",getShotsByList:"/shots/$/",getShotsByPlayerId:"/players/$/shots/",getShotsThatPlayerFollows:"/players/$/shots/following/",getPlayerById:"/players/$/",getPlayerFollowers:"/players/$/followers/",getPlayerFollowing:"/players/$/following/",getPlayerDraftees:"/players/$/draftees/",getCommentsOfShot:"/shots/$/comments/",getShotsThatPlayerLikes:"/players/$/shots/likes/"},o=function(e){return function(){var s=[].slice.call(arguments),o=e.replace("$",s.shift());t(o,s)}};for(var r in s)e.jribbble[r]=o(s[r])})(jQuery,window,document);

/***************************************************
		BACK TO TOP LINK
***************************************************/
	$('.go-top').click(function(event) {
		event.preventDefault();
		$('html, body').animate({scrollTop: 0}, 300);
	});

/***************************************************
	TOOLTIP
***************************************************/
$("[rel=tooltip]").tooltip();
$("[data-rel=tooltip]").tooltip();

/***************************************************
		IMAGE HOVER
***************************************************/
	$(".hover_img").on('mouseover',function(){
			var info=$(this).find("img");
			info.stop().animate({opacity:0.8},300);
		}
	);
	$(".hover_img").on('mouseout',function(){
			var info=$(this).find("img");
			info.stop().animate({opacity:1},300);
		});

/***************************************************
	STICKY MENU
***************************************************/
var sticky = $('.sticky'),
    stickyHeight = $(sticky).outerHeight(),
    stickyTop = $(sticky).offset().top,
    stickyBottom = stickyTop + stickyHeight;
$(window).scroll(function(){
  var scrollTop = $(window).scrollTop();
   if(scrollTop > stickyBottom){
    if($(sticky).is(':hidden')){
      $(sticky).slideDown('slow').find('.logo').addClass('spin');
    }
  }else{
    if($(sticky).is(':visible')){
      $(sticky).fadeOut('fast').find('.logo').removeClass('spin');
    }
  }
});

$(sticky).on('click', '.logo', function(){
  $('html, body').animate({scrollTop: 0} ,'slow');
});



