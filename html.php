<?php

/** 
 * Builds html-tags from data and method-name.
 * @name html
 * @author marcus
 */
class html {
	
	/**
	 * The constant name of this class.
	 * @var string
	 */
	const classname = "html";
	
	const tag_start = "start";
	const tag_end = "end";
	const tag_self = "self";
	
	/**
	 * Singleton pattern:
	 * Private  constructor.
	 */
	private function __construct() {}
	
	/**
	 * Captures any and all static invokings of functions on this class and builds the html-tag corresponding to the invoked function.
	 * @param string $name The name of the method/function invoked.
	 * @param array $args An array containing the parameters used when invoking the method/function.
	 * @return string The built tag
	 * @example
	 * $paragraph = html::p($data, $attribute, $tag_type);
	 * $paragraph:
	 * <p >$data
	 */
	public static function __callStatic($name, $args) {

		$func_array = array(self::classname, $name);
		
		if (method_exists(self::classname, $name) && is_callable($func_array)) {
			
			return call_user_func($func_array, $args);
		}
		else {

			$data = null;
			$attr = null;
			$type = null;
			$return = null;
			
			switch (count($args)) {
				default:
				case 0:
					$type = self::tag_start;
					break;
				
				case 1:
					$data = array_shift($args);
					break;
				case 2:
					$data = array_shift($args);
					$attr = array_shift($args);
					break;
				case 3:
					$data = array_shift($args);
					$attr = array_shift($args);
					$type = array_shift($args);
					break;
			}

			if (is_array($data)) {
				
				foreach ($data as $value) {

					$return .= self::$name($value, $attr, $type);
				}
				
				return $return;
			}

			switch ($type) {
				
				case self::tag_start:
					return self::builder($name);
					break;
				case self::tag_end:
					return self::builder($name, false, true);
					break;
				case self::tag_self:
					return self::builder($name, true, false, $attr);
					break;
				
				default:
					$return .= self::builder($name, false, false, $attr);
					$return .= $data;
					$return .= self::builder($name, false, true);
					break;
			}

			return $return;
		}
	}
	
	/**
	 * Builds an html-tag with the specified attribute and name.
	 * @param string $name The name of the tag
	 * @param boolean $is_enclosed Indicates whether the tag should be self-enclosing or not
	 * @param boolean $is_end Indicates whether the tag should be the closing end.
	 * @param string $attr The attribute of the opening and/or self-enclosing tag.
	 * @return string A string 
	 */
	private static function builder($name, $is_enclosed = false, $is_end = false, $attr = null) {
		
		if ($is_enclosed) {
			
			return '<' . $name . ' ' . self::gAttr($attr) . '/>';
		}
		elseif($is_end) {
			
			return '</' . $name . '>';
		}
		else {
			
			return '<' . $name . ' ' . self::gAttr($attr) . '>';
		}
	}
	
	/**
	 * Creates a tag attribute from the given array.
	 * @param array $attr Key represents the attribute name, and the Value the attribute data.
	 * @return string The attribute string if $attr is an array, null otherwise.
	 */
	private static function gAttr($attr) {
		
		if (is_array($attr)) {
			$return = null;
			
			foreach ($attr as $name => $data) {
			
				if ($data == null || $data == "") {
	
					continue;
				}

				$return .= $name . '="' . $data . '" ';
			}
			
			return $return;
		
		}
		else {
			
			return null;
		}
	}

	/**
	* t_form
	* Template function
	*/
	public static function t_form($name, $action, $method, $data, $cattr = null) {

		$attr =  
			array(
				"name" 	 => $name,
				"action" => $action,
				"method" => $method);

		if (is_array($cattr )) {

			$cattr = array_merge(
				$cattr,
				$attr);
		}
		else {
			
			$cattr = $attr;
		}

		return self::form(
			$data,
			$cattr
		);
	}

	public static function t_js($file, $inline = "") {
		
		return self::script(
			$inline,
			array(
				"type"	=> "text/javascript",
				"src"	=> $file
			));
	}
}

?>
