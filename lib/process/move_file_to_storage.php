<?php

	if(!function_exists("move_file_to_storage"))
	{
		function move_file_to_storage($filepath, $new_filepath)
		{
			if(!is_string($filepath))
			{
				throw new Exception("Invalid input. " . gettype($filepath) . " received. Expected string. - [db_update_mark_file_as_downloaded]");

				return false;
			}
			else
			{
				if(empty($filepath) === true)
				{
					throw new Exception("Input is empty. - [move_file_to_storage]");

					return false;
				}
			}

			if(!is_string($new_filepath))
			{
				throw new Exception("Invalid input. " . gettype($new_filepath) . " received. Expected string. - [db_update_mark_file_as_downloaded]");

				return false;
			}
			else
			{
				if(empty($new_filepath) === true)
				{
					throw new Exception("Input is empty. - [move_file_to_storage]");

					return false;
				}
			}

			/*
				Get the directory name of where the file should be moved to.
				Verify that it already exists.
				If it does not, create it.
				In either case, verify that it can be written to.

				https://www.php.net/manual/en/function.dirname.php
				https://www.php.net/manual/en/function.file-exists.php
				https://www.php.net/manual/en/function.is-readable.php
				https://www.php.net/manual/en/function.is-writable.php
			*/
			$new_dirpath = dirname($new_filepath);

			if(file_exists($new_dirpath) === false)
			{
				trigger_error("The directory \"$new_dirpath\" does not appear to exist. Attempting to create it. - [db_update_mark_file_as_downloaded]", E_USER_WARNING);
				if(mkdir($new_dirpath, 0755, true) === true)
				{
					trigger_error("Successfully created the directory \"$new_dirpath\. - [db_update_mark_file_as_downloaded]", E_USER_NOTICE);
				}
				else
				{
					trigger_error("Could not create the directory \"$new_dirpath\". - [db_update_mark_file_as_downloaded]", E_USER_WARNING);
					return false;
				}
			}
			if(is_readable($new_dirpath) === true)
			{
				if(is_writable($new_dirpath) === false)
				{
					trigger_error("This process does not appear to have permissions to write to the directory \"$new_dirpath\". - [db_update_mark_file_as_downloaded]", E_USER_WARNING);
					return false;
				}
			}
			else
			{
				trigger_error("This process does not appear to have permissions to read from the directory \"$new_dirpath\". - [db_update_mark_file_as_downloaded]", E_USER_WARNING);
				return false;
			}

			/*
				Check for collisions. Does something already exist?
				https://www.php.net/manual/en/function.file-exists.php
			*/
			if(file_exists($new_filepath))
			{
				trigger_error("\"$filepath\" - Could not move file to specified path of \"$new_filepath\". A file with the same name is already present at that path. - [db_update_mark_file_as_downloaded]", E_USER_WARNING);
				return false;
			}

			/*
				Move from download path to storage path.
				https://www.php.net/manual/en/function.rename.php
			*/
			if(rename($filepath, $new_filepath) === true)
			{
				trigger_error("\"$filepath\" - File moved to \"$new_filepath\".", E_USER_NOTICE);
				return true;
			}
			else
			{
				trigger_error("\"$filepath\" - Could not move file to specified path of \"$new_filepath\".", E_USER_NOTICE);
				return false;
			}
		}
	}

?>