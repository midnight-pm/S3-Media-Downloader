<?php

	/*
		If the request was not made from the CLI, and the request method was not GET or POST, full stop.
	*/
	if(PHP_SAPI !== "cli")
	{
		print "This is intended to be run from a command line interface." . PHP_EOL;
		exit();
	}
	else
	{
		/*
			In advance, set the script to operate as if the timezone is "UTC" by default.
		*/
		date_default_timezone_set("UTC");
	}

	/*
		Define named constants.
		http://php.net/manual/en/function.define.php
	*/

	define('BASE_PATH', dirname(__FILE__));
	define('CLS_PATH', BASE_PATH . "/cls");
	define('DB_PATH', BASE_PATH . "/db");
	define('INC_PATH', BASE_PATH . "/inc");
	define('LIB_PATH', BASE_PATH . "/lib");
	define('QRY_PATH', BASE_PATH . "/queries");
	define('RES_PATH', BASE_PATH . "/res");


	/*
		Bring in required files for use.
	*/
	require(RES_PATH . "/config.inc.php");
	require(CLS_PATH . "/logger.class.php");
	require(CLS_PATH . "/rcx/rollingcurlx.class.php");

	require(LIB_PATH . "/error/exception_cleanup.php");
	require(LIB_PATH . "/error/exception_error_log.php");
	require(LIB_PATH . "/error/exception_error_output.php");
	require(LIB_PATH . "/error/exception_handler.php");
	require(LIB_PATH . "/guid/generate_guid.php");

	require(LIB_PATH . "/db/db_connect.php");
	require(LIB_PATH . "/db/db_create_tables.php");
	require(LIB_PATH . "/db/db_check_entries_filehash.php");
	require(LIB_PATH . "/db/db_check_entries_filenames.php");
	require(LIB_PATH . "/db/db_update_add_file_data.php");
	require(LIB_PATH . "/db/db_update_mark_file_as_downloaded.php");

	require(LIB_PATH . "/aws/aws_verify_install.php");
	require(LIB_PATH . "/aws/aws_get_listing.php");
	require(LIB_PATH . "/aws/aws_parse_listing.php");

	require(LIB_PATH . "/ffmpeg/ffmpeg_verify_install.php");
	require(LIB_PATH . "/ffmpeg/ffmpeg_file_hash_check.php");
	require(LIB_PATH . "/ffmpeg/ffprobe_verify_install.php");
	require(LIB_PATH . "/ffmpeg/ffprobe_file_get_error.php");
	require(LIB_PATH . "/ffmpeg/ffprobe_file_info_json.php");

	require(LIB_PATH . "/filters/filter_file_name.php");
	require(LIB_PATH . "/filters/filter_string_var.php");

	require(LIB_PATH . "/json/decode_json_to_array.php");
	require(LIB_PATH . "/json/json_validate.php");

	require(LIB_PATH . "/process/create_downloads_array.php");
	require(LIB_PATH . "/process/move_file_to_storage.php");
	require(LIB_PATH . "/process/process_downloads.php");

	require(LIB_PATH . "/rcx/rcx_build_object.php");
	require(LIB_PATH . "/rcx/rcx_build_request.php");
	require(LIB_PATH . "/rcx/rcx_callback_function.php");
	require(LIB_PATH . "/rcx/rcx_execute.php");

	/*
		Generate a GUID.
	*/
	$guid = generate_guid();

	/*
		Open logger
	*/
	try
	{
		$log = new logger();
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}

	/*
		Verify dependencies
	*/
	try
	{
		/*
			Verify that the aws cli client is available
		*/
		if(aws_verify_install($config["dependencies"]["aws"]) === false)
		{
			if(empty($config["dependencies"]["aws"]) === false)
			{
				trigger_error("The aws client does not appear to be installed at the specified location of \"" . $config["dependencies"]["aws"] . "\". Please install the client, or provide the correct full path to the aws client's location." . PHP_EOL, E_USER_ERROR);
			}
			else
			{
				trigger_error("The aws client does not appear to be installed. Please install the client, or provide the full path to the aws client's location." . PHP_EOL, E_USER_ERROR);
			}
		}

		/*
			Verify that ffmpeg is available
		*/
		if(ffmpeg_verify_install($config["dependencies"]["ffmpeg"]) === false)
		{
			if(empty($config["dependencies"]["ffmpeg"]) === false)
			{
				trigger_error("ffmpeg does not appear to be installed at the specified location of \"" . $config["dependencies"]["ffmpeg"] . "\". Please install ffmpeg, or provide the correct full path to ffmpeg's location." . PHP_EOL, E_USER_ERROR);
			}
			else
			{
				trigger_error("ffmpeg does not appear to be installed. Please install ffmpeg, or provide the full path to ffmpeg's location." . PHP_EOL, E_USER_ERROR);
			}
		}

		/*
			Verify that ffprobe is available
		*/
		if(ffprobe_verify_install($config["dependencies"]["ffprobe"]) === false)
		{
			if(empty($config["dependencies"]["ffprobe"]) === false)
			{
				trigger_error("ffprobe does not appear to be installed at the specified location of \"" . $config["dependencies"]["ffprobe"] . "\". Please install ffprobe, or provide the correct full path to ffprobe's location." . PHP_EOL, E_USER_ERROR);
			}
			else
			{
				trigger_error("ffprobe does not appear to be installed. Please install ffmpeg, or provide the full path to ffprobe's location." . PHP_EOL, E_USER_ERROR);
			}
		}
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}

	/*
		Attempt to open a connection to the sqlite database.
	*/
	try
	{
		$db = db_connect($config["database"]);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($db);
		}
	}

	/*
		Verify that the table exists in the sqlite database.
		If it does not exist, create it.
	*/
	try
	{
		$build_table = db_create_tables($db);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($build_table);
		}
	}

	/*
		Obtain a listing.
	*/
	try
	{
		$aws_listing = aws_get_listing($config["aws"]["s3_bucket_name"], $config["dependencies"]["aws"]);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			// var_dump($aws_listing);
		}
	}

	/*
		Parse the obtained listing.
	*/
	try
	{
		$aws_parsed = aws_parse_listing($aws_listing);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			// var_dump($aws_parsed);
		}
	}

	/*
		Check parsed obtained listing against database for existing entries.
		Toss any files that are have already been downloaded.
		Retrieve any files that have not been.
		Add entries to database.
	*/
	try
	{
		$files_to_retrieve = db_check_entries_filenames($db, $aws_parsed, $config["aws"]["s3_bucket_name"]);
		// $files_to_retrieve = @db_check_entries_filenames($db, $aws_parsed, $config["aws"]["s3_bucket_name"]);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($files_to_retrieve);
			// var_dump(count($files_to_retrieve));
			// exit();
		}
	}

	/*
		Instantiate the RollingCurlX Class.
	*/
	try
	{
		$rcx = rcx_build_object($config["rolling_curl_x"]);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($rcx);
		}
	}

	/*
		Build requests from parsed listing.
	*/
	try
	{
		$rcx_build_request = rcx_build_request($rcx, $config["rolling_curl_x"], $config["aws"], $files_to_retrieve);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($rcx_build_request);
			var_dump($rcx);
		}
	}

	/*
		Create array to hold result set of successfully retrieved files.
	*/
	try
	{
		$retrieved_files = create_downloads_array();
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($retrieved_files);
		}
	}

	/*
		Execute requests from parsed listing.
	*/
	try
	{
		$rcx_execute = rcx_execute($rcx, $config["received"], $config["storage"]);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($rcx_execute);
		}
	}

	/*
		Process retrieved files.
	*/
	try
	{
		$process_downloads = process_downloads($db, $retrieved_files, $config["dependencies"]["ffmpeg"], $config["dependencies"]["ffprobe"], $config["storage"]["directory"]);
	}
	catch (Exception $e)
	{
		if($config["debug_mode"] === true)
		{
			exception_error_output($e, $guid);
		}

		exit();
	}
	finally
	{
		if($config["debug_mode"] === true)
		{
			var_dump($process_downloads);
		}
	}

?>