<?
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

class Custom_Form_Element_PlainText extends Zend_Form_Element_Xhtml
{
    public $helper = 'formPlainText';
 
    public function isValid($value){
        return true;
    }
}
?>