<?php

	if(!function_exists("store_download"))
	{
		function store_download($download_path, $file_timestamp, $response)
		{
			/*
				Check to see if the file already exists. If it does, delete it.
				https://www.php.net/manual/en/function.file-exists.php
				https://www.php.net/manual/en/function.unlink.php
			*/
			if(file_exists($download_path) === true)
			{
				if(unlink($download_path) === true)
				{
					trigger_error("\"$download_path\" - Removed already existing file.", E_USER_NOTICE);
				}
				else
				{
					trigger_error("\"$download_path\" - Error encountered when attempting to delete already existing file.", E_USER_WARNING);
				}
			}

			/*
				Place the received contents in $download_path
				https://www.php.net/manual/en/function.file-put-contents.php
			*/
			if(file_put_contents($download_path, $response) !== false)
			{
				/*
					Set modification time of file.
					https://www.php.net/manual/en/function.touch.php
				*/
				if(touch($download_path, $file_timestamp) === true)
				{
					trigger_error("\"$download_path\" - File modification time has been set to $file_timestamp.", E_USER_NOTICE);
				}
				else
				{
					trigger_error("\"$download_path\" - Could not change the file modification time.", E_USER_WARNING);
				}

				trigger_error("\"$download_path\" - Successfully Stored.", E_USER_NOTICE);
				return true;
			}
			else
			{
				trigger_error("Error encountered when attempting to write to \"$download_path\".", E_USER_WARNING);
				return false;
			}
		}
	}

	if(!function_exists("check_download_size"))
	{
		function check_download_size($download_path, $expected_size)
		{
			/*
				Compare the file size of the download to the file size of the listing.
				https://www.php.net/manual/en/function.filesize.php
			*/
			$filesize = filesize($download_path);
			if((int) $filesize !== (int) $expected_size)
			{
				trigger_error("\"$download_path\" - File Size Mismatch. Expected: $expected_size, Received: $filesize.", E_USER_WARNING);
				return false;
			}
			else
			{
				trigger_error("\"$download_path\" - File Size OK. Received: $filesize.", E_USER_NOTICE);
				return true;
			}
		}
	}

	if(!function_exists("rcx_callback_function"))
	{
		function rcx_callback_function($response, $url, $request_info, $user_data, $time, $post_data, $headers, $response_body)
		{

			$time; //how long the request took in milliseconds (float)
			$request_info;	/*	Info returned by curl_getinfo($ch)
								Contains:
									response headers
									url
									content-type
									http code
									time
										transfers
										total
									speed
									size
									etc.
							*/
			$user_data; /*	User Supplied Data
							Set Parameters:
								filename - filename in S3
								size - size of file in S3
								date - datestamp of file in S3
								time - timestamp of file in S3
						*/
			// var_dump($request_info);
			// var_dump($user_data);

			/*
				Will need to later figure out how to pass this as a parameter.
			*/
			global $config;
			$received_directory = $config["received"]["directory"];
			$download_path = $received_directory . "/" . $user_data["filename"];

			$expected_size = $user_data["size"];

			/*
				Get modification time of file.
				https://www.php.net/manual/en/function.strtotime.php
			*/
			$file_datetime = $user_data["date"] . " " . $user_data["time"];
			$file_timestamp = strtotime($file_datetime);

			if(store_download($download_path, $file_timestamp, $response) === true)
			{
				if(check_download_size($download_path, $expected_size) === false)
				{
					/*
						Remove erroneous download.
					*/
					if(unlink($download_path) === true)
					{
						trigger_error("\"$download_path\" - Removed erroneous download.", E_USER_NOTICE);
					}
					else
					{
						trigger_error("\"$download_path\" - Error encountered when attempting to delete erroneous download.", E_USER_WARNING);
					}
				}
				else
				{
					global $retrieved_files;
					$retrieved_files[] = array(
						"filename" => $user_data["filename"]
						, "size" => $user_data["size"]
						, "date" => $user_data["date"]
						, "time" => $user_data["time"]
						, "path" => $download_path
					);
				}
			}
		}
	}

?>