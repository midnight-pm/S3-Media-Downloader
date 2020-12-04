<?php

	/*
		Adapted from sanitize_file_name() function in Wordpress
		https://developer.wordpress.org/reference/functions/sanitize_file_name/
	*/
	if(!function_exists("filter_file_name"))
	{
		function filter_file_name($input)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-string.php
			*/
			if(!is_string($input))
			{
				trigger_error("Invalid input. " . gettype($input) . " received. Expected string. - [filter_file_name]", E_USER_WARNING);

				return $input;
			}
			else
			{
				if(empty($input) === true)
				{
					trigger_error("Provided input is empty. - [filter_file_name]", E_USER_WARNING);

					return $input;
				}
			}

			$string = $input;

			/*
				Array of filesystem forbidden characters
				https://www.askingbox.com/tip/php-remove-invalid-characters-from-file-names
			*/
			$special_chars = array(
				'\\'
				, '/'
				, ':'
				, '*'
				, '?'
				, '"'
				, '<'
				, '>'
				, '|'
			);

			/*
				https://www.php.net/manual/en/function.str-replace.php
			*/
			$string = str_replace($special_chars, ' ', $string );

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