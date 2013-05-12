<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Log files presenter</title>

        <script type="text/javascript" src="js/jquery-1.9.1.min.js" ></script>
        <script type="text/javascript" src="js/highcharts.js" ></script>
        <script type="text/javascript" src="js/gray.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/prototype.js"></script>
        <script type="text/javascript" src="js/calendarview.js"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="css/calendarview.css">
        <style>

            div.calendar {
                max-width: 240px;
                margin-left: auto;
                margin-right: auto;
            }
            div.calendar table {
                width: 100%;
            }
            div.dateField {
                width: 140px;
                padding: 6px;
                -webkit-border-radius: 6px;
                -moz-border-radius: 6px;
                color: #555;
                background-color: white;
                margin-left: auto;
                margin-right: auto;
                text-align: center;
            }

        </style>

        <script type="text/javascript">


            function getURLParameter(name) {
                return decodeURI(
                (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]
            );
            }
//            function refreshstatistics(){
//                var from = document.getElementById("embeddedDateField1").innerHTML
//                var to = document.getElementById("embeddedDateField2").innerHTML
//                
//               
//                alert("from: "+from+"   to: "+to)
//            }

            function getid() {
                return getURLParameter("id");
            }
            function getFrom() {
                if (document.getElementById("embeddedDateField1").innerHTML == "Select Date")
                            return "current";
                        else
                            return document.getElementById("embeddedDateField1").innerHTML;
            }
            function getTO() {
               if (document.getElementById("embeddedDateField2").innerHTML == "Select Date")
                            return "current";
                        else
                            return document.getElementById("embeddedDateField2").innerHTML;
            }
            
            function setupCalendars() {
                // Embedded Calendar1
                Calendar.setup(
                {
                    dateField: 'embeddedDateField1',
                    parentElement: 'embeddedCalendar1'
                }
            )
                // Embedded Calendar2
                Calendar.setup(
                {
                    dateField: 'embeddedDateField2',
                    parentElement: 'embeddedCalendar2'
                }
            )


            }
            
            function getStatistics(){
//                DebugLine
//                alert("from: "+getFrom()+"   to: "+getTO())
                var options = {
                    chart: {
                        renderTo: 'container',
                        defaultSeriesType: 'line',
                        marginRight: 130,
                        marginBottom: 25
                    },
                    title: {
                        text: 'Seeding history',
                        x: -20 //center
                    },
                    subtitle: {
                        text: '',
                        x: -20
                    },
                    xAxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    yAxis: {
                        title: {
                            text: 'Seeders'
                        },
                        plotLines: [{
                                value: 0,
                                width: 1,
                                color: '#808080'
                            }]
                    },
                    tooltip: {
                        valueSuffix: 'seeders'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -10,
                        y: 100,
                        borderWidth: 0
                    },
                    series: [{
                            name: 'Threshold',                           
                            color: 'red'
                        }, {
                            name: 'file'
                        }]
                }
                // Load data asynchronously using jQuery. On success, add the data
                // to the options and initiate the chart.
                // This data is obtained by exporting a GA custom report to TSV.
                // http://api.jquery.com/jQuery.get/
                jQuery.get('getData.php?id='+getid()+"&from="+getFrom()+"&to="+getTO(), null, function(tsv) {
                    var lines = [];
                    var threshold = [];
                    var  seeders = [];
                    var   dates= [];
                    try {
                        // split the data return into lines and parse them
                      
                        tsv = tsv.split("=#=#=");
                          
                        jQuery.each(tsv, function(i, line) {
                           
                            if(line.length == 0)return;
                            line = line.split(/\t/);
                            
                            
                            //                            DebugLine
                            //                            alert(line[0]+" ==== "+line[2]+"===="+line[3])
                            

                            //                            date = Date.parse(line[0] +' UTC');
                            dates.push([line[0]]);
                            threshold.push([parseInt(line[2])]);
                            seeders.push([parseInt(line[3])]);
                            
                        });
                    } catch (e) {  }
                    options.xAxis.categories = dates;
                    options.series[0].data = threshold;
                    options.series[1].data = seeders;
                    chart = new Highcharts.Chart(options);
                });
            }
        </script>
        <script type="text/javascript">
            var chart;
            //            $(document).ready(function() {
            Event.observe(window, 'load', function() {
                
                setupCalendars() 
                getStatistics()
            });
        </script>

    </head>
    <body >
        <br>
        <h1 align="center" class="muted">Welcome to the statistics page.</h1>
        <br></br>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span3" id="leftmenu">

                   


                        <table>
                            <caption><h4>Select time range</h4></caption>
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div id="embeddedCalendar1" style="margin-left: auto; margin-right: auto">
                                        </div>
                                        <br />
                                        <div id="embeddedDateField1" class="dateField">Select Date</div>
                                    </td>
                                    <td>
                                        <div id="embeddedCalendar2" style="margin-left: auto; margin-right: auto">
                                        </div>
                                        <br />
                                        <div id="embeddedDateField2" class="dateField">Select Date</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                     
                        <p align="center"> <button class="btn btn-primary" type="button"  onclick="getStatistics();">Refresh statistics</button></p>

             


                </div>
                <div class="span9" id="mainoutput"><div id="container" style="width: 100%; height: 400px; margin: 0 auto"></div></div>
            </div>
        </div>

    </body>
</html>
