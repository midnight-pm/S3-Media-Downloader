<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("exception_cleanup"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function exception_cleanup()
		{
			global $log;
			global $config;
			global $guid;
			global $lock_file_handle;

			/*
				Only do this if there is an exception error.

				http://php.net/manual/en/function.error-get-last.php
				https://stackoverflow.com/a/4410769
			*/
			$error = error_get_last();

			if($error["type"] === E_ERROR)
			{
				/*
					http://php.net/manual/en/function.is-object.php
				*/
				if(is_object($log))
				{
					$log->write("[$guid] Caught Shutdown.");
				}

				if(is_resource($lock_file_handle))
				{
					/*
						Release lock and close pid file.
					*/
					try
					{
						if(is_object($log))
						{
							$log->write("[$guid] Releasing lock.");
						}
						release_exclusive_lock($config["process_lock"]["directory"], $config["process_lock"]["filename"], $config["process_lock"]["deletion"], $lock_file_handle);
					}
					catch (Exception $e)
					{
						/*
							http://php.net/manual/en/class.exception.php
							http://php.net/manual/en/exception.getmessage.php
						*/
						if(is_object($log))
						{
							exception_error_log($e, $guid);
						}
						if($config["debug_mode"] === true)
						{
							exception_error_output($e, $guid);
						}

						/*
							http://php.net/manual/en/function.exit.php
						*/
						exit();
					}
				}
			}
		}
	}

	/*
		http://php.net/manual/en/function.register-shutdown-function.php
	*/
	register_shutdown_function("exception_cleanup");

?>
