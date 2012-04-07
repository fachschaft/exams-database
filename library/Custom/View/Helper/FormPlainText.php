<?
require_once 'Zend/View/Helper/FormElement.php';
     

class Custom_View_Helper_FormPlainText extends Zend_View_Helper_FormElement {

    public function formPlainText($name, $value = null, $attribs = null) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        if (null === $value) { $value = $name; }
         
        return $this->view->escape($value);
    }
}