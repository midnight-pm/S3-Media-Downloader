<?php


	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("db_check_entries_filenames"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function db_check_entries_filenames($dbh, $array, $s3_bucket)
		{
			/*
				$initial_size = count($array);
				var_dump($initial_size);
			*/

			/*
				Test input.
				http://php.net/manual/en/function.is-object.php
				http://php.net/manual/en/function.is-array.php
			*/
			if(!is_object($dbh))
			{
				throw new Exception("This function requires a valid database connection. - [db_check_entries_filenames]");

				return false;
			}
			if(!is_array($array))
			{
				throw new Exception("Invalid input. - [db_check_entries_filenames]");

				return false;
			}
			else
			{
				if(empty($array) === true)
				{
					throw new Exception("Input is empty. - [db_check_entries_filenames]");

					return false;
				}
			}

			/*
				Instantiate an empty array.
				Elements which have been identified as not being a duplicate record will be written here.
			*/
			$result = array();

			/*
				https://www.php.net/manual/en/pdo.prepare.php
				https://stackoverflow.com/a/52450550
			*/
			$find_sql = file_get_contents(QRY_PATH . "/select-filename.sql");
			$find_stmt = $dbh->prepare($find_sql);

			$verify_sql = file_get_contents(QRY_PATH . "/select-filename-where-downloaded-true.sql");
			$verify_stmt = $dbh->prepare($verify_sql);

			$insert_sql = file_get_contents(QRY_PATH . "/insert-new-file.sql");
			$insert_stmt = $dbh->prepare($insert_sql);

			/*
				Loop through input array.
			*/
			foreach($array AS $key => $fileinfo)
			{
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
							":filename" => $fileinfo['filename']
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
							The file was not found.
							Add it to the database.
						*/
						$params = array(
								":s3_bucket" => $s3_bucket
								, ":s3_timestamp" => $fileinfo['date'] . " " . $fileinfo['time']
								, ":s3_size" => $fileinfo['size']
								, ":filename" => $fileinfo['filename']
							);
						$query = $insert_stmt->execute($params);
						// var_dump($query);
						/*
							https://www.php.net/manual/en/pdostatement.rowcount.php
						*/
						$count = $insert_stmt->rowCount();
						// var_dump($count);
						if($count >= 1)
						{
							trigger_error("[$key] \"" . $fileinfo['filename'] . "\" added to database. File will be retrieved from Amazon AWS S3 Bucket \"$s3_bucket\".", E_USER_NOTICE);
						}
						else
						{
							trigger_error("[$key] \"" . $fileinfo['filename'] . "\" error encountered while attempting to add file to database.", E_USER_WARNING);
						}
					}
					else
					{
						/*
							The file was found in the database.
							Verify that it has already been downloaded.
						*/
						$params = array(
								":filename" => $fileinfo['filename']
							);
						$query = $verify_stmt->execute($params);
						// var_dump($query);
						$fetch = $verify_stmt->fetchAll(PDO::FETCH_ASSOC);
						// var_dump($fetch);
						if(is_array($fetch) === true)
						{
							if(count($fetch) >= (int) 1)
							{
								/*
									If this file has already been retrieved, drop it from the array.
									https://www.php.net/manual/en/function.unset.php#99607
								*/
								unset($array[$key]);
								trigger_error("[$key] \"" . $fileinfo['filename'] . "\" found in database. Will not be retrieved.", E_USER_NOTICE);
							}
							else
							{
								trigger_error("[$key] \"" . $fileinfo['filename'] . "\" found in database, but has not yet been retrieved.", E_USER_NOTICE);
								// var_dump(count($fetch));
								// var_dump($fetch);
							}
						}
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
			}

			/*
				Reindex the array
				Return the result

				https://www.php.net/manual/en/function.array-values.php
			*/
			$result = array_values($array);

			/*
			$final_size = count($result);
			var_dump($final_size);
			*/

			return $result;
		}
	}
	
?>
