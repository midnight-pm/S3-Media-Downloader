<?php


	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("db_connect"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function db_connect($db_config)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-array.php
			*/
			if(!is_array($db_config))
			{
				throw new Exception("Input is invalid. - [db_connect]");

				return false;
			}
			else
			{
				/*
					Test input to ensure that it is not empty.
				*/
				if(empty($db_config["directory"]))
				{
					throw new Exception("Missing database location. - [db_connect]");

					return false;
				}
				if(empty($db_config["filename"]))
				{
					throw new Exception("Missing database filename. - [db_connect]");

					return false;
				}
			}

			/*
				Set location of file.
			*/
			$db_location = $db_config["directory"] . "/" . $db_config ["filename"];

			if(file_exists($db_location) === false)
			{
				throw new Exception("Specified database location of \"$db_location\" does not appear to exist.");
			}
			else
			{
				if(is_readable($db_location) === false)
				{
					throw new Exception("Permission error: The file \"$db_location\" does not appear to be readable.");
				}
				else
				{
					if(is_writable($db_location) === false)
					{
						throw new Exception("Permission error: The file \"$db_location\" does not appear to be writeable.");
					}
				}
			}

			/*
				http://php.net/manual/en/pdo.getavailabledrivers.php
			*/
			$available_pdo_drivers = PDO::getAvailableDrivers();

			/*
				Confirm availability of needed SQL driver.

				http://php.net/manual/en/function.in-array.php
			*/
			if(in_array("sqlite", $available_pdo_drivers))
			{
				/*
					https://php.net/manual/en/ref.pdo-sqlite.connection.php
				*/
				$data_source_name = "sqlite:" . $db_location;
			}
			else
			{
				throw new Exception("Could not find the necessary SQL driver. There is no SQL driver available for SQLITE. - [db_connect]");

				return false;
			}

			/*
				Attempt a connection.
			*/
			try
			{
				/*
					http://php.net/manual/en/pdo.construct.php
				*/
				$connection = new PDO($data_source_name);

				/*
					https://www.php.net/manual/en/pdo.setattribute.php
				*/
				$connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			}
			/*
				Use the PDOException handler.
				http://php.net/manual/en/class.pdoexception.php
			*/
			catch(PDOException $e)
			{
				$error_msg = $e->getMessage();
				$error_code = $e->getCode();

				$error = "$error_msg - [$error_code] - [db_connect]";
				throw new Exception($error); 

				return false;
			}

			return $connection;
		}
	}
	
?>
