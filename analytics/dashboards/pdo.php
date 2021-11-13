<?php
/*
 *
 *
 *
 *    Some of these queries may require the ONLY_FULL_GROUP_BY option to be removed from /etc/mysql/my.cnf
 *    E.g. redeclare sql_mode:
 *        sql_mode = "STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"
 *
 *
 *
 */
  header('Content-Type: application/json');
  $conn = new PDO('mysql:host=localhost;port=3306;dbname=schoology','santa','hG8(*3te8@-)&et#uC8%3Et*7e');
  $resultLimit = 4;
  $query = 'SHOW FULL TABLES IN schoology WHERE TABLE_TYPE LIKE "VIEW"';
  $stmt = $conn->prepare($query);
  $stmt->execute();

  $viewsArray = array();
  while($result = $stmt->fetch()) {
    array_push($viewsArray, $result[0]);
  }
  unset($result);

  $dataArrays = array();
  $chartArrays = array();
  $charts = array();
  $i = 1;
  foreach ($viewsArray as $view) {
    unset($stmt);
    $data = array();
    $view = filter_var($view, FILTER_SANITIZE_STRING);
/*  Database names & column names cannot be bound to a prepared statement,
    so simply use substitution in a text-based query.     */    
    $query = "SELECT * FROM $view LIMIT $resultLimit";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $j = 0;
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if ($j === 0) {
        $data[] = $result;
      }
      else {
        $data[] = array_values($result);
      }
      $j++;
    }
/*  Retrieve the column names from the first record, then change
    the record from associative to a numeric array to make JSON happy.    */
    $colArray = array();
    foreach (array_keys($data[0]) as $columnName) {
      $colArray[] = $columnName;
    }
/*  Make numeric.   */
    $data[0] = array_values($data[0]);
    $dataArrays[$view] = $data;
    $chartArrays[$view] = array();
    $chartArrays[$view]['data'][] = $colArray;
    foreach($data as $row) {
      $chartArrays[$view]['data'][] = $row;
    }
/*  Fetch chart meta data from database (if exists).    */
    $query = 'SELECT * FROM `chart` WHERE `view` = :view LIMIT 1';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':view', $view, PDO::PARAM_STR);
    $stmt->execute();
    if ($result = $stmt->fetch()) {
      if (isset($result['title'])) { $chartArrays[$view]['title'] = $result['title']; }
      if (isset($result['elementId']))
      {
        $chartArrays[$view]['elementId'] = $result['elementId'];
      }
      else
      {
        $chartArrays[$view]['elementId'] = 'chart'.$i;
      }
      if (isset($result['type'])) { $chartArrays[$view]['type'] = $result['type']; }
      $options = array();
      if (isset($result['width'])) {
        if ($width = $result['width'])
          $options['width'] = $width;
      }
      if (isset($result['height'])) {
        if ($height = $result['height'])
          $options['height'] = $height;
      }
      if (!empty($options))
        $chartArrays[$view]['options'] = $options;
    }
    else
    {
      $chartArrays[$view]['title'] = 'Generic Title';
      $chartArrays[$view]['elementId'] = 'chart'.$i;
      $chartArrays[$view]['type'] = 'pie';
      $options = array(
        'width' => 700,
        'height' => 500
      );
      if (!empty($options)) {
        $chartArrays[$view]['options'] = $options;
      }
    }
    array_push($charts, $chartArrays[$view]);
    $i++;
  }
  echo json_encode($charts, JSON_NUMERIC_CHECK);



/*
 *  Example static chart data.
 */
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
              'type' => 'line',
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
              'type' => 'line',
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
              'type' => 'pie',
              'options' => [
                              'width'   => 700,
                              'height'  => 500
                           ]
           ];
$comment = <<<'COMMENT'
  $charts = array();

  array_push($charts, $chart1);
  array_push($charts, $chart2);
  array_push($charts, $chart3);

  echo json_encode($charts);
COMMENT;
?>
