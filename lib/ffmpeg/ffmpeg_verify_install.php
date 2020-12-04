<?php

	/*
		This function is used to determine if ffmpeg is installed and readily accessible
		via PATH.

		If ffmpeg is found, then it will return "true". If it is not found, then it will
		return false.

		https://stackoverflow.com/a/55276747
	*/

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("ffmpeg_verify_install"))
	{
		function ffmpeg_verify_install($path) :bool
		{
			/*
				Command to check for.
			*/
			if(empty($path))
			{
				$command = "ffmpeg";
			}
			else
			{
				$command = $path;
			}

			/*
				Determine if the environment is Windows or not.

				The function "strpos" will return "1" if it is Windows - allowing the result to be utilized as if it was a boolean.
				https://www.php.net/manual/en/function.strpos.php
			*/
			$os_check = strpos(PHP_OS, 'WIN') === 0;

			/*
				Convert the result of the above check into a proper boolean from an integer.
				https://www.php.net/manual/en/function.boolval.php
			*/
			$windows = boolval($os_check);

			/*
				Evaluate to determine how to check for the availability of ffmpeg.
				If the environment is Windows, then utilize the command "where".
				If the envrionment is Linux or macOS, then utilize the command "command -v"
			*/
			$test = $windows ? 'where' : 'command -v';

			/*
				Test for the path and confirm whether or not the result is executable.

				https://www.php.net/manual/en/function.escapeshellcmd.php
				https://www.php.net/manual/en/function.shell-exec.php
				https://www.php.net/manual/en/function.is-executable.php
			*/
			$result = is_executable(trim(shell_exec(escapeshellcmd("$test $command"))));

			/*
				Return the result to the client.
				The value will be either true or false.

				https://www.php.net/manual/en/function.return.php
			*/
			return $result;
		}
	}

?>
