<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("exception_error_output"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function exception_error_output($exception, $guid)
		{
			global $log;

			$exception_emsg = $exception->getMessage();
			$exception_otpt = "[$guid] Exception Error: $exception_emsg";

			print $exception_otpt . PHP_EOL;

			/*
				http://php.net/manual/en/function.error-get-last.php
			*/
			$php_error = error_get_last();
			$php_error_type = $php_error["type"];
			$php_error_mesg = $php_error["message"];
			$php_error_file = $php_error["file"];
			$php_error_line = $php_error["line"];

			$php_error_otpt = "[$guid] Error: $php_error_mesg [$php_error_type] ($php_error_file\:$php_error_line)";

			print $php_error_otpt . PHP_EOL;

			/*
				http://php.net/manual/en/exception.gettraceasstring.php
			*/
			$exception_trce = str_replace("\n", PHP_EOL, $exception->getTraceAsString());
			$exception_trco = "[$guid] Stack Trace: " . $exception_trce;

			print $exception_trco . PHP_EOL;

			/*
				http://php.net/manual/en/function.debug-print-backtrace.php
					http://php.net/manual/en/function.ob-start.php
					http://php.net/manual/en/function.ob-get-clean.php
			*/
			ob_start();
			debug_print_backtrace();
			$debug = ob_get_clean();

			$debug_otpt = "[$guid] Debug: " . PHP_EOL . "$debug";

			print $debug_otpt . PHP_EOL;
		}
	}
?>
