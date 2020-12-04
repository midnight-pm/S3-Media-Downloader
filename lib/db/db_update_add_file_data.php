<?php


	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("db_update_add_file_data"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function db_update_add_file_data($dbh, $array)
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
			if(!is_array($array))
			{
				throw new Exception("Invalid input. " . gettype($input) . " received. Expected array. - [db_update_mark_file_as_downloaded]");

				return false;
			}
			else
			{
				if(empty($array) === true)
				{
					throw new Exception("Provided input is empty. - [db_update_mark_file_as_downloaded]");

					return false;
				}
			}

			/*
				https://www.php.net/manual/en/pdo.prepare.php
				https://stackoverflow.com/a/52450550
			*/
			$sql = file_get_contents(QRY_PATH . "/update-add-file-data.sql");

			try
			{
				$stmt = $dbh->prepare($sql);
				$params = array(
						":filename" => $array["file_name"]
						, ":file_hash" => $array["file_hash"]
						, ":track_artist" => $array["track_artist"]
						, ":track_title" => $array["track_title"]
						, ":track_album" => $array["track_album"]
						, ":track_genre" => $array["track_genre"]
						, ":track_composer" => $array["track_composer"]
						, ":track_date" => $array["track_date"]
						, ":track_key" => $array["track_key"]
						, ":track_bpm" => $array["track_bpm"]
						, ":track_comment" => $array["track_comment"]
						, ":codec_name" => $array["codec_name"]
						, ":codec_long_name" => $array["codec_long_name"]
						, ":codec_type" => $array["codec_type"]
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
					trigger_error("\"" . $array["file_name"] . "\" - Added file information to database.", E_USER_NOTICE);
					return true;
				}
				else
				{
					trigger_error("\"" . $array["file_name"] . "\" - Error encountered while attempting to update database.", E_USER_WARNING);
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

				return false;
			}
		}
	}
	
?>
