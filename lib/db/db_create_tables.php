<?php


	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("db_create_tables"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function db_create_tables($dbh)
		{
			/*
				Test input.
				http://php.net/manual/en/function.is-resource.php
			*/
			if(!is_object($dbh))
			{
				throw new Exception("This function requires a valid database connection. - [db_connect]");

				return false;
			}

			/*
				Test the database.
			*/
			try
			{
				/*
					Is the database empty?
				*/
				$query = $dbh->query("SELECT * FROM sqlite_master;");
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				// var_dump($result);

				if(count($result) === (int) 0)
				{
					/*
						Nothing returned from the query.
						Attempt to create the table.
					*/
					$sql = file_get_contents(QRY_PATH . "/create.sql");
					$result = $dbh->exec($sql);
					// var_dump($result);

					/*
						Verify the table's existence.
					*/
					$query = $dbh->query("SELECT * FROM sqlite_master WHERE type = 'table' AND name = 'data';");
					$result = $query->fetchAll(PDO::FETCH_ASSOC);
					// var_dump($result);
					if(is_array($result) === true)
					{
						if(count($result) === (int) 1)
						{
							$result = true;
						}
					}
					else
					{
						$result = false;
					}
				}
				else
				{
					/*
						Test for the table's existence.
					*/
					$query = $dbh->query("SELECT * FROM sqlite_master WHERE type = 'table' AND name = 'data';");
					$result = $query->fetchAll(PDO::FETCH_ASSOC);
					// var_dump($result);
					if(is_array($result) === true)
					{
						if(count($result) === (int) 1)
						{
							$result = true;
						}
						else
						{
							$result = false;
						}
					}
					else
					{
						$result = false;
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

				$error = "$error_msg - [$error_code] - [db_connect]";
				throw new Exception($error); 
			}
			finally
			{
				return $result;
			}
		}
	}
	
?>
