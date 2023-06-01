<?php

	if(!function_exists("process_downloads"))
	{
		function process_downloads($dbh, $array, $ffmpeg_path, $ffprobe_path, $storage_directory)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-object.php
				http://php.net/manual/en/function.is-array.php
			*/
			if(!is_object($dbh))
			{
				throw new Exception("This function requires a valid database connection. - [process_downloads]");

				return false;
			}
			if(!is_array($array))
			{
				throw new Exception("Invalid input. - [process_downloads]");

				return false;
			}
			else
			{
				if(empty($array) === true)
				{
					throw new Exception("Input is empty. - [process_downloads]");

					return false;
				}
			}

			foreach($array AS $key => $fileinfo)
			{
				// var_dump($key);
				// var_dump($fileinfo);
				trigger_error("[$key] \"" . $fileinfo["path"] . "\" - Processing File. - [process_downloads]", E_USER_WARNING);

				/*
					Using ffprobe, probe the file for metadata.
				*/
				$ffprobe_json = ffprobe_file_info_json($fileinfo["path"], $ffprobe_path);
				// var_dump($ffprobe_json);

				$validate_json = json_validate($ffprobe_json);
				if($validate_json === false)
				{
					trigger_error("\"" . $fileinfo["path"] . "\" - $validate_json - [process_downloads]", E_USER_WARNING);
				}
				else
				{
					$ffprobe_array = decode_json_to_array($ffprobe_json);
					// var_dump($ffprobe_array);

					/*
						Check for an empty array.
						This is indicative of a bad file or a bad download.
						Delete the file and move on.
					*/
					if(empty($ffprobe_array) === true)
					{
						trigger_error("\"" . $fileinfo["path"] . "\" - Erroneous Download Detected. - [process_downloads]", E_USER_WARNING);
						$ffprobe_get_error = ffprobe_file_get_error($fileinfo["path"], $ffprobe_path);
						trigger_error("\"" . $fileinfo["path"] . "\" - [" . $ffprobe_get_error["return_var"] . "] - " . $ffprobe_get_error["output"] . " - [process_downloads]", E_USER_WARNING);

						/*
							Delete the erroneous file.
							https://www.php.net/manual/en/function.unlink.php
						*/
						if(unlink($fileinfo["path"]) === true)
						{
							trigger_error("\"" . $fileinfo["path"] . "\" - Discard File - [process_downloads]", E_USER_NOTICE);
						}
						else
						{
							trigger_error("\"" . $fileinfo["path"] . "\" - Error encountered when attempting to discard file - [process_downloads]", E_USER_WARNING);
						}
						/*
						$db_mark = db_update_mark_file_as_downloaded($dbh, $fileinfo["filename"]);
						*/
					}
					/*
						Validate that the expected array under the key of "$ffprobe_array["format"]["tags"]" is set and is not empty.
						If it is not set, or if it is empty, then consider the file bad as no tags have been added.
					*/
					elseif(empty($ffprobe_array["format"]["tags"]) === true)
					{
						trigger_error("\"" . $fileinfo["path"] . "\" - Erroneous Download Detected. - [process_downloads]", E_USER_WARNING);
						$ffprobe_get_error = ffprobe_file_get_error($fileinfo["path"], $ffprobe_path);
						trigger_error("\"" . $fileinfo["path"] . "\" - [" . $ffprobe_get_error["return_var"] . "] - " . $ffprobe_get_error["output"] . " - [process_downloads]", E_USER_WARNING);

						/*
							Delete the erroneous file.
						*/
						if(unlink($fileinfo["path"]) === true)
						{
							trigger_error("\"" . $fileinfo["path"] . "\" - Discard File - [process_downloads]", E_USER_NOTICE);
						}
						else
						{
							trigger_error("\"" . $fileinfo["path"] . "\" - Error encountered when attempting to discard file - [process_downloads]", E_USER_WARNING);
						}

						/*
							Do not retrieve this file again.
							----
							Update the database to indicate that the file has been retrieved already.
							This will prevent it from being retrieved again in the future.
						*/
						$db_mark = db_update_mark_file_as_downloaded($dbh, $fileinfo["filename"]);
					}
					/*
						Begin normal processing.
					*/
					else
					{
						/*
							Using ffmpeg, calculate the MD5 hash using the file's actual audio and video streams (if applicable).
							This will be used to detect duplicates - even when tag data differs.
						*/
						$ffmpeg_md5 = ffmpeg_file_hash_check($fileinfo["path"], $ffmpeg_path);
						// var_dump($ffmpeg_md5);

						if($ffmpeg_md5 === false)
						{
							trigger_error("\"" . $fileinfo["path"] . "\" - Error encountered when attempting to calculate the MD5 hash. - [process_downloads]", E_USER_WARNING);
						}
						else
						{
							/*
								Set variable values.
								Using ternary operators (for the sake of brevity), check to see if the value isset.
								If it's not, assign the variable a value of NULL.

								https://www.php.net/manual/en/language.operators.comparison.php
								https://davidwalsh.name/php-shorthand-if-else-ternary-operators
								https://www.php.net/manual/en/function.isset.php
							*/
							$file_track_artist = (isset($ffprobe_array["format"]["tags"]["artist"]) ? $ffprobe_array["format"]["tags"]["artist"] : NULL);
							$file_track_title = (isset($ffprobe_array["format"]["tags"]["title"]) ? $ffprobe_array["format"]["tags"]["title"] : NULL );
							$file_track_album = (isset($ffprobe_array["format"]["tags"]["album"]) ? $ffprobe_array["format"]["tags"]["album"] : NULL );
							$file_track_genre = (isset($ffprobe_array["format"]["tags"]["genre"]) ? $ffprobe_array["format"]["tags"]["genre"] : NULL);
							$file_track_composer = (isset($ffprobe_array["format"]["tags"]["composer"]) ? $ffprobe_array["format"]["tags"]["composer"] : NULL);
							$file_track_date = (isset($ffprobe_array["format"]["tags"]["date"]) ? $ffprobe_array["format"]["tags"]["date"] : NULL);

							/*
								Find the Key
								https://www.php.net/manual/en/function.array-key-exists.php
							*/
							if(array_key_exists("TKEY", $ffprobe_array["format"]["tags"]) === true)
							{
								$file_track_key = $ffprobe_array["format"]["tags"]["TKEY"];
							}
							elseif(array_key_exists("initialkey", $ffprobe_array["format"]["tags"]) === true)
							{
								$file_track_key = $ffprobe_array["format"]["tags"]["initialkey"];
							}
							else
							{
								$file_track_key = NULL;
							}

							/*
								Find the BPM
							*/
							if(array_key_exists("TBPM", $ffprobe_array["format"]["tags"]) === true)
							{
								$file_track_bpm = $ffprobe_array["format"]["tags"]["TBPM"];
							}
							else
							{
								$file_track_bpm = NULL;
							}

							$file_track_comment = (isset($ffprobe_array["format"]["tags"]["comment"]) ? $ffprobe_array["format"]["tags"]["comment"] : NULL);
							$file_codec_name = (isset($ffprobe_array["streams"]["0"]["codec_name"]) ? $ffprobe_array["streams"]["0"]["codec_name"] : NULL);
							$file_codec_long_name = (isset($ffprobe_array["streams"]["0"]["codec_long_name"]) ? $ffprobe_array["streams"]["0"]["codec_long_name"] : NULL);
							$file_codec_type = (isset($ffprobe_array["streams"]["0"]["codec_type"]) ? $ffprobe_array["streams"]["0"]["codec_type"] : NULL);

							/*
								Assign values to new array.
								Use trim to remove extraneous white space.
								https://www.php.net/manual/en/function.trim.php
							*/
							$retrieval = array(
								"file_name" => $fileinfo["filename"]
								, "file_timestamp" => $fileinfo["date"] . " " . $fileinfo["time"]
								, "file_size" => $fileinfo["size"]
								, "file_hash" => $ffmpeg_md5
								, "track_artist" => filter_string_var($file_track_artist)
								, "track_title" => filter_string_var($file_track_title)
								, "track_album" => filter_string_var($file_track_album)
								, "track_genre" => filter_string_var($file_track_genre)
								, "track_composer" => filter_string_var($file_track_composer)
								, "track_date" => filter_string_var($file_track_date)
								, "track_key" => filter_string_var($file_track_key)
								, "track_bpm" => filter_string_var($file_track_bpm)
								, "track_comment" => filter_string_var($file_track_comment)
								, "codec_name" => filter_string_var($file_codec_name)
								, "codec_long_name" => filter_string_var($file_codec_long_name)
								, "codec_type" => filter_string_var($file_codec_type)
							);

							// var_dump($retrieval);

							$hashcheck = db_check_entries_filehash($dbh, $ffmpeg_md5);
							if($hashcheck === true)
							{
								/*
									Found the record in the database.
									Toss the downloaded file as a duplicate.

									https://www.php.net/manual/en/function.unlink.php
								*/
								trigger_error("\"" . $fileinfo["path"] . "\" - Dupe Detected - File hash of \"$ffmpeg_md5\" found in database. Removing downloaded file and flagging it in the database as retrieved. - [process_downloads]", E_USER_NOTICE);
								$db_mark = db_update_mark_file_as_downloaded($dbh, $fileinfo["filename"]);
								if($db_mark === true)
								{
									unlink($fileinfo["path"]);
								}
							}
							else
							{
								/*
									Did not find the record.
									New file!
									Update the database with the retrieved data.
									Move file to storage.
								*/
								$db_update = db_update_add_file_data($dbh, $retrieval);
								if($db_update === true)
								{
									/*
										Set new filename.
										Strip unallowed characters and characters that will cause problems.
										https://www.php.net/manual/en/function.pathinfo.php
										https://stackoverflow.com/questions/10368217/how-to-get-the-file-extension-in-php
									*/
									$new_file_name = filter_file_name("$file_track_artist - $file_track_title." . pathinfo($fileinfo["filename"], PATHINFO_EXTENSION));

									/*
										Check the genre to see if it is set or not.
										If it is not, then do not append it to the filename.
									*/
									if(empty($file_track_genre) === true)
									{
										$new_file_path = $storage_directory . "/" . $new_file_name;
									}
									else
									{
										$new_file_path = $storage_directory . "/" . filter_file_name($file_track_genre) . "/" . $new_file_name;
									}

									/*
										Move the file to storage.
									*/
									$store_file = move_file_to_storage($fileinfo["path"], $new_file_path);

									/*
										Mark the record in the database for this file as having been downloaded.
									*/
									if($store_file === true)
									{
										$db_mark = db_update_mark_file_as_downloaded($dbh, $fileinfo["filename"]);
										trigger_error("\"" . $fileinfo["path"] . "\" - File successfully stored. - [process_downloads]", E_USER_NOTICE);
									}
									else
									{
										trigger_error("\"" . $fileinfo["path"] . "\" - File could not be successfully stored. - [process_downloads]", E_USER_WARNING);
									}
								}
							}
						}
					}
				}
				// exit();
			}

			return true;
		}
	}