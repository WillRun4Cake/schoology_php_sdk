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
$req = "users/".$myInfo->uid.'/sections';
echo "<br/>Request: $req, Fetching user data:<br/>";
$sections = $myInfo->genericRequest($req);
echo '<br/>Response:<br/><pre><code>';
print_r($sections);
echo '</code></pre><br/>';
echo "Finished....";