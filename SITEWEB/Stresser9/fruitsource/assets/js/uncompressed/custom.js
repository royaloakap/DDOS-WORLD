/* -------------------- Check Browser --------------------- */
function browser() {
	
	var isOpera = !!(window.opera && window.opera.version);  // Opera 8.0+
	var isFirefox = testCSS('MozBoxSizing');                 // FF 0.8+
	var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
	    // At least Safari 3+: "[object HTMLElementConstructor]"
	var isChrome = !isSafari && testCSS('WebkitTransform');  // Chrome 1+
	//var isIE = /*@cc_on!@*/false || testCSS('msTransform');  // At least IE6

	function testCSS(prop) {
	    return prop in document.documentElement.style;
	}
	
	if (isOpera) {
		
		return false;
		
	}else if (isSafari || isChrome) {
		
		return true;
		
	} else {
		
		return false;
		
	}
	
}

$(document).ready(function($){

	if($(".boxchart").length) {

		if (retina()) {

			$(".boxchart").sparkline('html', {
			    type: 'bar',
			    height: '60', // Double pixel number for retina display
				barWidth: '8', // Double pixel number for retina display
				barSpacing: '2', // Double pixel number for retina display
			    barColor: '#ffffff',
			    negBarColor: '#eeeeee'}
			);
			
			if (jQuery.browser.mozilla) {
				
				if (!!navigator.userAgent.match(/Trident\/7\./)) {
					$(".boxchart").css('zoom',0.5);
					$(".boxchart").css('height','30px;').css('margin','0px 15px -15px 17px');
				} else {
					$(".boxchart").css('MozTransform','scale(0.5,0.5)').css('height','30px;');
					$(".boxchart").css('height','30px;').css('margin','-15px 15px -15px -5px');
				}
				
			} else {
				$(".boxchart").css('zoom',0.5);
			}

		} else {

			$(".boxchart").sparkline('html', {
			    type: 'bar',
			    height: '30',
				barWidth: '4',
				barSpacing: '1',
			    barColor: '#ffffff',
			    negBarColor: '#eeeeee'}
			);

		}		

	}
	
	if($(".linechart").length) {

		if (retina()) {

			$(".linechart").sparkline('html', {
			    width: '130',
				height: '60',
				lineColor: '#ffffff',
				fillColor: false,
				spotColor: false,
				maxSpotColor: false,
				minSpotColor: false,
				spotRadius: 2,
				lineWidth: 2
			});
			
			if (jQuery.browser.mozilla) {
				
				if (!!navigator.userAgent.match(/Trident\/7\./)) {
					$(".linechart").css('zoom',0.5);
					$(".linechart").css('height','30px;').css('margin','0px 15px -15px 17px');
				} else {
					$(".linechart").css('MozTransform','scale(0.5,0.5)').css('height','30px;');
					$(".linechart").css('height','30px;').css('margin','-15px 15px -15px -5px');
				}
				
			} else {
				$(".linechart").css('zoom',0.5);
			}

		} else {

			$(".linechart").sparkline('html', {
			    width: '65',
				height: '30',
				lineColor: '#ffffff',
				fillColor: false,
				spotColor: false,
				maxSpotColor: false,
				minSpotColor: false,
				spotRadius: 2,
				lineWidth: 1
			});

		}		

	}
	
	if($('.chart-stat').length) {
	
		if (retina()) {

			$(".chart-stat > .chart").each(function(){

				var chartColor = $(this).css('color');	

				$(this).sparkline('html', {				
				    width: '180%',//Width of the chart - Defaults to 'auto' - May be any valid css width - 1.5em, 20px, etc (using a number without a unit specifier won't do what you want) - This option does nothing for bar and tristate chars (see barWidth)
					height: 80,//Height of the chart - Defaults to 'auto' (line height of the containing tag)
					lineColor: chartColor,//Used by line and discrete charts to specify the colour of the line drawn as a CSS values string
					fillColor: false,//Specify the colour used to fill the area under the graph as a CSS value. Set to false to disable fill
					spotColor: false,//The CSS colour of the final value marker. Set to false or an empty string to hide it
					maxSpotColor: false,//The CSS colour of the marker displayed for the maximum value. Set to false or an empty string to hide it
					minSpotColor: false,//The CSS colour of the marker displayed for the mimum value. Set to false or an empty string to hide it
					spotRadius: 2,//Radius of all spot markers, In pixels (default: 1.5) - Integer
					lineWidth: 2//In pixels (default: 1) - Integer
				});
				
				if (jQuery.browser.mozilla) {
					
					if (!!navigator.userAgent.match(/Trident\/7\./)) {
						$(this).css('zoom',0.5);
					} else {
						$(this).css('MozTransform','scale(0.5,0.5)');
						$(this).css('height','40px;').css('margin','-20px 0px -20px -25%');
					}
					
				} else {
					$(this).css('zoom',0.5);
				}

			});

		} else {

			$(".chart-stat > .chart").each(function(){

				var chartColor = $(this).css('color');

				$(this).sparkline('html', {				
				    width: '90%',//Width of the chart - Defaults to 'auto' - May be any valid css width - 1.5em, 20px, etc (using a number without a unit specifier won't do what you want) - This option does nothing for bar and tristate chars (see barWidth)
					height: 40,//Height of the chart - Defaults to 'auto' (line height of the containing tag)
					lineColor: chartColor,//Used by line and discrete charts to specify the colour of the line drawn as a CSS values string
					fillColor: false,//Specify the colour used to fill the area under the graph as a CSS value. Set to false to disable fill
					spotColor: false,//The CSS colour of the final value marker. Set to false or an empty string to hide it
					maxSpotColor: false,//The CSS colour of the marker displayed for the maximum value. Set to false or an empty string to hide it
					minSpotColor: false,//The CSS colour of the marker displayed for the mimum value. Set to false or an empty string to hide it
					spotRadius: 2,//Radius of all spot markers, In pixels (default: 1.5) - Integer
					lineWidth: 2//In pixels (default: 1) - Integer
				});

			});

		}
	
	}
});

