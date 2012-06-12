<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Custom_Formatter_EscapeSpecialChars
{
	static function escape($string)
	{
		$search  = array('&Auml;', '&Ouml;', '&Uuml;', '&auml;', '&ouml;', '&uuml;', '&szlig;');
		$replace = array('A', 'O', 'U', 'a', 'o', 'u', 's');
		$string = str_replace($search, $replace, $string);
		return $string;
	}
}

?>