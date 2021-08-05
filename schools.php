<?php
require_once(dirname(__FILE__).'/SchoologyApi.class.php');
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
/*	All Schools    */
$schreq = 'schools';  // 'schools/{id}'
echo "<br/>Fetching All Schools:<br/>";
$schoolsResult = $myInfo->genericRequest($schreq);
echo '<br/>All Schools:<br/><pre><code>';
print_r($schoolsResult);
echo "</code></pre>";
/*	All buildings   */
$campuses = "schools/15433069/buildings";
echo "<br/>Fetching All Buldings/Campuses '$campuses':<br/>";
$campusesResult = $myInfo->genericRequest($campuses);
echo '<br/>All buildings for 15433069:<br/><pre><code>';
print_r($campusesResult);
echo '</code></pre><br/>';
foreach ($campusesResult->building as $key => $building)
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