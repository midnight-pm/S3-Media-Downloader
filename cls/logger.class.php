<?php

	/*
		http://php.net/manual/en/function.class-exists.php
	*/
	if(!class_exists("logger"))
	{
		/*
			http://php.net/manual/en/language.oop5.php
		*/
		class logger
		{
			// global $config;

			/*
				http://php.net/manual/en/function.date-default-timezone-set.php
			*/
			// date_default_timezone_set($config["logger"]["timezone"]);

			/*
				http://php.net/manual/en/language.oop5.visibility.php
			*/
			private $file_handle;
			private $file_dir;
			private $file_path;

			/*
				http://php.net/manual/en/language.oop5.decon.php
			*/

			public function __construct()
			{
				$this->open();
			}

			/*
				http://php.net/manual/en/functions.user-defined.php
			*/
			private function datetime()
			{
				/*
					https://stackoverflow.com/questions/17909871/getting-date-format-m-d-y-his-u-from-milliseconds/17909891
					https://stackoverflow.com/a/17909891

					http://php.net/manual/en/function.microtime.php
				*/
				$time = microtime(true);

				/*
					http://php.net/manual/en/function.sprintf.php
				*/
				$micro = sprintf("%06d",($time - floor($time)) * 1000000);

				/*
					http://php.net/manual/en/datetime.construct.php
					http://php.net/manual/en/function.date.php
				*/
				$date = new DateTime(date("Y-m-d H:i:s\." . $micro, $time));
				$datetime = $date->format("Y-m-d\THis\.uO");

				return $datetime;
			}

			private function open()
			{
				/*
					Bring $config array into scope.
				*/
				global $config;

				/*
					Verify required values are not empty.
				*/
				if(empty($config["logger"]["log_dir_path"]))
				{
					throw new Exception ("The directory where logs files should be stored has not been properly defined in the configuration file.");
				}
				if(empty($config["logger"]["log_file_name_prexfix"]))
				{
					throw new Exception ("The log file name has not been properly defined in the configuration file.");
				}
				if(empty($config["logger"]["log_dir_permissions"]))
				{
					throw new Exception ("The parameter for directory permissions has not been properly defined in the configuration file.");
				}
				if(empty($config["logger"]["timezone"]))
				{
					throw new Exception ("A timezone has not been properly defined in the configuration file.");
				}

				/*
					Override php.ini.
				*/
				if(!date_default_timezone_set($config["logger"]["timezone"]))
				{
					throw new Exception ("An error was encountered while attempting to define the timezone using the provided configuration parameter.");
				}

				/*
					Set variable values for further usage throughout class.
				*/
				$log_dir_path = $config["logger"]["log_dir_path"];
				$log_dir_path_year = date('Y', time());
				$log_dir_path_month = date('m', time());
				$log_dir_path_day = date('d', time());

				$log_file_name_prefix = $config["logger"]["log_file_name_prexfix"];

				$log_file_name_timestamp = $this->datetime();

				$log_full_path_dir = "$log_dir_path/$log_dir_path_year/$log_dir_path_month/$log_dir_path_day";
				$log_full_path_file = "$log_dir_path/$log_dir_path_year/$log_dir_path_month/$log_dir_path_day/$log_file_name_prefix-$log_file_name_timestamp.log";

				$this->file_dir = $log_full_path_dir;
				$this->file_path = $log_full_path_file;

				/*
					http://php.net/manual/en/function.file-exists.php
				*/
				if(!file_exists($this->file_dir))
				{
					/*
						http://php.net/manual/en/function.mkdir.php
					*/
					if(!mkdir($this->file_dir, $config["logger"]["log_dir_permissions"], true))
					{
						throw new Exception ("Failed to create directory \"" . $this->file_dir . "\".");
					}
				}

				/*
					http://php.net/manual/en/function.fopen.php
				*/
				if(!$this->file_handle = fopen($this->file_path,"a"))
				{
					throw new Exception ("Failed to open file handle for \"" . $this->file_path . "\".");
				}
			}

			public function write($input)
			{
				/*
					http://php.net/manual/en/function.is-resource.php
				*/
				if(!is_resource($this->file_handle))
				{
					$this->open();
				}

				$timestamp = date('Y-m-d H:i:s O', time());

				/*
					http://php.net/manual/en/function.fwrite.php
				*/
				if(fwrite($this->file_handle, PHP_EOL . "$timestamp - $input") === FALSE)
				{
					throw new Exception ("Error encountered when attempting to write to \"" . $this->file_path . "\".");
				}
			}
			
			public function close()
			{
				if(is_resource($this->file_handle))
				{
					/*
						http://php.net/manual/en/function.fclose.php
					*/
					fclose($this->file_handle);
				}
			}
		}
	}

?>
