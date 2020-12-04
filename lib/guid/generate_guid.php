<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("generate_guid"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function generate_guid()
		{
			/*
				https://stackoverflow.com/a/26163679
			*/
			if (function_exists('com_create_guid') === true)
			{
				return trim(com_create_guid(), '{}');
			}

			return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		}
	}
?>
