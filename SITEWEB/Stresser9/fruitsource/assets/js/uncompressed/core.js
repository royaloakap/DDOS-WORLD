jQuery(document).ready(function($){
		
	/* ---------- Remove elements in IE8 ---------- */
	if(jQuery.browser.version.substring(0, 2) == "8.") {
		 
		$('.hideInIE8').remove();
		
	}
	
	/* ---------- Disable moving to top ---------- */
	$('a[href="#"][data-top!=true]').click(function(e){
		e.preventDefault();
	});
	

	/* ---------- Tabs ---------- */
	$('#myTab a:first').tab('show');
	$('#myTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	/* ---------- Tooltip ---------- */
	$('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

	/* ---------- Popover ---------- */
	$('[rel="popover"],[data-rel="popover"],[data-toggle="popover"]').popover();

	
	$('.btn-close').click(function(e){
		e.preventDefault();
		$(this).parent().parent().parent().fadeOut();
	});
	$('.btn-minimize').click(function(e){
		e.preventDefault();
		var $target = $(this).parent().parent().next('.panel-body');
		if($target.is(':visible')) $('i',$(this)).removeClass('fa-chevron-up').addClass('fa-chevron-down');
		else 					   $('i',$(this)).removeClass('fa-chevron-down').addClass('fa-chevron-up');
		$target.slideToggle('slow', function() {
		    widthFunctions();
		});
		
	});
	$('.btn-setting').click(function(e){
		e.preventDefault();
		$('#myModal').modal('show');
	});

});




/* ---------- Check Retina ---------- */
function retina(){
	
	retinaMode = (window.devicePixelRatio > 1);
	
	return retinaMode;
	
}

/* ---------- Main Menu Open/Close, Min/Full ---------- */
jQuery(document).ready(function($){
		
	$('#main-menu-toggle').click(function(){
		
		if($(this).hasClass('open')){
			
			$(this).removeClass('open').addClass('close');
			
			var span = $('.main').attr('class');
			var spanNum = parseInt(span.replace( /^\D+/g, ''));
			var newSpanNum = spanNum + 2;
			var newSpan = 'span' + newSpanNum;
						
			$('.main').addClass('full');
			$('.navbar-brand').addClass('noBg');
			$('.sidebar').hide();
			
		} else {
			
			$(this).removeClass('close').addClass('open');
			
			var span = $('.main').attr('class');
			var spanNum = parseInt(span.replace( /^\D+/g, ''));
			var newSpanNum = spanNum - 2;
			var newSpan = 'span' + newSpanNum;
			
			$('.main').removeClass('full');
			$('.navbar-brand').removeClass('noBg');
			$('.sidebar').show();
			
		}				
		
	});
		
	$('#main-menu-min').click(function(){
		
		if($(this).hasClass('full')){
			
			$(this).removeClass('full').addClass('minified').find('i').removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
			
			$('body').addClass('sidebar-minified');
			$('.main').addClass('sidebar-minified');
			$('.sidebar').addClass('minified');
			
			$('.dropmenu > .chevron').removeClass('opened').addClass('closed');
			$('.dropmenu').parent().find('ul').hide();
			
			$('.sidebar > div > ul > li > a > .chevron').removeClass('closed').addClass('opened');
			$('.sidebar > div > ul > li > a').addClass('open');
						
		} else {
			
			$(this).removeClass('minified').addClass('full').find('i').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
			
			$('body').removeClass('sidebar-minified');
			$('.main').removeClass('sidebar-minified');
			$('.sidebar').removeClass('minified');
			
			$('.sidebar > div > ul > li > a > .chevron').removeClass('opened').addClass('closed');
			$('.sidebar > div > ul > li > a').removeClass('open');		
			
		}
		
	});
	
	$('.dropmenu').click(function(e){
		
		e.preventDefault();
		
		if ($('.sidebar').hasClass('minified')) {
			
			if ($(this).hasClass('open')) {
				
				//do nothing or add here any function
				
			} else {
				$(this).parent().find('ul').first().slideToggle();
				
				if ($(this).find('.chevron').hasClass('closed')) {

					$(this).find('.chevron').removeClass('closed').addClass('opened')

				} else {

					$(this).find('.chevron').removeClass('opened').addClass('closed')

				}
								
			}
			
		} else {
			
			$(this).parent().find('ul').first().slideToggle();

			if ($(this).find('.chevron').hasClass('closed')) {

				$(this).find('.chevron').removeClass('closed').addClass('opened');

			} else {

				$(this).find('.chevron').removeClass('opened').addClass('closed');

			}
			
		}
	
	});
	
	if ($('.sidebar').hasClass('minified')) {
		
		$('.sidebar > div > ul > li > a > .chevron').removeClass('closed').addClass('opened');
		$('.sidebar > div > ul > li > a').addClass('open');
		$('body').addClass('sidebar-minified');
	}	
		
});	

jQuery(document).ready(function($){
	
	/* ---------- Add class .active to current link  ---------- */
	$('ul.nav-sidebar').find('a').each(function(){
		
		if($($(this))[0].href==String(window.location)) {
			
			$(this).addClass('active');
			
			$(this).parents('ul').add(this).each(function(){
			    $(this).show();
				$(this).prev('a').find('.chevron').removeClass('closed').addClass('opened');
			});
			
		}	
	
	});

});


$(document).ready(function(){
				
	widthFunctions();
			
});
           
/* ---------- Page width functions ---------- */

$(window).bind("resize", widthFunctions);

function widthFunctions(e) {
		
	if($('.timeline')) {
		
		$('.timeslot').each(function(){
			
			var timeslotHeight = $(this).find('.task').outerHeight();
			
			$(this).css('height',timeslotHeight);
			
		});
		
	}
	
	var sidebarHeight = $('.sidebar').outerHeight();
	var mainHeight = $('.main').outerHeight();
		
	var sidebarLeftHeight = $('.sidebar').outerHeight();
	var contentHeight = $('.main').height();
	var contentHeightOuter = $('.main').outerHeight();
	
	var headerHeight = $('.navbar').outerHeight();
	var footerHeight = $('footer').outerHeight();
	
    var winHeight = $(window).height();
    var winWidth = $(window).width();

	if (winWidth < 992) {
		$('#main-menu-min').removeClass('minified').addClass('full').find('i').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
		$('body').removeClass('sidebar-minified');
		$('.main').removeClass('sidebar-minified');
		$('.sidebar').removeClass('minified');
	}
	
	if (winWidth > 768) {
		$('.main').css('min-height',winHeight-headerHeight-footerHeight);
	}
	
	if (winWidth < 768) {
		
		if($('.chat-full')) {

			$('.chat-full').each(function(){

				$(this).addClass('alt');

			});

		}
		
	} else {
		
		if($('.chat-full')) {

			$('.chat-full').each(function(){

				$(this).removeClass('alt');

			});

		}
		
	}
   
}