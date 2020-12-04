<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("rcx_execute"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function rcx_execute($rcx, $config_received, $config_storage)
		{

			/*
				Final Checks.
			*/

			/*
				Check to see if the needed storage directories exist.
				If they do not exist, then attempt to create them.

				https://www.php.net/manual/en/function.file-exists.php
			*/
			if(file_exists($config_received["directory"]) === false)
			{
				trigger_error("The directory \"" . $config_received["directory"] . "\" does not appear to exist. Attempting to create it.", E_USER_WARNING);
				if(mkdir($config_received["directory"], 0755, true) === true)
				{
					trigger_error("Successfully created the directory \"" . $config_received["directory"] . "\.", E_USER_NOTICE);
				}
				else
				{
					throw new Exception("Could not create the directory \"" . $config_received["directory"] . "\".");
					return false;
				}
			}
			else
			{
				if(is_readable($config_received["directory"]) === false)
				{
					throw new Exception("The directory \"" . $config_received["directory"] . "\" does not appear to be readable. Please check the directory permissions.");
					return false;
				}
				else
				{
					if(is_writable($config_received["directory"]) === false)
					{
						throw new Exception("The directory \"" . $config_received["directory"] . "\" does not appear to be writeable. Please check the directory permissions.");
						return false;
					}
				}
			}
			if(file_exists($config_storage["directory"]) === false)
			{
				trigger_error("The directory \"" . $config_storage["directory"] . "\" does not appear to exist. Attempting to create it.", E_USER_WARNING);
				if(mkdir($config_storage["directory"], 0755, true) === true)
				{
					trigger_error("Successfully created the directory \"" . $config_storage["directory"] . "\.", E_USER_NOTICE);
				}
				else
				{
					throw new Exception("Could not create the directory \"" . $config_storage["directory"] . "\".");
					return false;
				}
			}
			else
			{
				if(is_readable($config_storage["directory"]) === false)
				{
					throw new Exception("The directory \"" . $config_storage["directory"] . "\" does not appear to be readable. Please check the directory permissions.");
					return false;
				}
				else
				{
					if(is_writable($config_storage["directory"]) === false)
					{
						throw new Exception("The directory \"" . $config_storage["directory"] . "\" does not appear to be writeable. Please check the directory permissions.");
						return false;
					}
				}
			}

			/*
				Start the process.
			*/
			trigger_error("Executing Calls to Amazon S3.", E_USER_NOTICE);

			/*
				Start Timer (Count how long it takes to execute and complete all REST calls).
			*/
			$rest_exec_timer = microtime(true);

			/*
				Begin execution.
			*/
			$rcx->execute();

			/*
				Stop Timer (Count how long it takes to execute and complete all REST calls).
			*/
			$rest_exec_timer = microtime(true)-$rest_exec_timer;

			trigger_error("Execution Time: $rest_exec_timer seconds.", E_USER_NOTICE);

			return true;
		}
	}

?>