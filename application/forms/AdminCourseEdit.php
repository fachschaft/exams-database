<?php

class Application_Form_AdminCourseEdit extends Zend_Form
{
	protected $_course = null;
	protected $_group = null;
	protected $_degreeSelect = null;
	protected $_degreeDelete = null;
	protected $_save = null;
	protected $_cancel = null;

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
    	));
    }
    
    public function showEdit($selectedDegree = -1)
    {
    	$this->displayDegrees($selectedDegree);
    	 
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
    
    public function displayDegrees($selectedDegree)
    {
    	$this->_group = new Zend_Form_Element_Multiselect('select_degrees');
    
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select one degree');
    	$this->_group->addValidator('NotEmpty', true);
    	 
    	$this->setDegrees($this->_group);
    	 
    	
    	if(is_array($selectedDegree)) { $this->_group->setValue($selectedDegree); } else { $this->_group->setValue(array($selectedDegree)); }
    	 
    	 
    
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

