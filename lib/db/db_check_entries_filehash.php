<?php


	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("db_check_entries_filehash"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function db_check_entries_filehash($dbh, $hash)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-object.php
				http://php.net/manual/en/function.is-string.php
			*/
			if(!is_object($dbh))
			{
				throw new Exception("This function requires a valid database connection. - [db_check_entry_filehash]");

				return false;
			}
			if(!is_string($hash))
			{
				throw new Exception("Invalid input. " . gettype($input) . " received. Expected string. - [db_check_entry_filehash]");

				return false;
			}
			else
			{
				if(empty($hash) === true)
				{
					throw new Exception("Input is empty. - [db_check_entry_filehash]");

					return false;
				}
			}

			/*
				https://www.php.net/manual/en/pdo.prepare.php
				https://stackoverflow.com/a/52450550
			*/
			$find_sql = file_get_contents(QRY_PATH . "/select-filehash.sql");
			$find_stmt = $dbh->prepare($find_sql);

			/*
				$key holds the array key.
				$fileinfo holds the file info from the aws listing.
			*/
			// var_dump($key);
			// var_dump($fileinfo);

			try
			{
				/*
					https://www.php.net/manual/en/pdostatement.execute.php
				*/
				$params = array(
						":filehash" => $hash
					);
				$query = $find_stmt->execute($params);
				// var_dump($query);
				$fetch = $find_stmt->fetchAll(PDO::FETCH_ASSOC);
				// var_dump($fetch);
				if(is_array($fetch) === true)
				{
					if(count($fetch) >= (int) 1)
					{
						$found = true;
					}
					else
					{
						$found = false;
					}
				}
				else
				{
					$found = false;
				}

				if($found === false)
				{
					/*
						Record was not found in the database.
					*/
					return false;
				}
				else
				{
					/*
						Record was found in the database.
					*/
					return true;
				}
			}
			/*
				Use the PDOException handler.
				http://php.net/manual/en/class.pdoexception.php
			*/
			catch(PDOException $e)
			{
				$error_msg = $e->getMessage();
				$error_code = $e->getCode();

				$error = "$error_msg - [$error_code] - [db_check_entries_filenames]";
				trigger_error($error, E_USER_WARNING);
				throw new Exception($error);

				return false;
			}
		}
	}
	
?>
