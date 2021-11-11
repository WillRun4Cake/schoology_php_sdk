<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});

      var chart1 = {data: [
          ['Role','Student','Teacher','School Staff','School Counselor','Educational Technologist','Above School-Level Staff','Teacher/Webmaster','School Administrator','LMS System Administrator','Instructional Designer'],
          ['2017',50,80,40,2,10,11,6,4,3,10],
          ['2018',55,65,30,5,15,15,4,22,6,25],
          ['2019',62,45,20,11,20,17,12,3,9,20],
          ['2020',68,60,25,1,12,19,61,5,3,30],
          ['2021',75,95,45,5,8,25,8,2,3,40],
        ],
        title: 'DoDEA Roles by Quantity',
        elementId: 'chart1',
        chartType: 'line',
        options: {
          width: 700,
          height: 500
        }
      };

      var chart2 = {data: [
          ['Role','Student','Teacher','School Staff','School Counselor','Educational Technologist','Above School-Level Staff','Teacher/Webmaster','School Administrator','LMS System Administrator','Instructional Designer'],
          ['2017',50,80,40,2,10,11,6,4,3,10],
          ['2018',55,65,30,5,15,15,4,22,6,25],
          ['2019',62,45,20,11,20,17,12,3,9,20],
          ['2020',68,60,25,1,12,19,61,5,3,30],
          ['2021',75,95,45,5,8,25,8,2,3,40],
        ],
        title: 'DoDEA Hoops by Quantity',
        elementId: 'chart2',
        chartType: 'line',
        options: {
          width: 700,
          height: 500
        }
      };

      var chart3 = {data: [
          ['Role', 'Quantity'],
          ['Student', 30000],
          ['Teacher', 18000],
          ['School Staff', 4000],
          ['School Administrator', 2000],
          ['Webmaster', 3500]
        ],
        title: 'DoDEA Usage by Role',
        elementId: 'chart3',
        chartType: 'pie',
        options: {
          width: 700,
          height: 500
        }
      };

      var charts = [];
      charts.push(chart1);
      charts.push(chart2);
      charts.push(chart3);

      google.charts.setOnLoadCallback(chartLoad);

      function chartLoad () {
        for (var i in charts) {
          google.charts.setOnLoadCallback(drawChart(charts[i]));
        }
      }

      function drawChart (chart) {
        if (chart.options == undefined) {
          options = null;
        }
        if (!chart.data) {
          throw new Error('No data provided to drawChart()');
        }
        if (!chart.title) {
          throw new Error('A title must be provided to drawChart()');
        }
        if (typeof chart.elementId !== 'string') {
          throw new Error('No element Id provided to drawChart()');
        }
        if (typeof chart.chartType !== 'string') {
          throw new Error('No chart type provided to drawChart()');
        }

        var data = new google.visualization.arrayToDataTable(chart.data);
        var title = chart.title;
        var elementId = chart.elementId;
        var chartType = chart.chartType;
        var options = chart.options;

        if (options && (options.hasOwnProperty('width') || options.hasOwnProperty('height'))) {
          if (options && (options.hasOwnProperty('width') && options.hasOwnProperty('height'))) {
           var options = {
            'title': title,
            'width': options.width,
            'height': options.height,
           };
          } else {
            throw new Error('A width property and a height property must be provided.');
          }
        } else {
          var options = {
            'title': title
          };
        }

        switch (chartType) {
          case ('pie'):
            var chartx = new google.visualization.PieChart(document.getElementById(elementId));
            break;
          case ('line'):
            var chartx = new google.visualization.LineChart(document.getElementById(elementId));
            break;
          default:
            throw new Error('No chart type provided.');
        }
        chartx.draw(data, options);
      }
  </script>
  <style>
    .container,h3 { max-width: 1900px; display: table; margin:  auto; }
    .chart { display: inline-block; }
    .caption { display:  inline-block; }
    h3 { margin-top: 110px; margin-bottom: 40px; }
  </style>
</head>
<body>
  <h3>Example Dashboard.</h3>
  <div class="container">
      <span class="chart" id="chart1"></span>
      <span class="chart" id="chart2"></span>
      <span class="chart" id="chart3"></span>
  </div>
</body>
</html>