<?
require_once 'Zend/View/Helper/FormElement.php';
     

class Custom_View_Helper_FormLink extends Zend_View_Helper_FormElement {

    public function formLink($name, $value = null, $attribs = null) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        if (null === $value) { $value = $name; }
		if($attribs['link'] == null) { $link = '#'; } else { $link = $attribs['link']; }
		
		return'<a href="'.$link.'">'.$this->view->escape($value).'</a>';
    }
}