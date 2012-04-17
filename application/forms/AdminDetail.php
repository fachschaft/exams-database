<?php

class Application_Form_AdminDetail extends Zend_Form
{

    protected $_elementCourse = null;
    protected $_elementSemester = null;
    protected $_elementLecturer = null;
    protected $_elementExamType = null;
    protected $_elementDegree = null;
    protected $_elementExamSubType = null;
    protected $_elementUniversity = null;
	protected $_elementComment = null;
	protected $_elementAutor = null;

	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);
	
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('/exams-admin/editdetails');
 
        //
        $this->_elementCourse = new Zend_Form_Element_Multiselect('course');
        $this->_elementCourse->setAttrib('size', '10')
                             ->setRequired(true)
                             ->setLabel('Vorlesung');
        //$this->setCourseOptions();
        $this->addElement($this->_elementCourse);
        
        //
        $this->_elementLecturer = new Zend_Form_Element_Multiselect('lecturer');
        $this->_elementLecturer->setAttrib('size', '10')
                               ->setRequired(true)
                               ->setLabel('Dozent');
        //$this->setLecturerOptions(array());
        $this->addElement($this->_elementLecturer);
        
        //
        $this->_elementSemester = new Zend_Form_Element_Select('semester');
        $this->_elementSemester->setAttrib('size', '10')
                               ->setRequired(true)
                               ->setLabel('Semester');
        $this->setSemesterOptions();
        $this->addElement($this->_elementSemester);
        
        //
        $this->_elementExamType = new Zend_Form_Element_Select('type');
        $this->_elementExamType->setAttrib('size', '5')
                               ->setRequired(true)
                               ->setLabel('Typ');
        $this->setExamTypeOptions();
        $this->addElement($this->_elementExamType);
        
		//
		$this->_elementAutor = new Zend_Form_Element_Text('autor');
		$this->_elementAutor->setLabel('Autor');
        $this->addElement($this->_elementAutor);
        
        //
        $this->_elementDegree = new Zend_Form_Element_Select('degree_exam');
        $this->_elementDegree->setRequired(true)
                               ->setLabel('Degree');
        $this->setExamDegreeOptions();
        $this->addElement($this->_elementDegree);
        
        //
        $this->_elementExamSubType = new Zend_Form_Element_Select('subType');
        $this->_elementExamSubType->setRequired(true)
                               ->setLabel('Solution Type');
        $this->setExamSubType();
        $this->addElement($this->_elementExamSubType);
        
        //
        $this->_elementUniversity = new Zend_Form_Element_Select('university');
        $this->_elementUniversity->setRequired(true)
                               ->setLabel('University');
        $this->setExamUniversityOptions();
        $this->addElement($this->_elementUniversity);
        
		//
		$this->_elementComment = new Zend_Form_Element_Textarea('comment');
		$this->_elementComment->setLabel('Comment');
        $this->addElement($this->_elementComment);
		
        
        $this->addElement('hidden', 'step', array(
            'value' => '2',
        	'decorators' => $this->_decoratorHidden,
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
	public function setCourseOptions(Application_Model_Degree $degree, array $courses = array())
    {
        $options = array();
       
        $coursesMapper = new Application_Model_CourseMapper();
        $entries = $coursesMapper->fetchByDegree($degree);
  
        foreach($entries as $group)
        {
           $options[$group->getId()] = $group->getName();
        } 
    
        $opt = array();
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementCourse->setMultiOptions($opt);
        $selected = array();
        foreach($courses as $course) { $selected[] = $course->id; }
		$this->_elementCourse->setValue($selected);
    }
    
    public function setSemesterOptions($selected = array())
    {
        $semesters = new Application_Model_SemesterMapper();
        $entries = $semesters->fetchAll();
        $options = array();

        foreach($entries as $group)
        {
           $options[$group->getId()] = $group->getName();
        }
  
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementSemester->setMultiOptions($opt);
        $this->_elementSemester->setValue($selected);
    }
    
    public function setExamTypeOptions($selected = array())
    {
        $types = new Application_Model_ExamTypeMapper();
        $entries = $types->fetchAll();
        $options = array();
        
        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        }
        
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamType->setMultiOptions($opt);
        $this->_elementExamType->setValue($selected);
    }
    
    public function setExamSubType($selected = array())
    {
        $types = new Application_Model_ExamSubTypeMapper();
        $entries = $types->fetchAll();
        $options = array();
        
        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        }
        
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamSubType->setMultiOptions($opt);
        $this->_elementExamSubType->setValue($selected);
    }
    
    public function setLecturerOptions(Application_Model_Degree $degree, array $lecturers = array())
    {   
        $options = array();

        $lecturersMapper = new Application_Model_LecturerMapper();
        $entries = $lecturersMapper->fetchByDegree($degree);

        foreach($entries as $group)
        {
           $options[$group->getId()] = $group->getName();
        }
        $opt = array();
        foreach($options as $id => $o) { $opt[$id] = $o; }
        if($opt != null) $this->_elementLecturer->setMultiOptions($opt);
        $selected = array();
        foreach($lecturers as $lecturer) { $selected[] = $lecturer->id; }
        $this->_elementLecturer->setValue($selected);
    }
    
    public function setExamDegreeOptions($selected = array())
    {
        $options = array();
        $courses = new Application_Model_ExamDegreeMapper();
        $entries = $courses->fetchAll();

        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        } 
    
        $opt = array();
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementDegree->setMultiOptions($opt);
		$this->_elementDegree->setValue($selected);
    }
    
    public function setExamUniversityOptions($selected = array())
    {
        $options = array();
        $courses = new Application_Model_ExamUniversityMapper();
        $entries = $courses->fetchAll();
  
        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        }
    
        $opt = array();
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementUniversity->setMultiOptions($opt);
		$this->_elementUniversity->setValue($selected);
    }
    
    public function setDegree($id)
    {
        $this->addElement('hidden', 'degree', array(
            'value' => $id,
        	'decorators' => $this->_decoratorHidden,
        ));
    }
	
	public function setExamId($id)
    {
        $this->addElement('hidden', 'exam_id', array(
            'value' => $id,
        	'decorators' => $this->_decoratorHidden,
        ));
    }
	
	public function setExamComment($text) 
	{
		$this->_elementComment->setValue($text);
	}
	
	public function setExamAutor($text) 
	{
		$this->_elementAutor->setValue($text);
	}

}