<?php

	/*
		http://php.net/manual/en/function.function-exists.php
	*/
	if(!function_exists("exception_handler"))
	{
		/*
			http://php.net/manual/en/functions.user-defined.php
		*/
		function exception_handler($exception)
		{
			/*
				http://php.net/manual/en/language.exceptions.php
			*/
			$exception_class = get_class($exception);
			$exception_message = $exception->getMessage();
			$exception_file = $exception->getFile();
			$exception_line = $exception->getLine();
			$exception_trace = $exception->getTraceAsString();

			print "FATAL ERROR:" . PHP_EOL;
			print "Uncaught exception \"$exception_class\"." . PHP_EOL;
			print "Message: $exception_message" . PHP_EOL;
			print "Exception thrown in \"$exception_file\" on line \"$exception_line\"." . PHP_EOL;
			print PHP_EOL;
			print "Stack Trace: ". PHP_EOL;
			print "$exception_trace" . PHP_EOL;
		}

		/*
			http://php.net/manual/en/function.set-exception-handler.php
		*/
		set_exception_handler("exception_handler");
	}

?>
