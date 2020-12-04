<?php

	/*
		Adapted from sanitize_file_name() function in Wordpress
		https://developer.wordpress.org/reference/functions/sanitize_file_name/
	*/
	if(!function_exists("filter_string_var"))
	{
		function filter_string_var($input)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-string.php
			*/
			if(!is_string($input))
			{
				trigger_error("Invalid input. " . gettype($input) . " received. Expected string. - [filter_string_var]", E_USER_WARNING);

				return $input;
			}
			else
			{
				if(empty($input) === true)
				{
					trigger_error("Provided input is empty. - [filter_string_var]", E_USER_WARNING);

					return $input;
				}
			}

			$string = $input;

			/*
				Remove Non-breaking_space
				https://en.wikipedia.org/wiki/Non-breaking_space
			*/
			$string = preg_replace("#\x{00a0}#siu", ' ', $string );

			/*
				Remove multiple occurences of whitespace characters in a string and convert them all into single spaces
				https://www.php.net/manual/en/function.trim.php#41699
			*/
			$string = preg_replace('/\s+/', ' ', $string);

			/*
				Linebreaks
			*/
			$string = str_replace(PHP_EOL, '', $string);

			/*
				Strip any whitespace from the beginning and end of the string.
				https://www.php.net/manual/en/function.trim.php
			*/
			$string = trim($string);

			/*
				https://www.php.net/manual/en/function.return.php
			*/
			return $string;
		}
	}

?>