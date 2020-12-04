<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("rcx_build_object"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function rcx_build_object($config)
		{
			/*
				https://www.php.net/manual/en/function.is-string.php
			*/
			if(is_array($config) === false)
			{
				throw new Exception("Missing config. - [rcx_build_object]");

				return false;
			}
			else
			{
				if(empty($config) === true)
				{
					throw new Exception("Missing config. - [rcx_build_object]");

					return false;
				}
			}
			
			/*

				From Documentation:

				First initialize class with the maximum number of concurrent requests
				you want open at a time. All requests after this will be queued until
				one completes.

				See: https://github.com/marcushat/RollingCurlX/blob/master/README.md

			*/
			// Initialize Class with the max number of connections
			$RCX = new RollingCurlX($config["max_connections"]);

			// Set a timeout on all requests:
			$RCX->setTimeout($config["timeout_period"]); //in milliseconds

			// Set HTTP Headers
			$RCX->setHeaders(["User-Agent: " . $config["user_agent"]]);

			trigger_error("Max connections set to \"" . $config["max_connections"] . "\"", E_USER_NOTICE);
			trigger_error("Timeout period set to \"" . $config["timeout_period"] . "\"", E_USER_NOTICE);
			trigger_error("User-Agent set to \"" . $config["user_agent"] . "\"", E_USER_NOTICE);

			/*
				Return object.
			*/
			return $RCX;
		}
	}

?>