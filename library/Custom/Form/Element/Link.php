<?
/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

class Custom_Form_Element_Link extends Zend_Form_Element_Xhtml
{
    public $helper = 'formLink';
 
    public function isValid($value){
        return true;
    }
}
?>