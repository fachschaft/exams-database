<?php

class Application_Form_ExamCourses extends Zend_Form
{
    protected $_elementCourse = null;
    protected $_elementSemester = null;
    protected $_elementLecturer = null;
    protected $_elementExamType = null;
    

    public function init()
    {
       // Set the method for the display form to POST
        $this->setMethod('post');
 
        $this->_elementCourse = new Zend_Form_Element_Multiselect('course');
        
        $this->_elementCourse->setAttrib('size', '10')
                             ->setLabel('Vorlesung');
        $this->setCourseOptions(array());
        
        $this->addElement($this->_elementCourse);
        
        
        //
        $this->_elementLecturer = new Zend_Form_Element_Multiselect('lecturer');
        $this->_elementLecturer->setAttrib('size', '10')
                               ->setLabel('Dozent');

        $this->setLecturerOptions(array());
        
        $this->addElement($this->_elementLecturer);
        
        //
        $this->_elementSemester = new Zend_Form_Element_Multiselect('semester');
        $this->_elementSemester->setAttrib('size', '10')
                               ->setLabel('Semester');
        $this->setSemesterOptions(array());
        
        $this->addElement($this->_elementSemester);
        
        //
        $this->_elementExamType = new Zend_Form_Element_Multiselect('examType');
        $this->_elementExamType->setAttrib('size', '5')
                               ->setLabel('Typ');
        $this->setExamTypeOptions(array());
        
        $this->addElement($this->_elementExamType);
        
        
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
    public function setCourseOptions(array $options)
    {
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementCourse->setMultiOptions($opt);
        //$this->_elementCourse->setAttrib('selected', 'multiple');
    }
    
    public function setSemesterOptions(array $options)
    {
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementSemester->setMultiOptions($opt);
        $this->_elementSemester->setValue(array('-1'));
    }
    
    public function setExamTypeOptions(array $options)
    {
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamType->setMultiOptions($opt);
        $this->_elementExamType->setValue(array('-1'));
    }
    
    public function setLecturerOptions(array $options)
    {
        $opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementLecturer->setMultiOptions($opt);
        $this->_elementLecturer->setValue(array('-1'));
    }

}

