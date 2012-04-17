<?php

class Application_Form_AdminDegreeEdit extends Zend_Form
{
	protected $_degree = null;
	protected $_group = null;
	protected $_degreeSelect = null;
	protected $_degreeDelete = null;
	protected $_save = null;
	protected $_cancel = null;
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);

    public function init()
    {
    	$this->setMethod('post');
    	 
    	$this->_degree = new Zend_Form_Element_Radio('select_degree');
    	 
    	$this->_degree->setAttrib('size', '3');
    	$this->_degree->setRequired(true);
    	$this->_degree->setAttrib('label','Select a group to edit');
    	
    	$this->setGroups($this->_degree);
    	 
    	$this->addElement($this->_degree);
    	
    	
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
    
    public function setDegree($degreeId)
    {
    	$this->getElement('select_degree')->setValue($degreeId);
    	
    	$this->addElement('hidden', 'select_degree_2', array(
    			'value' => $degreeId,
    			'decorators' => $this->_decoratorHidden,
    	));
    }
    
    public function showEdit($selectedGroup = -1)
    {
    	$this->displayGroups($selectedGroup);
    	
    	$this->removeElement('select_delete');
    	
    	
    	$this->getElement('select_degree')->setAttrib('disabled', 'disabled');
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
    
    public function displayGroups($selectedGroup)
    {
    	$this->_group = new Zend_Form_Element_Select('select_group');
    	 
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select one degree');
    	
    	$this->setDegrees($this->_group);
    	
    	$this->_group->setValue(array($selectedGroup));
    	
    	
    	 
    	$this->addElement($this->_group);
    }
    
    private function setDegrees($element)
    {
    	$degreeMapper = new Application_Model_DegreeGroupMapper();
    	$entries = $degreeMapper->fetchAll();
    	$options = array();
    
    	foreach($entries as $degree)
    	{
    		$options[$degree->getId()] = $degree->getName();
    	}
    
    	$element->setMultiOptions($options);
    }
    
    private function setGroups($element)
    {
    	$groups = new Application_Model_DegreeMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName() . ' - ' . $group->group->name;
    	}
    
    	$element->setMultiOptions($options);
    }

}

