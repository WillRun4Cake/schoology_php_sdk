<?php
/*  Load the Environment-specific variables/contstants.
    These are intended to be located one directory above the public web directory (www or public_html).    */
use PrepareEnv\DotEnv as DotEnv;

$filepath = __DIR__ . '/../.env';

if (file_exists($filepath)) {
  (new DotEnv($filepath))->load();
} else {
  throw new Exception("The .env evironment file could not be found. Please create this file in the home directory.");
}

/*
$required_constants = array('SCHOOLOGY_API_BASE');

foreach($required_constants as $cnst) {
  if (!defined($cnst)) {
    define($cnst, null);
  }
}  */
