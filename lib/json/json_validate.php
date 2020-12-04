<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("json_validate"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function json_validate($input)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-string.php
			*/
			if(!is_string($input))
			{
				/*
					https://www.php.net/manual/en/function.gettype.php
				*/
				throw new Exception("Invalid input. " . gettype($input) . " received. Expected string. - [json_validate]");

				return false;
			}
			else
			{
				if(empty($input) === true)
				{
					throw new Exception("Input is empty. - [json_validate]");

					return false;
				}
			}

			// var_dump($input);
			/*
				Decode the JSON data
				http://php.net/manual/en/function.json-decode.php
			*/
			$result = json_decode($input);

			/*
				Check for possible JSON errors
				http://php.net/manual/en/control-structures.switch.php
			*/
			switch (json_last_error())
			{
				case JSON_ERROR_NONE:
					$error = ""; // JSON is valid // No error has occurred
					break;
				case JSON_ERROR_DEPTH:
					$error = "The maximum stack depth has been exceeded - [JSON_ERROR_DEPTH]";
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$error = "Invalid or malformed JSON - [JSON_ERROR_STATE_MISMATCH]";
					break;
				case JSON_ERROR_CTRL_CHAR:
					$error = "Control character error, possibly incorrectly encoded - [JSON_ERROR_CTRL_CHAR]";
					break;
				case JSON_ERROR_SYNTAX:
					$error = "Syntax error, malformed JSON - [JSON_ERROR_SYNTAX]";
					break;
				/*
					PHP >= 5.3.3
				*/
				case JSON_ERROR_UTF8:
					$error = "Malformed UTF-8 characters, possibly incorrectly encoded - [JSON_ERROR_UTF8]";
					break;
				/*
					PHP >= 5.5.0
				*/
				case JSON_ERROR_RECURSION:
					$error = "One or more recursive references in the value to be encoded - [JSON_ERROR_RECURSION]";
					break;
				/*
					PHP >= 5.5.0
				*/
				case JSON_ERROR_INF_OR_NAN:
					$error = "One or more NAN or INF values in the value to be encoded - [JSON_ERROR_INF_OR_NAN]";
					break;
				case JSON_ERROR_UNSUPPORTED_TYPE:
					$error = "A value of a type that cannot be encoded was given - [JSON_ERROR_UNSUPPORTED_TYPE]";
					break;
				default:
					$error = "Unknown JSON error occured - [UNKNOWN]";
					break;
			}

			/*
				http://php.net/manual/en/function.unset.php
			*/
			$result = NULL;
			unset($result);

			if ($error !== "")
			{
				return $error;
			}
			else
			{
				return true;
			}
		}
	}
?>