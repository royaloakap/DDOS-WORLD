$(':checkbox:checked').prop('checked',false);
var mapData = {
                "US": 298,
                "SA": 200,
                "AU": 760,
                "IN": 200,
                "GB": 120,
            };


jQuery('#world-map-marker').vectorMap(
{
    map: 'world_mill_en',
    backgroundColor: '#353c48',
    borderColor: '#fff',
    borderOpacity: 0.25,
    borderWidth: 0,
    color: '#5b6474',
    regionStyle : {
        initial : {
          fill : '#5b6474'
        }
      },

    markerStyle: {
      initial: {
                    r: 7,
                    'fill': '#fff',
                    'fill-opacity':1,
                    'stroke': '#000',
                    'stroke-width' : 2,
                    'stroke-opacity': 0.4
                },
                },
   
    markers : [{
        latLng : [21.00, 78.00],
        name : 'INDIA : 350'
      
      },
      {
        latLng : [-33.00, 151.00],
        name : 'Australia : 250'
        
      },
      {
        latLng : [36.77, -119.41],
        name : 'USA : 250'
      
      },
      {
        latLng : [55.37, -3.41],
        name : 'UK   : 250'
      
      },
      {
        latLng : [25.20, 55.27],
        name : 'UAE : 250'
      
      }],
      series: {
                    regions: [{
                        values: mapData,
                        scale: ["#232832", "#202733"],
                        normalizeFunction: 'polynomial'
                    }]
                },
    hoverOpacity: null,
    normalizeFunction: 'linear',
    zoomOnScroll: false,
    scaleColors: ['#b6d6ff', '#005ace'],
    selectedColor: '#c9dfaf',
    selectedRegions: [],
    enableZoom: false,
    hoverColor: '#fff',
    
    
});

 window.addEventListener('resize', function() {
        vectorMap.resize();
    });
 
 // Real Time chart
        

        var data = [],
            totalPoints = 100;

        function getRandomData() {

            if (data.length > 0)
                data = data.slice(1);

            // Do a random walk

            while (data.length < totalPoints) {

                var prev = data.length > 0 ? data[data.length - 1] : 50,
                    y = prev + Math.random() * 10 - 5;

                if (y < 0) {
                    y = 0;
                } else if (y > 100) {
                    y = 100;
                }

                data.push(y);
            }

            // Zip the generated y values with the x values

            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]])
            }

            return res;
        }

        // Set up the control widget

        var updateInterval = 20;
        $("#updateInterval").val(updateInterval).change(function () {
            var v = $(this).val();
            if (v && !isNaN(+v)) {
                updateInterval = +v;
                if (updateInterval < 1) {
                    updateInterval = 1;
                } else if (updateInterval > 2000) {
                    updateInterval = 2000;
                }
                $(this).val("" + updateInterval);
            }
        });

        var plot = $.plot("#placeholder", [ getRandomData() ], {
            series: {
                shadowSize: 0   // Drawing is faster without shadows
            },
            yaxis: {
                min: 0,
                max: 100
            },
            xaxis: {
                show: false
            },
            colors: ["#96a2b4"],
            grid: {
                color: "rgba(120, 130, 140, 0.2)",
                hoverable: true,
                borderWidth: 0,
                backgroundColor: '#353c48'
            },
            tooltip: true,
              resize:true,
            tooltipOpts: {
                content: "Y: %y",
                defaultTheme: false
            }
        

        });

        function update() {

            plot.setData([getRandomData()]);

            // Since the axes don't change, we don't need to call plot.setupGrid()

            plot.draw();
            setTimeout(update, updateInterval);
        }

        update();
         $('.vcarousel').carousel({
            interval: 3000
         })
         $("body").trigger("resize");

//sparkline charts
$(document).ready(function() {
   var sparklineLogin = function() { 
       
  
        $("#sparkline8").sparkline([2,4,4,6,8,5,6,4,8,6,6,2 ], {
            type: 'line',
            width: '100%',
            height: '50',
            lineColor: '#99d683',
            fillColor: '#99d683',
            maxSpotColor: '#99d683',
            highlightLineColor: 'rgba(0, 0, 0, 0.2)',
            highlightSpotColor: '#99d683'
        });
        $("#sparkline9").sparkline([0,2,8,6,8,5,6,4,8,6,6,2 ], {
            type: 'line',
            width: '100%',
            height: '50',
            lineColor: '#13dafe',
            fillColor: '#13dafe',
            minSpotColor:'#13dafe',
            maxSpotColor: '#13dafe',
            highlightLineColor: 'rgba(0, 0, 0, 0.2)',
            highlightSpotColor: '#13dafe'
        });
        $("#sparkline10").sparkline([2,4,4,6,8,5,6,4,8,6,6,2], {
            type: 'line',
            width: '100%',
            height: '50',
            lineColor: '#ffdb4a',
            fillColor: '#ffdb4a',
            maxSpotColor: '#ffdb4a',
            highlightLineColor: 'rgba(0, 0, 0, 0.2)',
            highlightSpotColor: '#ffdb4a'
        });
       
   }
    var sparkResize;
 
        $(window).resize(function(e) {
            clearTimeout(sparkResize);
            sparkResize = setTimeout(sparklineLogin, 500);
        });
        sparklineLogin();

});

// Slimscroll
 $('.slimscrollcountry').slimScroll({
          height: '250',
          position: 'right',
          size: "5px",
          color: '#dcdcdc',
          
      });