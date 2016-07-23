<?php



class ConfigReader {
	private $url;
	private $xml;
	private $errors;
	private $config;
	
	public function __construct($url) {
		$this->url = $url;

	}

	private function load() {
		$this->errors = array();
		$old_setting = libxml_use_internal_errors(true);
		$this->xml = simplexml_load_file($this->url);
		if (!$this->xml) {
			foreach (libxml_get_errors() as $error) {
				$this->errors[] = "Parsing error (line $error->line, column $error->column): $error->message";
			}
		}
		libxml_use_internal_errors($old_setting);
	}

/*
	private function xmlToArray($xml) {
		if ($xml->count()) {
			echo "Entering {$xml->getName()} with {$xml->count()} children\n";
			$arr = array();
			foreach ($xml->children() as $child) {
				echo "Looking at child: {$child->getName()}\n";
				if (array_key_exists($child->getName(), $arr)) {
					echo "Child node {$child->getName()} already exists. Adding duplicate node.\n";
					if (!is_array($arr[$child->getName()])) {
						var_dump($arr);
						$arr[$child->getName()] = array($arr[$child->getName()]);
						var_dump($arr);
					}
					$arr[$child->getName()][] = $this->xmlToArray($child);
					var_dump($arr);
				} else
					$arr[$child->getName()] = $this->xmlToArray($child);
			}
			echo "Returning " , var_export($arr, 1) , " for {$xml->getName()} with {$xml->count()} children\n";
			return $arr;
		} else {
			return "$xml";
		}
	}
*/

	function parse() {
		$this->config = array();
		$this->load();
		
//		return $this->xmlToArray($this->xml);
	}

	function getSimpleXML() {
		return $this->xml;
	}

	function getErrors() {
		return $this->errors;
	}

	function getProperty($name) {
		return isset($this->xml->$name) ? $this->xml->$name : NULL;
	}

	function hasProperty($name) {
		return isset($this->xml->$name);
	}

	function getOptions() {
		return $this->xml->options;
	}

	function hasOption($name) {
		foreach ($this->xml->options as $option) {
		file_put_contents(dirname(__FILE__)."/options.txt", print_r($option, true), FILE_APPEND);
			if ($option->name == $name) {
				return true;
			}
		}
		return false;
	}

	function getOption($name, $default = NULL) {
		foreach ($this->xml->options as $option) {
			if ($option->name == $name) {
				return $option->value;
			}
		}
		return $default;
	}

};


