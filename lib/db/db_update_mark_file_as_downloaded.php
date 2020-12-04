<?php


	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("db_update_mark_file_as_downloaded"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function db_update_mark_file_as_downloaded($dbh, $filename)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-object.php
				http://php.net/manual/en/function.is-string.php
			*/
			if(!is_object($dbh))
			{
				throw new Exception("This function requires a valid database connection. - [db_update_mark_file_as_downloaded]");

				return false;
			}
			if(!is_string($filename))
			{
				throw new Exception("Invalid input. " . gettype($input) . " received. Expected string. - [db_update_mark_file_as_downloaded]");

				return false;
			}
			else
			{
				if(empty($filename) === true)
				{
					throw new Exception("Provided input is empty. - [db_update_mark_file_as_downloaded]");

					return false;
				}
			}

			/*
				Get the current time
				https://www.php.net/manual/en/datetime.format.php
			*/
			$date = new DateTime();
			$datetime = $date->format('Y-m-d H:i:s');

			/*
				https://www.php.net/manual/en/pdo.prepare.php
				https://stackoverflow.com/a/52450550
			*/
			$sql = file_get_contents(QRY_PATH . "/update-mark-file-as-downloaded.sql");

			try
			{
				$stmt = $dbh->prepare($sql);
				$params = array(
						":datetime" => $datetime
						, ":filename" => $filename
					);
				$query = $stmt->execute($params);
				// var_dump($query);
				/*
					https://www.php.net/manual/en/pdostatement.rowcount.php
				*/
				$count = $stmt->rowCount();
				// var_dump($count);
				if($count >= 1)
				{
					trigger_error("\"$filename\" - Marked in database as downloaded.", E_USER_NOTICE);
					return true;
				}
				else
				{
					trigger_error("\"$filename\" - Error encountered while attempting to update database.", E_USER_WARNING);
					return false;
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
			}

			return $result;
		}
	}
	
?>
