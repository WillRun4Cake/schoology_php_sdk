<?php
//  echo '{"name": "Sammy", "hair": "brown", "age": 27}';
  $chart1 = [ 'data' => 
              [
                ['Role','Student','Teacher','School Staff','School Counselor','Educational Technologist','Above School-Level Staff','Teacher/Webmaster','School Administrator','LMS System Administrator','Instructional Designer'],
                ['2017',50,80,40,2,10,11,6,4,3,10],
                ['2018',55,65,30,5,15,15,4,22,6,25],
                ['2019',62,45,20,11,20,17,12,3,9,20],
                ['2020',68,60,25,1,12,19,61,5,3,30],
                ['2021',75,95,45,5,8,25,8,2,3,40]
              ],
              'title' => 'DoDEA Roles by Zebra Quantity',
              'elementId' => 'chart1',
              'chartType' => 'line',
              'options' => [
                              'width'   => 700,
                              'height'  => 500
                           ]
           ];

  $chart2 = [ 'data' => 
              [
                ['Role','Student','Teacher','School Staff','School Counselor','Educational Technologist','Above School-Level Staff','Teacher/Webmaster','School Administrator','LMS System Administrator','Instructional Designer'],
                ['2017',50,80,40,2,10,11,6,4,3,10],
                ['2018',55,65,30,5,15,15,4,22,6,25],
                ['2019',62,45,20,11,20,17,12,3,9,20],
                ['2020',68,60,25,1,12,19,61,5,3,30],
                ['2021',75,95,45,5,8,25,8,2,3,40],
              ],
              'title' => 'DoDEA Hoops by Ferret Quantity',
              'elementId' => 'chart2',
              'chartType' => 'line',
              'options' => [
                              'width'   => 700,
                              'height'  => 500
                           ]
           ];

  $chart3 = [ 'data' => 
              [
                ['Role', 'Quantity'],
                ['Student', 30000],
                ['Teacher', 18000],
                ['School Staff', 4000],
                ['School Administrator', 2000],
                ['Webmaster', 3500]
              ],
              'title' => 'DoDEA Usage by Dolphin Role',
              'elementId' => 'chart3',
              'chartType' => 'pie',
              'options' => [
                              'width'   => 700,
                              'height'  => 500
                           ]
           ];

  $charts = array();

  array_push($charts, $chart1);
  array_push($charts, $chart2);
  array_push($charts, $chart3);

  echo json_encode($charts);
?>
