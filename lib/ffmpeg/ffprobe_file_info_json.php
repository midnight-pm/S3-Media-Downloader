<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("ffprobe_file_info_json"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function ffprobe_file_info_json($file_path, $ffprobe_path)
		{
			/*
				https://www.php.net/manual/en/function.is-string.php
			*/
			if(is_string($file_path) === false)
			{
				throw new Exception("Invalid input. - [ffprobe_file_info_json]");

				return false;
			}
			else
			{
				/*
					https://www.php.net/manual/en/function.empty.php
				*/
				if(empty($file_path) === true)
				{
					throw new Exception("Missing path to file. - [ffprobe_file_info_json]");

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
					throw new Exception("The input for the user-provided path to ffprobe does not appear to be correct. - [ffprobe_file_info_json]");
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
			$execution_line = "$ffprobe_path -v quiet -print_format json -show_format -show_streams $input";
			// var_dump($execution_line);

			/*
				Open an output buffer, then capture the return from the command execution.

				https://www.php.net/manual/en/function.ob-start.php
				https://www.php.net/manual/en/function.ob-get-contents.php
				https://www.php.net/manual/en/function.ob-end-clean.php

				https://www.php.net/manual/en/function.passthru.php
				https://www.php.net/manual/en/function.passthru.php#50540
			*/
			ob_start();
			passthru($execution_line);
			$result = ob_get_contents();
			ob_end_clean();

			return $result;
		}
	}
?>
