<?php
require_once(dirname(__FILE__).'/SchoologyApi.class.php');
// JWarner@swingtech.com
Class Request
{
	function __construct()
	{
		$this->_token_key = '3031d64a01bc262ee9504ed29c6e1eee0610be9c8';
		$this->_token_secret = 'd9826b46c3f46d4aa3e83373b570ba59';
		$this->_two_legged = true;
		$this->schoology = null;
		$this->uid = null;
	}

	public function requestAuthHeader()
	{
		//xxxxx
	}
	public function requestSecretKeys ()
	{
		if (isset($_SESSION) && isset($_SESSION['user_id']))
		{
			$uid = $_SESSION['user_id'];
		}
		else
		{
			$uid = 0;
		}

		// Use hard-coded access tokens
		$this->_token_key = '3031d64a01bc262ee9504ed29c6e1eee0610be9c8';
		$this->_token_secret = 'd9826b46c3f46d4aa3e83373b570ba59';
		return true;
	}

	public function requestAccessTokens()
	{
		if (!empty($this->_token_key) && !empty($this->_token_secret))
		{
			try 
			{
				$this->apiResult('users/me');
			}
			catch (Exception $err)
			{
				if ($err->getCode() == 401)
				{
					unset($this->_token_key, $this->_token_secret);
					echo "<p style='color: red;'>Your access token key/secret did not work.";
					return false;
				}
			}
			//$this->schoology = new SchoologyApi(SCHOOLOGY_CONSUMER_KEY, SCHOOLOGY_CONSUMER_SECRET, '', '','', TRUE); 
		}
	}

	public function assignAdditionalSchoolsBulk($schools, $users)
	{
		if (empty($schools))
		{
			throw new Exception('You must input a comma-delimited string of school id\'s.');
		}
		$usersArray = explode(',', $users);
		$userChildObjs = array();
		foreach ($usersArray as $user)
		{
			$tmpUser = new stdClass();
			$tmpUser->id = $user;
			$tmpUser->additional_buildings = $schools;
			array_push($userChildObjs, $tmpUser);
			unset($tmpUser);
		}
		$usersObj = new stdClass();
		$usersObj->users = new stdClass();
		$usersObj->users->user = $userChildObjs;
		$url = 'users';
/*		print_r(json_encode((array) $usersObj, JSON_PRETTY_PRINT));
		return false;  */
		$this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);
		$response = $this->schoology->api($url, 'PUT', (array) $usersObj);
		if ($response)
		{
			if (isset($response->result) && isset($response->http_code) && (int) $response->http_code > 199 && (int) $response->http_code < 300)
			{
				if (isset($response->result->user))
				{
					return true;
				}
			}
			else
			{
				echo "<br/>Error with assigning additional buildings $schools to users: $users. Response: <br/><pre><code>";
				print_r(json_encode($response, JSON_PRETTY_PRINT));
				echo "</code></pre>";	
				throw new Exception("Error. Failed to assign additional buildings.");
			}
		}
		else
		{
			throw new Exception("Failed to bulk update schools. schools: $schools. users: $users");
		}
		return false;
	}

	public function assignPrimarySchoolBulk($school=null, $users)
	{
		if (!is_numeric($school))
		{
			throw new Exception("You can only enter one primary school id. It must be numeric.");
			return false;
		}
		$usersArray = explode(',', $users);
		$userChildObjs = array();
		foreach ($usersArray as $user)
		{
			$tmpUser = new stdClass();
			$tmpUser->id = $user;
			$tmpUser->building_id = $school;
			array_push($userChildObjs, $tmpUser);
			unset($tmpUser);
		}
		$usersObj = new stdClass();
		$usersObj->users = new stdClass();
		$usersObj->users->user = $userChildObjs;
		$url = 'users';
/*		print_r(json_encode((array) $usersObj, JSON_PRETTY_PRINT));
		return false;  */
		$this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);
		$response = $this->schoology->api($url, 'PUT', (array) $usersObj);
		if ($response)
		{
			if (isset($response->result) && isset($response->http_code) && (int) $response->http_code > 199 && (int) $response->http_code < 300)
			{
				if (isset($response->result->user))
				{
					return true;
				}
			}
			else
			{
				echo "<br/>Error with assigning additional buildings $school to users: $users. Response: <br/><pre><code>";
				print_r(json_encode($response, JSON_PRETTY_PRINT));
				echo "</code></pre>";	
				throw new Exception("Error. Failed to assign additional buildings.");
			}
		}
		else
		{
			throw new Exception("Failed to bulk update schools. schools: $school. users: $users");
		}
		return false;
	}

	public function genericRequest ($url, $json=false)
	{
		$this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);
		$response = $this->schoology->api($url);
		if ($response)
		{
			if ($response && isset($response->result))
			{
				if ($json && isset($response->raw_result))
					return $response->raw_result;
				else
					return $response->result;
			}
			else
			{
				return $response;
			}
		}
		else
		{
			throw new Exception("Failed to retrieve a valid Schoology internal user id.");
		}
	}

	public function getUserIdFromUniqueId ($uniqueId)
	{
		$this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);
		$response = $this->schoology->api("users/ext/$uniqueId");
		if ($response && isset($response->redirect_url) && !empty($response->redirect_url))
		{
			$idArray = array();
			$this->uid = preg_match('~[\d]+$~', $response->redirect_url, $idArray);
/*			echo "<pre><code>idArray matches:<br/>";
			print_r($idArray);
			return true;   */
			if (isset($idArray[0]) && is_numeric($idArray[0]))
			{
				$this->uid = $idArray[0];
//				throw new Exception("<br/>Fonud user id: ".$this->uid);
				return $this->uid;
			}
			else
			{
				throw new Exception("Failed to retrieve a valid Schoology internal user id.");
			}
		}
		else
		{
			throw new Exception("Failed to retrieve a valid Schoology internal user id.");
		}
		return false;
	}
}
$email = isset($_REQUEST['email']) ? filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) : null;
if (isset($_REQUEST['singleuser']) && $_REQUEST['singleuser'] === 'true' && $email)
{
	$readonly = $email ? 'readonly' : '';
	echo "<h4>User update page</h4><br/><br/><form method='POST' action='' id='begin'>
		User email: <input type='text' id='email' name='email' value='$email' placeholder='$email' ".$readonly." />
		<input type='hidden' id='singleuser' name='singleuser' value='true' /><br/>
		<input type='submit' value='Null' /><br/>
	</form>";
	if ($email)
	{
		echo "<form id='resetForm' action='' method='POST'>
				<input type='submit' id='reset' value='Reset' />
			  </form>";
	}
	echo "<pre><code>";
	print_r($_REQUEST);
	echo "</code><pre>";
	$api = new Request();
	$email = $_REQUEST['email'];
	try
	{
		$uidRequest = $api->getUserIdFromUniqueId($email);
	} catch (Exception $e)
	{
		echo "<br/>That user was not found.";
		exit();
	}
	if ($uidRequest)
	{
		echo "User id: $uidRequest<br/>";
/*		echo "<pre><code>";
		print_r(json_encode($uidRequest,JSON_PRETTY_PRINT));
		echo "</code></pre>";  */
	}
	else
	{
		echo "User api request failed.";
		exit();
	}
	/*	Make school update before populatidng the form fields.    */
	if (isset($_REQUEST['updateready']) && $_REQUEST['updateready'] === 'true' &&
		isset($_REQUEST['primary_school_string']) && 
		isset($_REQUEST['additional_schools_string'])
		)
	{
		$priSchool = $_REQUEST['primary_school_string'];
		$addSchools = $_REQUEST['additional_schools_string'];
		if(!preg_match('~[,[:blank:][:digit:]]+~', $priSchool) || !preg_match('~[,[:blank:][:digit:]]+~', $addSchools))
		{
			echo "<br/>The school codes entered must be comma-delimited numbers only.";
			exit();
		}
		$user = (string) $api->uid;
		$priSchoolUpdate = $api->assignPrimarySchoolBulk($priSchool, $user);
		$additionalSchoolUpdates = $api->assignAdditionalSchoolsBulk($addSchools, $user);
	}
	$userReq = "users/$uidRequest";
	$userInfo = $api->genericRequest($userReq);
/*	echo "<br/>User info for $email:<br/>";
	echo '<br/>Response:<br/><pre><code>';
	print_r($userInfo);
	echo '</code></pre><br/>';  */
	$userPrimaryBldg = null;
	$userAdditionalBldgs = null;
	if (isset($userInfo->building_id) && isset($userInfo->additional_buildings))
	{
		$userPrimaryBldg = $userInfo->building_id;
		$userAdditionalBldgs = $userInfo->additional_buildings;
	}
	echo "<form id='schoologyUpdateForm' action='' method='POST'>
<input type='hidden' id='useremail' name='email' value='$email' />
	Primary School: &nbsp;&nbsp;&nbsp;&nbsp;<input id='priBuilding' type='text' style='width: 200px;' name='primary_school_string' placeholder=''>
	Additional Schools: <input id='addBuildings' type='text' style='width: 200px;' name='additional_schools_string' placeholder=''><br/>
<input type='hidden' id='singleuser' name='singleuser' value='true' />
<input type='hidden' id='updateusers' name='updateready' value='true' />
	<input type='submit' value='Update' />
		  </form>
		  <script type='text/javascript'>
		  	var priB = document.getElementById('priBuilding');
		  	var addB = document.getElementById('addBuildings');
		  	priB.value = '$userPrimaryBldg';
		  	addB.value = '$userAdditionalBldgs';
		  </script>";

	if (isset($_REQUEST['updateready']) && $_REQUEST['updateready'] === 'true' &&
		isset($_REQUEST['primary_school_string']) && 
		isset($_REQUEST['additional_schools_string'])
		)
	{
/*		$priSchool = $_REQUEST['primary_school_string'];
		$addSchools = $_REQUEST['additional_schools_string'];
		if(!preg_match('~[,[:blank:][:digit:]]+~', $priSchool) || !preg_match('~[,[:blank:][:digit:]]+~', $addSchools))
		{
			echo "<br/>The school codes entered must be comma-delimited numbers only.";
			exit();
		}
		$user = (string) $api->uid;
		$priSchoolUpdate = $api->assignPrimarySchoolBulk($priSchool, $user);
		$additionalSchoolUpdates = $api->assignAdditionalSchoolsBulk($addSchools, $user);  */
		if ($priSchoolUpdate)
		{
			echo "<br/>Assigning primary school $priSchool to user: $user. Response: <pre><code>";
			print_r(json_encode($priSchoolUpdate, JSON_PRETTY_PRINT));
			echo "</code></pre>";
		}
		if ($additionalSchoolUpdates)
		{
			echo "<br/>Assigning additional schools $addSchools to user: $user. Response: <pre><code>";
			print_r(json_encode($additionalSchoolUpdates, JSON_PRETTY_PRINT));
			echo "</code></pre>";
		}
	}
	echo "<br/><br/><br/>User info for $email:<br/>";
	$userReq = "users/$uidRequest";
	$userData = $api->genericRequest($userReq);
	echo '<br/>Response:<br/><pre><code>';
	print_r($userData);
	echo '</code></pre><br/>';
	exit();
}
else
{
	echo '<h4>User update page</h4><br/><br/><form method="POST" action="" id="schoologyUpdateForm">
		User email: <input type="text" id="email" name="email" placeholder="adam@example.com" />
		<input type="hidden" id="singleuser" name="singleuser" value="true" />
		<input type="submit" value="Continue" />
	</form>';
	echo "<pre><code>";
	print_r($_REQUEST);
	exit();
}

