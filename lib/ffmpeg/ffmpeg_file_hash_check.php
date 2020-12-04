<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("ffmpeg_file_hash_check"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function ffmpeg_file_hash_check($file_path, $ffmpeg_path)
		{
			/*
				https://www.php.net/manual/en/function.is-string.php
			*/
			if(is_string($file_path) === false)
			{
				throw new Exception("Invalid input. - [ffmpeg_file_hash_check]");

				return false;
			}
			else
			{
				/*
					https://www.php.net/manual/en/function.empty.php
				*/
				if(empty($file_path) === true)
				{
					throw new Exception("Missing path to file. - [ffmpeg_file_hash_check]");

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
				As the path to ffmpeg is optional, it will only be checked if the parameter is not empty.
				Note that the behavior of empty() differs from isset().
				isset() will return as true on a set variable, even if that variable is null.
				empty() will return as true on a set variable, even if it is null or is an empty string.

				https://www.php.net/manual/en/function.empty.php
			*/
			if(empty($ffmpeg_path) === false)
			{
				if(is_string($ffmpeg_path) === false)
				{
					throw new Exception("The input for the user-provided path to ffmpeg does not appear to be correct. - [ffmpeg_file_hash_check]");
				}
			}
			else
			{
				/*
					Default to "ffmpeg" and rely on system $PATH
				*/
				$ffmpeg_path = "ffmpeg";
			}

			/*
				https://www.php.net/manual/en/function.escapeshellarg.php
				https://www.php.net/manual/en/function.escapeshellcmd.php
				https://trac.ffmpeg.org/wiki/Map
			*/
			$input = escapeshellarg($file_path);
			$execution_line = "$ffmpeg_path -i $input -map 0:v? -map 0:a? -f md5 - 2>/dev/null | cut -c 5-36";
			// var_dump($execution_line);

			/*
				Open an output buffer to prevent output to stdout.
				Capture the return from the command execution.
				Check the return value.
					If it's false, return false.
					If it is not false, return the MD5 hash.

				https://www.php.net/manual/en/function.ob-start.php
				https://www.php.net/manual/en/function.ob-end-clean.php

				https://www.php.net/manual/en/function.system.php
			*/
			ob_start();
			$last_line = system($execution_line, $ret_val);
			ob_end_clean();

			if($ret_val === false)
			{
				$result = $ret_val;
			}
			else
			{
				$result = $last_line;
			}

			return $result;
		}
	}
?>
