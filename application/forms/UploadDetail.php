<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Form_UploadDetail extends Zend_Form
{

    protected $_elementCourse = null;
    protected $_elementSemester = null;
    protected $_elementLecturer = null;
    protected $_elementExamType = null;
    protected $_elementDegree = null;
    protected $_elementExamSubType = null;
    protected $_elementUniversity = null;
    
    public $_decoratorHidden = array(
    		'ViewHelper',
    		array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
    );

    public function init()
    {
        $this->setMethod('post');
        $this->setAction('/exams/upload');
 
        //
        $this->_elementCourse = new Zend_Form_Element_Multiselect('course');
        $this->_elementCourse->setAttrib('size', '10')
                             ->setRequired(true)
                             ->setLabel('Vorlesung');
        //$this->setCourseOptions(array());
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
        $this->addElement('text', 'autor', array(
            'label'    => 'Autor',
            'required'   => false,
            
        ));
        
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
        
        $this->addElement('textarea', 'comment', array(
            'label'    => 'Comment',
            'required'   => false,
        ));
        
        $this->addElement('hidden', 'step', array(
            'value' => '2',
        	'decorators' => $this->_decoratorHidden,
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
        
        
        //$this->setMultiOptions();
    }
    
    public function setCourseOptions(Application_Model_Degree $degree)
    {
        $options = array();
        $courses = new Application_Model_CourseMapper();
        $entries = $courses->fetchByDegree($degree);
 
        foreach($entries as $group)
        {
           $options[$group->getId()] = $group->getName();
        }
    
        $opt = array();
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
  
        //$opt = array('-1'=>'- other -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementSemester->setMultiOptions($opt);
        //$this->_elementSemester->setValue(array('-1'));
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
        
        //$opt = array('-1'=>'- other -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamType->setMultiOptions($opt);
        //$this->_elementExamType->setValue(array('-1'));
    }
    
    public function setExamSubType()
    {
        $types = new Application_Model_ExamSubTypeMapper();
        $entries = $types->fetchAll();
        $options = array();
        
        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        }
        
        //$opt = array('-1'=>'- all -');
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamSubType->setMultiOptions($opt);
        //$this->_elementExamSubType->setValue(array('-1'));
    }
    
    public function setLecturerOptions(Application_Model_Degree $degree)
    {   
        $options = array();
        $lecturers = new Application_Model_LecturerMapper();
        $entries = $lecturers->fetchByDegree($degree);

        foreach($entries as $group)
        {
            $options[$group->getId()] = $group;
        }
        
    
        $opt = array();
        foreach($options as $id => $o) { $opt[$id] = $o; }
        if($opt != null) $this->_elementLecturer->setMultiOptions($opt);
        //$this->_elementLecturer->setValue(array('-1'));
    }
    
    public function setExamDegreeOptions()
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
    }
    
    public function setExamUniversityOptions()
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
    }
    
    public function setDegree($id)
    {
        $this->addElement('hidden', 'degree', array(
            'value' => $id,
        	'decorators' => $this->_decoratorHidden,
        ));
    }
    
    


}

