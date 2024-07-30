<?php
require(dirname(__FILE__).'/preload/preload.php');
require_once(dirname(__FILE__).'/SchoologyApi.class.php');
require_once(dirname(__FILE__).'/Schoology.Request.Utils.php');
// JWarner@swingtech.com
//***************
echo "<h5>Requesting API data......</h5><p>&nbsp;</p>";
$email = 'JWarner@swingtech.com';

//$req = 'users/109529517/sections';


/*
$req = new Request();
$allRoles = $req->getAllRoles();

echo "<p>Listing all Roles:<br/><pre><code>";
print_r($allRoles);
echo "</code></pre></p>";
*/

$req = new Request();
$users = $req->getUsersByRole('925573',true);  // 925573 = Student Inactive role in Dev instance

if (is_array($users) && sizeof($users) > 0) {
  echo "<p>Listing all ".sizeof($users)."  Users with \"Student Inactive\" role:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     (All API queries limited to 200 users/results)</p><br/>";
  echo "<ul>";
  foreach ($users as $ud) {
    if (isset($ud->name_display) && isset($ud->uid)) {
      echo "<li>".$ud->name_display . '  (' . $ud->uid . ')';
    }
  }
  echo "</ul>";
} else {
  echo "<br/>No users found wih role: Student Inactive<br/>";
  exit();
}

echo "Inactivating the above listed users now.<br/>";

$usersToInactivate = array();

if (!is_array($users))
  exit("Users to inactive is not array. This must be an array to proceed.");

foreach ($users as $u) {
  $usersToInactivate[$u->uid] = $u->uid;
}

if (sizeof($usersToInactivate) < 1)
  exit("No users to inactivate.");

$req = new Request();
$deletedUsers = $req->inactivateUsers(implode(',', $usersToInactivate));

if (is_array($deletedUsers)) {
  if (sizeof($deletedUsers) > 0) {
    echo "<br/>Successfully inactivated these users: <br/><ul>";
    foreach ($deletedUsers as $du) {
      echo "<li>$du</li>";
    }
    echo "</ul>";
  }
}


/*

------------   Should these users also have their role changed from "Student Inactive" to "Student" ?? ---------


*/

exit("<p>Finished.....</p>");