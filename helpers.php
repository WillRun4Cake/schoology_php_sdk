<?php
	/* PHP Helper functions */
	function convertToCamelCase(String $string,String $delimiter = '_',$capitalizeFirstCharacter = false) {
		$str = str_replace(' ','',ucwords(str_replace($delimiter, ' ', $string)));

		if (!$capitalizeFirstCharacter) {
			$str[0] = strtolower($str[0]);
		}

		return $str;
	}

	function removeCommentText(String $str) {
		return preg_replace('~\([^)]*\)~', '', $str);
	}
?>