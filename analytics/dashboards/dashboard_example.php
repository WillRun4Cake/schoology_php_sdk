<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);
      google.charts.setOnLoadCallback(drawChart2);
      google.charts.setOnLoadCallback(drawChart3);

      function drawChart1() {
/********************   Line Chart    *************************************/
        var rolesArray = [
          ['Role','Student','Teacher','SchoolStaff','SchoolCounselor','EducationalTechnologist','AboveSchoolLevelStaff','Teacher/Webmaster','SchoolAdministrator','LMSSystemAdministrator','InstructionalDesigner'],
          ['2017',50,80,40,2,10,11,6,4,3,10],
          ['2018',55,65,30,5,15,15,4,22,6,25],
          ['2019',62,45,20,11,20,17,12,3,9,20],
          ['2020',68,60,25,1,12,19,61,5,3,30],
          ['2021',75,95,45,5,8,25,8,2,3,40],
        ];

        var data1 = new google.visualization.arrayToDataTable(rolesArray);
        var view1 = new google.visualization.DataView(data1);
        var options1 = {
          'title': 'DoDEA Roles by Quantity',
          'curveType': 'function',
          'width': 700,
          'height': 500,
          'legend': { 'position': 'bothist' }
        };
//       var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
       var chart1 = new google.visualization.LineChart(document.getElementById('line_chart1'));

/*     If we only want some of the columns to be displayed, we can pass the indices of those columns.
       view.setColumns(Uindices);   */

/*    Now, draw the chart.     */
       chart1.draw(view1, options1);
      }

/***********************  Chart 2   ****************************************/
      function drawChart2 () {
        var coursesArray = [
          ['Role','Student','Teacher','SchoolStaff','SchoolCounselor','EducationalTechnologist','AboveSchoolLevelStaff','Teacher/Webmaster','SchoolAdministrator','LMSSystemAdministrator','InstructionalDesigner'],
          ['2017',50,80,40,2,10,11,6,4,3,10],
          ['2018',55,65,30,5,15,15,4,22,6,25],
          ['2019',62,45,20,11,20,17,12,3,9,20],
          ['2020',68,60,25,1,12,19,61,5,3,30],
          ['2021',75,95,45,5,8,25,8,2,3,40],
        ];

        var data2 = new google.visualization.arrayToDataTable(coursesArray);
        var view2 = new google.visualization.DataView(data2);
        var options2 = {
          'title': 'DoDEA Roles by Quantity',
          'curveType': 'function',
          'width': 700,
          'height': 500,
          'legend': { 'position': 'bothist' }
        };
//       var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
       var chart2 = new google.visualization.LineChart(document.getElementById('line_chart2'));

/*     If we only want some of the columns to be displayed, we can pass the indices of those columns.
       view.setColumns(Uindices);   */

/*    Now, draw the chart.     */
       chart2.draw(view2, options2);
     }

/***********************  Chart 3   ****************************************/
      function drawChart3 () {
        var dataArray = [
          ['Role', 'Quantity'],
          ['Student', 30000],
          ['Teacher', 18000],
          ['School Staff', 4000],
          ['School Administrator', 2000],
          ['Webmaster', 3500]
        ];

        var data = new google.visualization.arrayToDataTable(dataArray);

        var options = {
          'title': 'DoDEA Usage by Role',
          'width': 700,
          'height': 700,
          'caption': 'Text goes here.'
        };

        var chart = new google.visualization.PieChart(document.getElementById('pie_chart1'));

        chart.draw(data, options);
      }
  </script>
  <style>
    .container,h3 { width: 1800px; display: table; margin:  auto; }
    .chart { display: inline-block; }
    .caption { display:  inline-block; }
  </style>
</head>
<body>
  <h3>Example Dashboard.</h3>
  <div class="container">
    <div class="table-row">
      <span class="chart" id="line_chart1"></span>
      <span class="chart" id="line_chart2"></span>
      <span class="chart" id="pie_chart1"></span>
    </div>
    <div class="table-row">
      <div class="chart table-cell" id="line_chartxxx"></div>
      <div class="chart table-cell" id="line_chartxxx"></div>
    </div>
  </div>
</body>
</html>