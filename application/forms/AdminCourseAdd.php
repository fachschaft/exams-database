<?php

class Application_Form_AdminCourseAdd extends Zend_Form
{

	protected $_degrees = null;
	protected $_newElement = null;
	protected $_add = null;
	
    public function init()
    {
    	$this->setMethod('post');
    	 
    	
    	$this->_newElement = new Zend_Form_Element_Text('newElement');
    	$this->_newElement->setOptions(array(
    			'label'    => 'new course name:',
    			'required'	=> true,
    			'validators' => array(
    					array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newElement);
    	
    	
    	$this->_degrees = new Zend_Form_Element_Multiselect('degrees');
    	 
    	$this->_degrees->setAttrib('size', '3');
    	$this->_degrees->setRequired(true);
    	$this->_degrees->setAttrib('label','Select connected degrees');
    	
    	$this->setDegrees();
    	 
    	$this->addElement($this->_degrees);
    	 
    	// Add the submit button
    	$this->_add = new Zend_Form_Element_Submit('add');
    	$this->_add->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Add',
    	));
    	$this->addElement($this->_add);
    }
    
    private function setDegrees()
    {
    	$groups = new Application_Model_DegreeMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName();
    	}
    
    	$this->_degrees->setMultiOptions($options);
    }


}