exit('Done');


//***************
echo "<br/>REquesting ....";
$email = 'JWarner@swingtech.com';
//$req = 'users/109529517/sections';
$myInfo = new Request();
if (!$myInfo->getUserIdFromUniqueId($email))
{
	exit('Failed to retrieve a valid user id for this email: '.$email);
}
if (!$myInfo->uid || !is_numeric($myInfo->uid))
{
	exit("No valid user id");
}
if (isset($myInfo->uid) && $myInfo->uid)
{
	$uid = $myInfo->uid;
}
else
{
	exit("No valid user id");
}
$userReq = "users/$uid";
echo "<br/>User info for $email:<br/>";
$userInfo = $myInfo->genericRequest($userReq);
echo '<br/>Response:<br/><pre><code>';
print_r($userInfo);
echo '</code></pre><br/>';
$req = "users/".$myInfo->uid.'/sections';
echo "<br/>Request: $req, Fetching user data:<br/>";
$sections = $myInfo->genericRequest($req);
echo '<br/>Response:<br/><pre><code>';
//print_r($sections);
echo '</code></pre><br/>';
$sch = "schools/15433069/buildings";
echo "<br/>Fetching Schools info '$sch':<br/>";
$schools = $myInfo->genericRequest($sch);
echo '<br/>All schools for 15433069:<br/><pre><code>';
/*print_r($schools);
exit();    */
foreach ($schools->building as $key => $building)
{
	$bldg_code = $building->building_code ? $building->building_code : 'none';
	echo "<br/>".$building->title.", Internal id: ".$building->id.', Building code: '.$bldg_code;
}
echo "<br/><br/>Users Obj:<br/>";
/*	Bulk update schools   */
//$schools = '102709161,109029373,116302935';
$schools = '102493601';
$users = '10101,20202,30303';
$users = (string) $myInfo->uid;
$schoolUpdates = $myInfo->assignAdditionalSchoolsBulk($schools, $users);
if ($schoolUpdates)
{
	echo "<br/>Assigning additional buildings $schools to users: $users. Response: <br/><pre><code>";
	print_r(json_encode($schoolUpdates, JSON_PRETTY_PRINT));
	echo "</code></pre>";
}
echo "Finished....";