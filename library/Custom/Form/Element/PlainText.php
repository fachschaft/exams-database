<?
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