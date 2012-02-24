<?php

class Application_Form_ExamCourses extends Zend_Form
{
    protected $_elementCourse = null;
    protected $_elementSemester = null;
    protected $_elementLecturer = null;
    protected $_elementExamType = null;
    protected $_elementDegree = null;
    

    public function init()
    {
        $this->setMethod('post');
        $this->setAction('/exams/search');
 
        //
        $this->_elementCourse = new Zend_Form_Element_Multiselect('course');
        $this->_elementCourse->setAttrib('size', '10')
                             ->setRequired(true)
                             ->setLabel('Vorlesung');
        $this->setCourseOptions(array());
        $this->addElement($this->_elementCourse);
        
        //
        $this->_elementLecturer = new Zend_Form_Element_Multiselect('lecturer');
        $this->_elementLecturer->setAttrib('size', '10')
                               ->setRequired(true)
                               ->setLabel('Dozent');
        $this->setLecturerOptions(array());
        $this->addElement($this->_elementLecturer);
        
        //
        $this->_elementSemester = new Zend_Form_Element_Multiselect('semester');
        $this->_elementSemester->setAttrib('size', '10')
                               ->setRequired(true)
                               ->setLabel('Semester');
        $this->setSemesterOptions();
        $this->addElement($this->_elementSemester);
        
        //
        $this->_elementExamType = new Zend_Form_Element_Multiselect('examType');
        $this->_elementExamType->setAttrib('size', '5')
                               ->setRequired(true)
                               ->setLabel('Typ');
        $this->setExamTypeOptions();
        $this->addElement($this->_elementExamType);

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
    public function setCourseOptions($degree)
    {
        $options = array();
        if(!empty($degree)) {
            $courses = new Application_Model_CourseMapper();
            $entries = $courses->fetchByDegree($degree);
  
            foreach($entries as $group)
            {
               $options[$group->getId()] = $group->getName();
            } 
        }
    
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementCourse->setMultiOptions($opt);
    }
    
    public function setSemesterOptions()
    {
        $semesters = new Application_Model_SemesterMapper();
        $entries = $semesters->fetchAll();
        $options = array();

        foreach($entries as $group)
        {
           $options[$group->getId()] = $group->getName();
        }
  
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementSemester->setMultiOptions($opt);
        $this->_elementSemester->setValue(array('-1'));
    }
    
    public function setExamTypeOptions()
    {
        $types = new Application_Model_ExamTypeMapper();
        $entries = $types->fetchAll();
        $options = array();
        
        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        }
        
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamType->setMultiOptions($opt);
        $this->_elementExamType->setValue(array('-1'));
    }
    
    public function setLecturerOptions($degree)
    {   
        $options = array();
        if(!empty($degree)) {
            $lecturers = new Application_Model_LecturerMapper();
            $entries = $lecturers->fetchByDegree($degree);

            foreach($entries as $group)
            {
                $options[$group->getId()] = $group->getName();
            }
        }
    
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementLecturer->setMultiOptions($opt);
        $this->_elementLecturer->setValue(array('-1'));
    }
    
    public function setDegree($id)
    {
        $this->addElement('hidden', 'degree', array(
            'value' => $id,
        ));
    }

}

