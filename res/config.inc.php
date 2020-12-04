<?php

	$config = array(

		/*
			Enable/Disable Debug Mode
		*/
		"debug_mode" => false

		/*
			Process Locking
		*/
		, "process_lock" => array(
				"directory" => "/tmp"
				, "filename" => "s3_downloader.lock"
				, "deletion" => true
			)

		/*
			Dependencies
		*/
		, "dependencies" => array(
				/*
					Optional: Specify the full path to the aws client
							  i.e.: /usr/bin/aws
				*/
				"aws" => ""

				/*
					Optional: Specify the full path to ffmpeg
							  i.e.: /usr/bin/ffmpeg
				*/
				, "ffmpeg" => ""

				/*
					Optional: Specify the full path to ffprobe
							  i.e.: /usr/bin/ffprobe
				*/
				, "ffprobe" => ""
			)

		/*
			Database Configuration
		*/
		, "database" => array(
				"directory" => DB_PATH
				, "filename" => "mydb.sq3"
			)

		/*
			Amazon Configuration
		*/
		, "aws" => array(
				/*
					Specify the name of the Amazon S3 Bucket
				*/
				"s3_bucket_name" => ""
			)

		/*
			Received Data Paramaters
		*/
		, "received" => array(
				/*
					Where is received data stored?

					By default, it is set to utilize a directory named "received" in the script's location.
					In some cases, this may not need to be changed.

					This script will require permissions to read from and write into that directory.
				*/
				"directory" => "/data/received"
			)

		/*
			Data Storage Parameters
		*/
		, "storage" => array(
				/*
					Define a timezone to use for date related functions.
					http://php.net/manual/en/timezones.php
				*/
				"timezone" => "America/New_York"

				/*
					Where will addressed data be stored?

					By default, it is set to utilize a directory named "storage" in the script's location.
					In some cases, this may not need to be changed.

					This script will require permissions to read from and write into that directory.
				*/
				, "directory" => "/data/storage"

				/*
					This controls the permissions of the data storage directory.

					This will need to be defined using NUMERIC notation.
					In most cases, 0755 should suffice. Adjust accordingly as necessary.

					For further information, reference: 
					https://en.wikipedia.org/w/index.php?title=File_system_permissions&oldid=808567801#Numeric_notation

					Note: This setting has no effect when executed under Windows, but will still *need* to be set.
				*/
				, "permissions" => 0755
			)

		/*
			Configure RCX Paramters
		*/
		, "rolling_curl_x" => array(
				/*
					Set the maximum number of connections to use.
					This is an integer, and should be entered as numbers, with no surrounding quotes.
					Set between 1 and 10.
				*/
				"max_connections" => 10

				/*
					Set the timeout period.
					This is an integer, and should be entered as numbers, with no surrounding quotes.
					This value should be entered in milliseconds.
					1000 milliseconds (ms) is equal to 1 second (5000 ms = 5 seconds, 10000 = 10 seconds, etc.)
				*/
				, "timeout_period" => 300000

				/*
					Set the user agent to identify as.
				*/
				, "user_agent" => "Mozilla/5.0 (X11; Linux x86_64; rv:83.0) Gecko/20100101 Firefox/83.0"
		)

		/*
			Logging Parameters
		*/
		, "logger" => array(
				/*
					Define a timezone to use for date related functions.
					http://php.net/manual/en/timezones.php
				*/
				"timezone" => "America/New_York"

				/*
					Where will log files be stored?
					In some cases, this may not need to be changed.
					By default, it is set to create a directory named "logs" in the script's location.
					If this script does not have permissions to do so, a directory will need to be created for it.
					This script will require permissions to write into that directory.
				*/
				, "log_dir_path" => "/tmp"

				/*
					This controls the permissions of the log directory.

					This will need to be defined using NUMERIC notation.
					In most cases, 0755 should suffice. Adjust accordingly as necessary.

					For further information, reference: 
					https://en.wikipedia.org/w/index.php?title=File_system_permissions&oldid=808567801#Numeric_notation

					Note: This setting has no effect when executed under Windows, but will still *need* to be set.
				*/
				, "log_dir_permissions" => 0755

				/*
					This identifies the prefix of the file name for the log files.
				*/
				, "log_file_name_prexfix" => "s3_downloader"
			)
	);

?>
