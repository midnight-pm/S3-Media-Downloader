<?php

	/*
		This function is used to parse a listing obtained from the specified Amazon S3 Bucket
	*/

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("aws_parse_listing"))
	{
		function aws_parse_listing($input)
		{
			/*
				Test Input.
			*/
			if(empty($input) === true)
			{
				throw new Exception("Input does not appear to have been provided. - [aws_parse_listing]");
			}
			else
			{
				/*
					Expect a raw string as input.
				*/
				if(is_string($input) === false)
				{
					throw new Exception("Invalid input. - [aws_parse_listing]");
				}
			}

			/*
				Instantiate a variable named $result, defining it as an array().
			*/
			$result = array();

			trigger_error("Parsing retrieved listing from AWS - [aws_get_listing]", E_USER_NOTICE);
			/*
				This can be done using multiple techniques such as "explode()" and "preg_split()".
				Either of those would create an array of lines that can be iterated over.
				For reasons of speed, "strtok()" is used.

				Reference: https://stackoverflow.com/a/14789147

				The seperator of choice will be the PHP constant PHP_EOL to detect the line-endings returned by the system.
			*/
			$line = strtok($input, PHP_EOL);

			/*
				Use a while loop to go through each line.
			*/
			while ($line !== false)
			{
				/*
					Now do something with each line.
				*/
				$line = strtok(PHP_EOL);

				/*
					Utilize "is_string()" to filter out anything that is not a string (i.e.: if bool(false) sneaks in).
				*/
				if(is_string($line) === true)
				{
					/*
						This time, because the purpose is to create an array, we'll utilize "preg_split" to split the string into separate paramters.
						From there, we'll use array_combine() to give the result usable keys.
						Finally, we'll use array_push to add each result to one final array.
					*/
					$key_map = array("date", "time", "size", "filename");
					$val_map = preg_split("/\s+/", $line);
					$element = array_combine($key_map, $val_map);

					array_push($result, $element);
				}
				else
				{
					/*
						Raise Notice and Discard
					*/
					trigger_error("Value of \"$line\" does not appear to be a valid useable entry.", E_USER_NOTICE);
				}
			}

			/*
				Attempt to sort array results.
				https://www.php.net/manual/en/function.array-multisort.php
			*/
			array_multisort($result);

			/*
				Return the result to the client.
				The value will be either true or false.

				https://www.php.net/manual/en/function.return.php
			*/
			return $result;
		}
	}

?>
