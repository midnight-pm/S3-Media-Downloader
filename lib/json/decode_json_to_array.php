<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("decode_json_to_array"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function decode_json_to_array($input)
		{
			/*
				http://www.php.net/manual/en/function.is-string.php
			*/
			if(!is_string($input))
			{
				/*
					https://www.php.net/manual/en/function.gettype.php
				*/
				throw new Exception("Invalid input. " . gettype($input) . " received. Expected string. - [decode_json_to_array]");

				return false;
			}
			else
			{
				/*
					http://www.php.net/manual/en/function.empty.php
				*/
				if(empty($input))
				{
					throw new Exception ("Provided input is empty. - [decode_json_to_array]");

					return false;
				}
			}

			/*
				http://php.net/manual/en/function.utf8-encode.php
				-------------------------------------------------
				Take the input, and set it to UTF-8.
			*/
			$input_utf8 = utf8_encode($input);

			/*
				http://php.net/manual/en/function.json-decode.php
				-------------------------------------------------
				Decode the UTF-8-encoded JSON, and change it to
				an associative array.
			*/
			$array = json_decode($input_utf8, true);

			/*
				http://php.net/manual/en/function.is-array.php
			*/
			if(is_array($array))
			{
				/*
					Return an array.
				*/
				return $array;
			}
			else
			{
				trigger_error("Failed to process input as an array. - [decode_json_to_array]", E_USER_WARNING);

				return false;
			}
		}
	}