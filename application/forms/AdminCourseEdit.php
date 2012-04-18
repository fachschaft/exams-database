<?php

class Application_Form_AdminCourseEdit extends Zend_Form
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
    	 
    	$this->_course = new Zend_Form_Element_Select('select_course');
    	 
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
    
    public function setCourseId($courseId)
    {
    	$this->getElement('select_course')->setValue($courseId);
    	 
    	$this->addElement('hidden', 'select_course_2', array(
    			'value' => $courseId,
    			'decorators' => $this->_decoratorHidden,
    	));
    }
    
    public function showEdit($corsName, $selectedDegree = -1, $selectedCourse = -1)
    {
    	$this->displayEditCourse($corsName);
    	
    	$this->addElement(new Custom_Form_Element_PlainText('text1', array('value'=>'Select degree')));
    	
    	$this->displayDegrees($selectedDegree);
    	
    	$this->addElement(new Custom_Form_Element_PlainText('text2', array('value'=>'Select connected course')));
    	
    	$this->displayConnectedCourse($selectedCourse);
    	 
    	$this->removeElement('select_delete');
    	 
    	 
    	$this->getElement('select_course')->setAttrib('disabled', 'disabled');
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
    
    public function displayEditCourse($old_name)
    {
    	$this->_newElement = new Zend_Form_Element_Text('new_course_name');
    	$this->_newElement->setOptions(array(
    			'label'    => 'new course name:',
    			'required'	=> true,
    			'value'		=> $old_name,
    			'validators' => array(
    					array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newElement);
    }
    
    public function displayDegrees($selectedDegree)
    {
    	$this->_group = new Zend_Form_Element_Multiselect('select_degrees');
    
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select degrees');
    	$this->_group->addValidator('NotEmpty', true);
    	 
    	$this->setDegrees($this->_group);
    	 
    	
    	if(is_array($selectedDegree)) { $this->_group->setValue($selectedDegree); } else { $this->_group->setValue(array($selectedDegree)); }
    	 
    	 
    
    	$this->addElement($this->_group);
    }
    
    public function displayConnectedCourse($selectedCourse)
    {
    	$this->_connected = new Zend_Form_Element_Multiselect('select_connected_course');
    
    	$this->_connected->setAttrib('size', '3');
    	$this->_connected->setRequired(false);
    	$this->_connected->setAttrib('label','Select connected course');
    	$this->_connected->addValidator('NotEmpty', true);
    
    	$this->setConnectedCourse($this->_connected);
    
    	 
    	if(is_array($selectedCourse)) {
    		$this->_connected->setValue($selectedCourse);
    	} else { $this->_connected->setValue(array($selectedCourse));
    	}
    
    
    
    	$this->addElement($this->_connected);
    }
    
    private function setConnectedCourse($element)
    {
    	$groups = new Application_Model_CourseMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName();
    	}
    
    	$element->setMultiOptions($options);
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
    
    private function setCourse($element)
    {
    	$groups = new Application_Model_CourseMapper();
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

