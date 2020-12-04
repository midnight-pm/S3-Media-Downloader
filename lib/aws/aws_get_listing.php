<?php

	/*
		This function is used to obtain a listing from the specified Amazon S3 Bucket
	*/

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("aws_get_listing"))
	{
		function aws_get_listing($bucket, $aws_path)
		{
			/*
				Test Input.
			*/
			if(empty($bucket) === true)
			{
				throw new Exception("The name of the Amazon S3 bucket does not appear to have been provided. - [aws_get_listing]");
			}
			else
			{
				if(is_string($bucket) === false)
				{
					throw new Exception("Invalid input. - [aws_get_listing]");
				}
			}

			/*
				As the path to the aws client is optional, it will only be checked if the parameter is not empty.
				Note that the behavior of empty() differs from isset().
				isset() will return as true on a set variable, even if that variable is null.
				empty() will return as true on a set variable, even if it is null or is an empty string.

				https://www.php.net/manual/en/function.empty.php
			*/
			if(empty($aws_path) === false)
			{
				if(is_string($aws_path) === false)
				{
					throw new Exception("The input for the user-provided path to the aws client does not appear to be correct. - [aws_get_listing]");
				}
			}
			else
			{
				/*
					Default to "aws" and rely on system $PATH
				*/
				$aws_path = "aws";
			}

			$command = "$aws_path s3 ls $bucket --no-sign-request";

			trigger_error("Retrieving listing from AWS - [aws_get_listing]", E_USER_NOTICE);
			/*
				Test for the path and confirm whether or not the result is executable.

				https://www.php.net/manual/en/function.escapeshellcmd.php
				https://www.php.net/manual/en/function.shell-exec.php
				https://www.php.net/manual/en/function.is-executable.php
			*/
			$result = shell_exec(escapeshellcmd("$command"));
			// $result = file_get_contents("/data/2019-06.txt");
			// $result = file_get_contents("/data/2019-07.txt");
			// $result = file_get_contents("/data/2019-08.txt");

			/*
				Check to see if the result of "shell_exec" is null.
				The choice to do a type check for NULL is stylistic only.
				is_null is perfectly valid as well.

				https://www.php.net/manual/en/function.is-null.php
				https://www.php.net/manual/en/function.is-null.php#84161
			*/
			if($result === NULL)
			{
				throw new Exception("Returned no result from AWS - [aws_get_listing]");
			}
			else
			{
				trigger_error("Retrieved listing from AWS - [aws_get_listing]", E_USER_NOTICE);
				/*
					Return the result of the listing from AWS.

					https://www.php.net/manual/en/function.return.php
				*/
				return $result;
			}
		}
	}

?>
