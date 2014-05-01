<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Form_UploadDetail extends Application_Form_ExamTemplate
{

    protected $_elementCourse = null;
    protected $_elementSemester = null;
    protected $_elementLecturer = null;
    protected $_elementExamType = null;
    protected $_elementDegree = null;
    protected $_elementExamSubType = null;
    protected $_elementUniversity = null;


    public function init()
    {
 
        // Course selection box
        $this->_elementCourse = new Zend_Form_Element_Multiselect('course');
        $this->_elementCourse->setAttrib('size', '10')
                             ->setRequired(true)
                             ->setLabel('Vorlesung')
        					 ->registerInArrayValidator(false);
        $this->addElement($this->_elementCourse);
        $this->_elementCourse->setDecorators($this->_decoratorDiv);
        
        //Lecturer selection box
        $this->_elementLecturer = new Zend_Form_Element_Multiselect('lecturer');
        $this->_elementLecturer->setAttrib('size', '10')
                               ->setRequired(true)
                               ->setLabel('Dozent');
        $this->addElement($this->_elementLecturer);
        $this->_elementLecturer->setDecorators($this->_decoratorDiv);
        
        //Semester selection box
        $this->_elementSemester = new Zend_Form_Element_Select('semester');
        $this->_elementSemester->setAttrib('size', '10')
                               ->setRequired(true)
                               ->setLabel('Semester');
        $this->addElement($this->_elementSemester);
        $this->_elementSemester->setDecorators($this->_decoratorDiv);
        
        //Exam type dropdown
        $this->_elementExamType = new Zend_Form_Element_Select('type');
        $this->_elementExamType->setAttrib('size', '5')
                               ->setRequired(true)
                               ->setLabel('Typ');
        $this->addElement($this->_elementExamType);
        $this->_elementExamType->setDecorators($this->_decoratorDiv);
        
        //Exam author textbox
        $this->addElement('text', 'autor', array(
            'label'    => 'Autor',
            'required'   => false,
        	'decorators' => $this->_decoratorDiv,
            
        ));
        
        //Degree dropdown
        $this->_elementDegree = new Zend_Form_Element_Select('degree_exam');
        $this->_elementDegree->setRequired(true)
                               ->setLabel('Degree');
        $this->addElement($this->_elementDegree);
        $this->_elementDegree->setDecorators($this->_decoratorDiv);
        
        //Exam subtype dropdown
        $this->_elementExamSubType = new Zend_Form_Element_Select('subType');
        $this->_elementExamSubType->setRequired(true)
                               ->setLabel('Solution Type');
        $this->addElement($this->_elementExamSubType);
        $this->_elementExamSubType->setDecorators($this->_decoratorDiv);
        
        //University dropdown
        $this->_elementUniversity = new Zend_Form_Element_Select('university');
        $this->_elementUniversity->setRequired(true)
                               ->setLabel('University');
        $this->addElement($this->_elementUniversity);
        $this->_elementUniversity->setDecorators($this->_decoratorDiv);
        
        //comments text box
        $this->addElement('textarea', 'comment', array(
            'label'    => 'Comment',
            'required'   => false,
        	'decorators' => $this->_decoratorDiv,
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        	'decorators' => $this->_decoratorDivButton,
        ));
        
        
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
  
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementSemester->setMultiOptions($opt);
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
        
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamType->setMultiOptions($opt);
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
        
        foreach($options as $id => $o) { $opt[$id] = $o; }
        $this->_elementExamSubType->setMultiOptions($opt);
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
        	
        ));
    }
    
    


}

