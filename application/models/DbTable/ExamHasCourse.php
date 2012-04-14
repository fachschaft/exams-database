<?php

class Application_Model_DbTable_ExamHasCourse extends Zend_Db_Table_Abstract
{

    protected $_name = 'exam_has_course';
    protected $_primary = array('exam_idexam', 'course_idcourse');
    
    protected $_referenceMap    = array(
    		'Exam' => array(
    				'columns'           => 'exam_idexam',
    				'refTableClass'     => 'Application_Model_DbTable_Exam',
    				'refColumns'        => 'idexam'
    		),
    		'Course' => array(
    				'columns'           => 'course_idcourse',
    				'refTableClass'     => 'Application_Model_DbTable_Course',
    				'refColumns'        => 'idcourse'
    		)
    );

}

