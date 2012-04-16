<?php

class Application_Form_AdminDegreeGroup extends Zend_Form
{
	protected $_group = null;
	protected $_groupSelect = null;
	protected $_groupAdd = null;
	protected $_degree = null;
	protected $_newDegree = null;

    public function init()
    {
    	$this->setMethod('post');
    	
    	$this->_group = new Zend_Form_Element_MultiCheckbox('group');
    	
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select a group to edit');
    	    	
    	$this->setMultiOptions();
    	
    	$this->addElement($this->_group);
    	
    	// Add the submit button
    	$this->_groupSelect = new Zend_Form_Element_Submit('select');
    	$this->_groupSelect->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Delete',
    	));
    	$this->addElement($this->_groupSelect);
    	
    	
    	$this->_newDegree = new Zend_Form_Element_Text('newElement');
    	$this->_newDegree->setOptions(array(
    			'label'    => 'new group name:',
    			'required'	=> true,
    			'validators' => array(
                    				  array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newDegree);
    	
    	$this->_groupAdd = new Zend_Form_Element_Submit('add');
    	$this->_groupAdd->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Add',
    	));
    	$this->addElement($this->_groupAdd);
   	
    }
    
    public function setMultiOptions()
    {
    	$groups = new Application_Model_DegreeGroupMapper();
    	$entries = $groups->fetchAll();
    	 
    	$options = array();
    	 
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName();
    	}
    
    	$this->_group->setMultiOptions($options);
    }

}

