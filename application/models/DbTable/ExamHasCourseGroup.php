<?php

class Application_Model_DbTable_ExamHasCourseGroup extends Zend_Db_Table_Abstract
{

    protected $_name = 'exam_has_course_group';
    
    protected $_primary = array('exam_idexam', 'course_group_idcourse_group');
    
    //protected $_dependentTables = array('Application_Model_DbTable_CourseGroup');
    
    protected $_referenceMap    = array(
        'Group' => array(
            'columns'           => 'course_group_idcourse_group',
            'refTableClass'     => 'Application_Model_DbTable_CourseGroup',
            'refColumns'        => 'idcourse_group'
        ),
        'Exam' => array(
            'columns'           => 'exam_idexam',
            'refTableClass'     => 'Application_Model_DbTable_Exam',
            'refColumns'        => 'idexam'
        )
    );

}

