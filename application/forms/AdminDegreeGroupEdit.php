<?php

class Application_Form_AdminDegreeGroupEdit extends Zend_Form
{
	protected $_course = null;
	protected $_group = null;
	protected $_connected = null;
	protected $_degreeSelect = null;
	protected $_degreeDelete = null;
	protected $_save = null;
	protected $_cancel = null;
	protected $_newElement = null;

public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);

    public function init()
    {
        $this->setMethod('post');
    	 
    	$this->_course = new Zend_Form_Element_Select('select_degree_group');
    	 
    	$this->_course->setAttrib('size', '1');
    	$this->_course->setRequired(true);
    	$this->_course->setAttrib('label','Select a course to edit');
    	
    	$this->setCourse($this->_course);
    	 
    	$this->addElement($this->_course);
    	
    	
    	$this->_degreeSelect = new Zend_Form_Element_Submit('select_button');
    	$this->_degreeSelect->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Edit',
    	));
    	$this->addElement($this->_degreeSelect);

    	
    	$this->_degreeDelete = new Zend_Form_Element_Submit('select_delete');
    	$this->_degreeDelete->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Delete',
    	));
    	$this->addElement($this->_degreeDelete);
    }
    
    public function setGroupId($groupId)
    {
    	$this->getElement('select_degree_group')->setValue($groupId);
    	 
    	$this->addElement('hidden', 'select_degree_group_2', array(
    			'value' => $groupId,
    			'decorators' => $this->_decoratorHidden,
    	));
    }
    
    public function showEdit($groupName)
    {
    	$this->displayEditGroup($groupName);
    	 
    	$this->removeElement('select_delete');

    	$this->getElement('select_button')->setAttrib('disabled', 'disabled');
    	 
    	$this->_save = new Zend_Form_Element_Submit('select_save');
    	$this->_save->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Save',
    	));
    	$this->addElement($this->_save);
    	 
    	$this->_cancel = new Zend_Form_Element_Submit('select_cancel');
    	$this->_cancel->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Cancel',
    	));
    	$this->addElement($this->_cancel);
    }
    
    public function displayEditGroup($old_name)
    {
    	$this->_newElement = new Zend_Form_Element_Text('new_group_name');
    	$this->_newElement->setOptions(array(
    			'label'    => 'new group name:',
    			'required'	=> true,
    			'value'		=> $old_name,
    			'validators' => array(
    					array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newElement);
    }
    
    
    
    private function setCourse($element)
    {
    	$groups = new Application_Model_DegreeGroupMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName();
    	}
    	
    	// for seperateing by degrees have a look: http://stackoverflow.com/questions/7232180/zend-form-select-optgroup-how-to-specify-id
    
    	$element->setMultiOptions($options);
    } 


}