$(document).ready(function($){

	
	if($(".todo-list").length) {
		
		/* ---------- ToDo List Action Buttons ---------- */
		$(".todo-actions > a").click(function(){
			
			if ($(this).find('i').attr('class') == 'fa-check done') {
								
				$(this).find('i').removeClass('done');
				$(this).parent().parent().find('span').css({ opacity: 1 });
				$(this).parent().parent().find('.desc').css('text-decoration', 'none');
				
			} else {
				
				$(this).find('i').addClass('done');
				$(this).parent().parent().find('span').css({ opacity: 0.25 });
				$(this).parent().parent().find('.desc').css('text-decoration', 'line-through');
				
			}

			return false;
			
		});
		
		/* ---------- ToDo Remove Button ---------- */
		$(".todo-list > li > a.remove").click(function(){
			
			$(this).parent().slideUp();
			return false;
			
		});
		
		/* ---------- ToDo List Active Sortable List ---------- */
		$(function() {
		    $(".todo-list").sortable();
		    $(".todo-list").disableSelection();
		});
	}

});

$(document).ready(function($){

	/* ---------- Activity Feed ---------- */
	if($("#feed").length) {
		
		$('#filter > li > a').click(function(e){
			
			var selected = $(this).attr('data-option-value');
			
			$(this).parent().parent().find('a').each(function(){
				
				$(this).removeClass('active');
				
			});
						
			$(this).addClass('active');
			
			
			$('#timeline > li').each(function(){
				
				if($(this).hasClass(selected)) {
					
					$(this).show();
					
				} else if (selected == 'all') {
					
					$(this).show();
					
				} else {
					
					$(this).hide();
					
				}
				
			});
			
			e.preventDefault();
			
		});

	}

});

$(document).ready(function(){
	/* ---------- Skill Bars ---------- */
	if($(".skill-bar")){

		$(".meter > span").each(function() {
			
			var percent = parseInt($(this).html().replace("%",""));
			$(this).width(0)
			.animate({width: percent+'%', countNum: percent + 1}, {
				duration: 3000,
				easing:'linear',
				step: function() {
					$(this).text(Math.floor(this.countNum)+"%");
				},
				complete: function() {
					//do nothing
				}
			});

		});

	}
			
});

$(document).ready(function(){
	
	if($(".taskProgress")) {
	
		$(".taskProgress").each(function(){
			
			var endValue = parseInt($(this).html());
											
			$(this).progressbar({
				value: endValue
			});
			
			$(this).parent().find(".percent").html(endValue + "%");
			
		});
	
	}
	
});

/* ---------- Delete Comment ---------- */
$(document).ready(function($){
    $('.discussions').find('.delete').click(function(){
		
		$(this).parent().fadeTo("slow", 0.00, function(){ //fade
			$(this).slideUp("slow", function() { //slide up
		    	$(this).remove(); //then remove from the DOM
		    });
		});
	
	});
});

$(document).ready(function($){
	/* ---------- Notifications ---------- */
	$('.noty').click(function(e){
		e.preventDefault();
		var options = $.parseJSON($(this).attr('data-noty-options'));
		noty(options);
	});
});