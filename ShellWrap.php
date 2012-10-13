<?php

namespace MrRio;

class ShellWrap {
	
	private $output = array();

	public function __toString() {
		return implode("\n", $this->output) . "\n";
	}

	/**
	 * Check if array is associative, thanks to 
	 * http://stackoverflow.com/questions/173400
	 **/ 
	private function __isAssociative($array) {
		return array_keys($array) !== range(0, count($array) - 1);
	}

	private function __run($arguments) {

		// Unwind the args, figure out which ones were passed in as an array

		foreach($arguments as $arg_key => $argument) {
			if (is_array($argument)) {
				if ($this->__isAssociative($argument)) {
					
					// Ok, so we're passing in arguments
					
					$output = '';

					foreach ($argument as $key => $val) {

						if ($output != '') {
							$output .= ' ';
						}

						// If you pass 'false', it'll ignore the arg altogether
						if ($val !== false) {
							// Figure out if it's a long or short commandline arg
							if (strlen($key) == 1) {
								$output .= '-';
							} else {
								$output .= '--';
							}
							$output .= $key;

							// If you just pass in 'true', it'll just add the arg
							if ($val !== true) {
								$output .= '=' . $val;
							} 

						}
					}

					$arguments[$arg_key] = $output;
				} else {

					// We're passing in an array, but it's not --key=val style

					$arguments[$arg_key] = implode(' ', $argument);
				}
			}
		} 

		$shell = implode(' ', $arguments);

		$output = array();
		$return_var = null;

		echo $shell;

		exec($shell, $output, $return_var);


		$this->output = $output;
	}

	// Raw arguments

	public function __invoke() {
		$arguments = func_get_args();
		$this->__run($arguments);
		return $this;
	}

	public function __call($name, $arguments) {
		array_unshift($arguments, $name);
		$this->__run($arguments);
		return $this;
	}

}