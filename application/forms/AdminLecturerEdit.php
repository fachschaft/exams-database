<?php

class Application_Form_AdminLecturerEdit extends Zend_Form
{
	protected $_lecturer = null;
	protected $_group = null;
	protected $_connected = null;
	protected $_degreeSelect = null;
	protected $_degreeDelete = null;
	protected $_save = null;
	protected $_cancel = null;
	protected $_newElement = null;
	protected $_newElementDegree = null;
	protected $_newElementFirstName = null;
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);
	
    public function init()
    {
    	$this->setMethod('post');
    	
    	$this->_lecturer = new Zend_Form_Element_Select('select_lecturer');
    	
    	$this->_lecturer->setAttrib('size', '1');
    	$this->_lecturer->setRequired(true);
    	$this->_lecturer->setAttrib('label','Select a course to edit');
    	 
    	$this->setLecturer($this->_lecturer);
    	
    	$this->addElement($this->_lecturer);
    	 
    	 
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
    
    public function setLecturerId($lecturerId)
    {
    	$this->getElement('select_lecturer')->setValue($lecturerId);
    
    	$this->addElement('hidden', 'select_lecturer_2', array(
    			'value' => $lecturerId,
    			'decorators' => $this->_decoratorHidden,
    	));
    }
    
    public function showEdit($lecturerName, $lecturerDegee, $lecturerFurstname, $selectedDegree = -1)
    {
    	$this->displayEditLecturer($lecturerName, $lecturerDegee, $lecturerFurstname);
    	 
    	$this->addElement(new Custom_Form_Element_PlainText('text1', array('value'=>'Select degrees')));
    	 
    	$this->displayDegrees($selectedDegree); 
    
    	$this->removeElement('select_delete');
    
    
    	$this->getElement('select_lecturer')->setAttrib('disabled', 'disabled');
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
    
    public function displayEditLecturer($old_name, $old_degree, $old_first_name)
    {
    	$this->_newElement = new Zend_Form_Element_Text('new_lecturer_name');
    	$this->_newElement->setOptions(array(
    			'label'    => 'new lecturer name:',
    			'required'	=> true,
    			'value'		=> $old_name,
    			'validators' => array(
    					array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newElement);
    	
    	$this->_newElementDegree = new Zend_Form_Element_Text('newElementDegree');
    	$this->_newElementDegree->setOptions(array(
    			'label'    => 'degree (e.g. Prof. Dr.):',
    			'required'	=> true,
    			'value'		=> $old_degree,
    	));
    	$this->addElement($this->_newElementDegree);
    	 
    	$this->_newElementFirstName = new Zend_Form_Element_Text('newElementFirstName');
    	$this->_newElementFirstName->setOptions(array(
    			'label'    => 'first name:',
    			'value'		=> $old_first_name,
    	));
    	$this->addElement($this->_newElementFirstName);
    }
    
    
    
    
    public function displayDegrees($selectedDegree)
    {
    	$this->_group = new Zend_Form_Element_Multiselect('select_degrees');
    
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select degrees');
    	$this->_group->addValidator('NotEmpty', true);
    
    	$this->setDegrees($this->_group);
    
    	 
    	if(is_array($selectedDegree)) {
    		$this->_group->setValue($selectedDegree);
    	} else { $this->_group->setValue(array($selectedDegree));
    	}
    
    
    
    	$this->addElement($this->_group);
    }
    
    private function setDegrees($element)
    {
    	$degreeMapper = new Application_Model_DegreeMapper();
    	$entries = $degreeMapper->fetchAll();
    	$options = array();
    
    	foreach($entries as $degree)
    	{
    		$options[$degree->getId()] = $degree->getName();
    	}
    
    	$element->setMultiOptions($options);
    }
    
    private function setLecturer($element)
    {
    	$groups = new Application_Model_LecturerMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group;
    	}
   
    	$element->setMultiOptions($options);
    }


}

