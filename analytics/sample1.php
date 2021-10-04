<!DOCTYPE html>
<html>
<head>
	<style>
		p,h1,h2,h3,h4,h5,div,pre,code {
			display:  table;
			margin: auto;
		}
		h1,h2 {
			margin-top: 65px;
		}
	</style>
</head>
<body>
	<h2>Data Analytics sample analysis</h2>
	<p>Date Range: ....</p>
	<?php
		set_time_limit(400);
		require_once '../helpers.php';
		$fname = 'analytics.csv';
		echo "<p style='color: darkgreen;'>....Working.....</p>";
		echo "<br/>Before loading file. PHP Memory usage: ".number_format(memory_get_usage(),0,'.',',').' bytes';
		$rawStr = file_get_contents('./raw/'.$fname);
		if ($rawStr === false) {
			print('<br/><p style="color: red;">Error: Input File could not be read ('.$fname.').</p>');
			exit();
		}
		echo "<br/>After loading file. PHP Memory usage: ".number_format(memory_get_usage(),0,'.',',').' bytes';
		/*	Connect to database */
		$conn = new PDO('mysql:host=localhost;dbname=schoology', 'santa', 'hG8(*3te8@-)&et#uC8%3Et*7e');

		$rawArray = explode("\n",$rawStr);
		$headers = explode(',',$rawArray[0]);
		$columns = array();
		foreach ($headers as $i => $header) {
			$header = convertToCamelCase($header,'_');
			$columns[$i] = removeCommentText($header);
		}
		unset($rawStr);
		echo "<p>CSV Headers:<br/><pre><code>Raw Headers:<br/>";
//		print_r($headers);
		echo "<br/>Columns (without comments):<br/>";
//		print_r($columns);
		echo "</code></pre>";

		echo "<br/>After creating arrays. PHP Memory usage: ".number_format(memory_get_usage(),0,'.',',').' bytes';

		Class dataPoint {
			public function __construct() {
				global $columns;
				foreach ($columns as $k => $val) {
					$this->{$val} = null;
				}
			}
		}

		$sample1 = new dataPoint();
		echo "<p>New data point object:<br/><pre><code>";
		print_r($sample1);
		echo "</code></pre>";

		echo "<p>Raw data file contains ".count($rawArray)." lines.</p><br/>";

		echo "<br/>Before objs: PHP Memory usage: ".number_format(memory_get_usage(),0,'.',',').' bytes';
		$data = array();
		$query = "INSERT INTO `data` (
			`roleName`,
			`userBuildingId`,
			`userBuildingName`,
			`username`,
			`email`,
			`schoologyUserId`,
			`uniqueUserId`,
			`actionType`,
			`itemType`,
			`itemId`,
			`itemName`,
			`courseName`,
			`courseCode`,
			`sectionName`,
			`lastEventTimestamp`,
			`eventCount`,
			`roleId`,
			`userBuildingCode`,
			`lastName`,
			`firstName`,
			`deviceType`,
			`itemBuildingId`,
			`itemBuildingName`,
			`itemBuildingCode`,
			`itemParentType`,
			`groupId`,
			`groupName`,
			`courseId`,
			`sectionId`,
			`sectionSchoolCode`,
			`sectionCode`,
			`month`,
			`date`,
			`timestamp`,
			`timeSpent`
		) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$j = 0;
		/*	Limit to 5 million inserts   */
		for ($i=1; $i < min(5000000,count($rawArray) - 1); $i++) {
//			echo '<br/>i: '.$i;
			$sample = new dataPoint();
			$dataEach = str_getcsv($rawArray[$i]);
			$stmt=$conn->prepare($query);
			foreach ($dataEach as $q => &$r) {
				if ($r === '') {
					$r = null;
				}
			}
			$params = $dataEach;

/*		Troubleshooting

			echo "<br/>Data:<br/><pre><code>";
			print_r($params);
			echo "</code></pre><br/><br/>"; */

			if (!$stmt->execute($params)) {
				echo "<br/><p style='color: red;'>Error: could not insert: ".$i.' '.$dataEach[7].' '.$dataEach[8].'<br/>Error info: ';
				echo "<br/><pre stlye='display: inline-block'><code style='color: red;'>";
				print_r($stmt->errorInfo());
				echo "<br/>Data:<br/>";
				print_r($dataEach);
				echo "</code></pre></p><br/><br/>";
				unset($sample);
				unset($rawArray[$i]);
				unset($params);
				unset($stmt);
				continue;
			}
			$stat = $stmt->errorInfo();
			if (is_numeric($stat[0])) {
				$j++;
			}

			unset($sample);
			unset($rawArray[$i]);
			unset($params);
			unset($stmt);
		}
		echo "<p>Inserted ".number_format($j,0,'.',',')." records.</p>";
		echo "<br/>200 Objects, PHP Memory usage: ".number_format(memory_get_usage(),0,'.',',').' bytes';
		$query = 'SELECT COUNT(*) AS count FROM data';
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_OBJ);
		echo "<br/>Data table count:<br/><pre><code>";
		while ($result = $stmt->fetch()) {
			echo $result->count;
		}
		echo "</code></pre>";
		$query = 'SHOW TABLES';
		$stmt = $conn->prepare($query);
		$stmt->execute();
		echo "<br/>All tables:<br/><pre><code>";
		while ($result = $stmt->fetch()) {
			print_r($result);		
		}
		echo "</code></pre>";
		echo "<p>Last Data point:<br/><pre><code>";
		print_r(end($dataEach));
		echo "</code></pre>";
	?>
</body>
</html>