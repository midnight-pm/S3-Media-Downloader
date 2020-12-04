<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("build_create_affiliate_tags"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function rcx_build_request($rcx, $config_rcx, $config_aws, $array)
		{
			if(is_object($rcx) === false)
			{
				throw new Exception("Mising RCX object. - [rcx_build_request]");

				return false;
			}
			if(is_array($config_rcx) === false)
			{
				throw new Exception("Missing RCX config. - [rcx_build_request]");

				return false;
			}
			if(is_array($config_aws) === false)
			{
				throw new Exception("Missing AWS config. - [rcx_build_request]");

				return false;
			}
			if(is_array($array) === false)
			{
				throw new Exception("Missing input. - [rcx_build_request]");

				return false;
			}

			/*
				Loop through the input array, supplying each to the RCX function for processing.
			*/
			foreach($array as $file_details)
			{
				/*
					Build Request JSON Object and Parameters
				*/
				$url = "https://" . $config_aws["s3_bucket_name"] . ".s3.amazonaws.com/" . $file_details["filename"];
				$headers = ["User-Agent: " . $config_rcx["user_agent"]];
				$post_data = [];
				$user_data = ["filename" => $file_details["filename"], "size" => $file_details["size"], "date" => $file_details["date"], "time" => $file_details["time"]];
				$options = [CURLOPT_FOLLOWLOCATION => false, CURLOPT_HEADER => true];

				/*
					Add Request
				*/
				$rcx->addRequest($url, $post_data, 'rcx_callback_function', $user_data, $options, $headers);
			}

			/*
				Return true when complete.
			*/
			return true;
		}
	}

?>