/** ==================
 *  GROWTH CHART LARGE
 *  ==================
 */
var chart;
var graph;
var chartData;
function growth_large(trader_id,el,host,size)
{
    var url = 'http://' + host + '/trader/growth/?trader_id=' + trader_id;

    console.log('Requesting ' + url);
    $.get(url, function( data ) {


        var chartData = data.data.growth_daily;
        console.log('data: ' + chartData);
        AmCharts.ready(function () {
            // SERIAL CHART
            chart = new AmCharts.AmSerialChart();
            chart.pathToImages = "/js/am/images/";
            chart.dataProvider = chartData;
            chart.marginLeft = 10;
            chart.categoryField = "date";
            chart.dataDateFormat = "MM/DD/YYYY";

            // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
            //chart.addListener("dataUpdated", zoomChart);

            // AXES
            // category
            var categoryAxis = chart.categoryAxis;
            categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
            categoryAxis.minPeriod = "DD"; // our data is yearly, so we set minPeriod to YYYY
            categoryAxis.dashLength = 3;
            categoryAxis.minorGridEnabled = false;
            categoryAxis.minorGridAlpha = 0.1;

            // value
            var valueAxis = new AmCharts.ValueAxis();
            valueAxis.axisAlpha = 0;
            valueAxis.inside = false;
            valueAxis.dashLength = 3;
            chart.addValueAxis(valueAxis);

            // GRAPH
            graph = new AmCharts.AmGraph();
            graph.type = "smoothedLine"; // this line makes the graph smoothed line.
            graph.lineColor = "#8aba23";
            graph.negativeLineColor = "#7d8768"; // this line makes the graph to change color when it drops below 0
            /*graph.bullet = "round";
            graph.bulletSize = 7;
            graph.bulletBorderColor = "#FFFFFF";
            graph.bulletBorderAlpha = 1;
            graph.bulletBorderThickness = 1;*/


            graph.lineColorField = "lineColor";
            graph.fillColorsField = "lineColor";
            graph.fillAlphas = 0.2;
            graph.lineThickness = 5;
            graph.valueField = "value";
            graph.balloonText = "[[category]]<br><b><span style='font-size:14px;'>[[value]]%</span></b>";
            chart.addGraph(graph);


            // CURSOR
            var chartCursor = new AmCharts.ChartCursor();
            chartCursor.cursorAlpha = 0;
            chartCursor.cursorPosition = "mouse";
            chartCursor.cursorColor = "#8aba23";
            chartCursor.categoryBalloonDateFormat = "MM/DD/YY";
            chart.addChartCursor(chartCursor);

            // WRITE
            chart.write("trader_growth_chart_large");
        });

    });
}
function zoomChart() {
    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
    chart.zoomToDates(new Date(1972, 0), new Date(1984, 0));
}

/** ==================
 *  GROWTH CHART SMALL
 *  ==================
 */
