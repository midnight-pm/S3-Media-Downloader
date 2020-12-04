<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("ffprobe_file_get_error"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function ffprobe_file_get_error($file_path, $ffprobe_path)
		{
			/*
				https://www.php.net/manual/en/function.is-string.php
			*/
			if(is_string($file_path) === false)
			{
				throw new Exception("Invalid input. - [ffprobe_file_get_error]");

				return false;
			}
			else
			{
				/*
					https://www.php.net/manual/en/function.empty.php
				*/
				if(empty($file_path) === true)
				{
					throw new Exception("Missing path to file. - [ffprobe_file_get_error]");

					return false;
				}
			}

			/*
				Verify that the file is readable.
				https://www.php.net/manual/en/function.is-readable.php
			*/
			if(is_readable($file_path) === false)
			{
				throw new Exception("This process does not have permissions to read the file \"$file_path\".");

				return false;
			}

			/*
				As the path to ffprobe is optional, it will only be checked if the parameter is not empty.
				Note that the behavior of empty() differs from isset().
				isset() will return as true on a set variable, even if that variable is null.
				empty() will return as true on a set variable, even if it is null or is an empty string.

				https://www.php.net/manual/en/function.empty.php
			*/
			if(empty($ffprobe_path) === false)
			{
				if(is_string($ffprobe_path) === false)
				{
					throw new Exception("The input for the user-provided path to ffprobe does not appear to be correct. - [ffprobe_file_get_error]");
				}
			}
			else
			{
				/*
					Default to "ffprobe" and rely on system $PATH
				*/
				$ffprobe_path = "ffprobe";
			}

			/*
				https://www.php.net/manual/en/function.escapeshellarg.php
				https://www.php.net/manual/en/function.escapeshellcmd.php
				https://trac.ffmpeg.org/wiki/Map
			*/
			$input = escapeshellarg($file_path);
			$execution_line = "$ffprobe_path -v error -show_streams $input";
			// var_dump($execution_line);

			/*
				Execute the command, then return the output from the command execution.

				https://www.php.net/manual/en/function.exec.php
				https://www.php.net/manual/en/function.implode.php
			*/
			exec($execution_line, $output, $status);
			$result = array(
				"output" => implode(" | ", $output)
				, "return_var" => $status
			);

			return $result;
		}
	}
?>
