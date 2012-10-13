<?php

namespace MrRio;

class ShellWrap {
	
	public function __toString() {

		return "This\nis\nsome\nshell\noutput";
	}

	// Raw arguments
	public function __invoke($shell) {


		
	}

	public function __call($name, $arguments) {

		
		return $this;
	}



}