function growth_small(trader_id,el,host,size)
{
    var url = 'http://' + host + '/trader/growth_daily/?trader_id=' + trader_id;
    //console.log('Requesting ' + url);
    $.get(url, function( data ) {

        //var res = JSON.parse(data);
        var dates = data.data.growth_daily.dates;
        var growth = data.data.growth_daily.growth;
        var data;

        var ctx = document.getElementById(el).getContext("2d");
        var gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(84,91,67,0.3)');
        gradient.addColorStop(.6, 'rgba(255,255,255,0)');


        var gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(84,91,67,0.3)');
        gradient2.addColorStop(1, 'rgba(255,255,255,0)');


        var monthNames = [1,2,3,4,5,6,7,8,9,10,11,12 ];

        var i,_m,len
        var tmp=dates;
        len = dates.length;


        for(i=0;i< len;i++)
        {

         var d = new Date(tmp[i]);
         dates[i] = monthNames[d.getUTCMonth() ] + '/' + d.getDate();
            //console.log(dates[i] + ' - ' + d);
        }



        var _d = growth.slice(0).sort(function(a,b){return a-b});
        var _c = _d.length -1;
        var _h = _d[_c] ;
        var _l = _d[0] ;

        var _buffer = 50;
        var _scaleSteps =  Math.round(Math.abs(_h)+Math.abs(_l));// + (_buffer * 2);
        var _scaleStepWidth =Math.floor(_scaleSteps / _scaleSteps);// 10;// Math.ceil(_h/4) ;
        var _scaleStartValue = (_l) - (_scaleStepWidth *3);// - _buffer;



        console.log(_d + ' high:' + _h + ' low:' + _l + ' count:' + _c + ' scaleStartVal: ' + _scaleStartValue + ' scaleSteps: ' + _scaleSteps + ' scaleStepWidth:' + _scaleStepWidth);


        switch(size)
        {
            case 'small':

                data = {
                    labels: dates,
                    datasets: [
                        {
                            label:'Growth',
                            scaleLineWidth: 1,
                            fillColor: gradient,
                            strokeColor: "rgba(138,186,35,1)",
                            data: growth
                        }
                    ]
                };

                options = {
                    scaleLineColor: "rgba(0,0,0,.1)",
                    scaleShowGridLines : false,
                    scaleGridLineColor : "rgba(0,0,0,.05)",
                    bezierCurve : true,
                    datasetStroke : true,
                    datasetStrokeWidth : 3,
                    datasetFill : true,
                    pointDot : false,
                    scaleFontSize: 10,
                    showTooltips: false,
                    scaleLabel: "<%=value%>%",
                    scaleShowLabels:true,
                    showXLabels:15,
                    scaleStartValue:0,
                    animation: false,
                    scaleBeginAtZero: false,
                    maintainAspectRatio: false,
                    response:true
                };
                len = dates.length;
                for(i=0;i< len;i++)
                {
                    dates[i] = '';
                }



                break;

            default:


                data = {
                    labels: dates,
                    datasets: [
                        {
                            scaleLineWidth: 1,
                            label: "Growth %",
                            fillColor: gradient2,
                            strokeColor: "rgba(138,186,35,1)",
                            pointColor: "rgba(138,186,35,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(151,187,205,1)",
                            data: growth
                        }
                    ]
                };

                var options = {
/*
                    scaleOverride: false,
                    scaleStartValue: _scaleStartValue,
                    scaleSteps:_scaleSteps, //10
                    // Number - The value jump in the hard coded scale
                    scaleStepWidth: _scaleStepWidth, //15
*/

                    scaleLineColor: "rgba(0,0,0,.1)",
                    scaleBeginAtZero: false,
                    scaleShowGridLines : true,
                    scaleGridLineColor : "rgba(0,0,0,.05)",
                    scaleGridLineWidth : 1,
                    showScale:true,
                    maintainAspectRatio: false,

                    bezierCurve : true,
                    datasetStroke : true,
                    datasetStrokeWidth : 5,
                    datasetFill : true,
                    responsive: true,
                    pointDot : true,
                    showXLabels:15,
                    scaleLabel: "<%=value%>%",
                    scaleFontSize: 10,
                    animation: false,
                    pointHitDetectionRadius : 10,
                    tooltipTemplate: "<%if (label){%><%=label%> - Growth: <%}%><%= value %>%"

                };


                break;
        }





        var chart = new Chart(ctx).Line(data, options);

    });
}



/** ================================
 *  SHOW FACEBOOK LIKE ON FRONT PAGE
 *  ================================
 */
function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
jQuery(document).ready(function($){


    $.getJSON('http://graph.facebook.com/freeforexsignal/', function(data) {
        var likes = data.likes +1;
        var _likes = likes - 2000;
        var i = 10;
        var x = setInterval(function(){
            if(likes - _likes < 15) i = 1;

            if(likes !== _likes) {
                $('#fb-likes-count').text(addCommas(_likes)); // span#num

            }
            if(_likes >= likes ) {
                clearInterval(this);
                return;
            }
            _likes=_likes+i;


        },1);

    });
});