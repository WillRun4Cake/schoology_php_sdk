<?php

Class Request
{
  function __construct()
  {
    if (!defined('TOKEN_KEY'))
      throw new Exception("API Token consumer key is not defined.");
    if (!defined('TOKEN_SECRET'))
      throw new Exception("API token consumer secret is not defined.");

    $this->_token_key = TOKEN_KEY;
    $this->_token_secret = TOKEN_SECRET;
    $this->_two_legged = true;
    $this->schoology = null;
    $this->uid = null;

    if (!file_exists(__DIR__.'/.env')) {
      $envTemplate = <<<'END'
SCHOOLOGY_API_BASE=
TOKEN_KEY=
TOKEN_SECRET=
END;

    file_put_contents('.env', $envTemplate);
    exec('chmod 660 .env');
    }

   /* 
    $this->_token_key = '3031d64a01bc262ee9504ed29c6e1eee0610be9c8';
    $this->_token_secret = 'd9826b46c3f46d4aa3e83373b570ba59';
    $this->_two_legged = true;
    $this->schoology = null;
    $this->uid = null;
  */
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
/*    print_r(json_encode((array) $usersObj, JSON_PRETTY_PRINT));
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
/*    print_r(json_encode((array) $usersObj, JSON_PRETTY_PRINT));
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

  public function genericGETRequest($url, $json=false, $verbose=false, $query_string='')
  {
    $this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);
    $response = $this->schoology->api($url,'GET',array(),array(),$verbose,$query_string);
    if ($response)
    {
      if ($response && isset($response->result))
      {
        if ($json && isset($response->raw_result)) {
          return $response->raw_result;
        }
        else {
          return $response->result;
        }
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
/*      echo "<pre><code>idArray matches:<br/>";
      print_r($idArray);
      return true;   */
      if (isset($idArray[0]) && is_numeric($idArray[0]))
      {
        $this->uid = $idArray[0];
//        throw new Exception("<br/>Fonud user id: ".$this->uid);
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


  public function getAllRoles($returnFullObject=false)
  {
    $this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);
    $response = $this->schoology->api("roles");
    if ($response)
    {
      $rolesArray = array();
      $this->roles = $response;  // ->other . fields . ?

      if (isset($response->result->role) && is_array($response->result->role)) {
        foreach ($response->result->role as $key => $role) {
          if ($returnFullObject) {
            if (isset($role->id) && isset($role->title)) {
              $rolesArray[$role->id] = $role;
            }
          } else {
            if (isset($role->id) && isset($role->title)) {
              $rolesArray[$role->id] = $role->title;
            }
          }
        }
      }

      return $rolesArray;
/*      
      echo "<br/>Response:<br/><pre><code>";
      print_r($response);
      echo "</code></pre>";
*/
      return null;
    }
    else
    {
      throw new Exception("Error: The request to get roles has failed.");
    }
    return false;
  }

/**
 * Returns all users with the provided role(s).
 * 
 * @param string $roleString A string of comma-delimited role id's to filter on, e.g., 82938,203820,74027,203893
 * @return array An array of the id's of all users with the given role(s).
 * 
**/
  public function getUsersByRole($roleString,$returnObject = false)
  {
    $roleString = str_ireplace(' ','',$roleString);
    $roleStringTest = preg_match('~^[0-9]{1,15}(,[0-9]{1,15}){0,}$~', $roleString);
    if (!$roleStringTest)
      throw new Exception("Error: Invalid argement passed to getUsersbyRole(). A comma-delimited string of role id's must be passed to getUsersByRole().");

    $this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);

    $response = $this->genericGETRequest("users",false,true,"role_ids=$roleString");

    if ($response)
    {
      $usersArray = array();

      if (is_string($response))
        $result = json_decode($response);
      elseif (is_object($response))
        $result = $response;
      else
        $result = $response;

      if (isset($result->user) && is_array($result->user))
      {
        if (!$returnObject) {
          foreach ($result->user as $index => $user) {
            $usersArray[$index] = isset($user->id) ? $user->id : $user->name_display;
          }
        } else {
          return $result->user;
        }
      }

      return $usersArray;
    }
    else
    {
      throw new Exception("Error: The request to get roles has failed.");
    }
    return false;
  }


/**
 * Inactivate the users provided.
 * 
 * @param string users A comma-delimited string of user id's to inactivate, e.g., 82938,203820,74027,203893
 * @return array An array of the id's of all users which were deactivated.
 * 
**/
  public function inactivateUsers($userString, $keepEnrollments = true)
  {
    $this->schoology = new SchoologyApi($this->_token_key, $this->_token_secret, '', '','', TRUE);

    $response = $this->schoology->_inactivateUsers($userString, $keepEnrollments);

    if ($response && is_array($response)) {
      return $response;
    } else {
      throw new Exception('Error: May have failed to inactivate some or all users: '.$userString);
    }

    return false;
  }

}  // End Request class

?>