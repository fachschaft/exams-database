<?php

class Application_Model_DbTable_ExamHasLecturer extends Zend_Db_Table_Abstract
{

    protected $_name = 'exam_has_lecturer';
    protected $_primary = array('exam_idexam', 'lecturer_idlecturer');
    
    protected $_referenceMap    = array(
    		'Lecturer' => array(
    				'columns'           => 'lecturer_idlecturer',
    				'refTableClass'     => 'Application_Model_DbTable_Lecturer',
    				'refColumns'        => 'idlecturer'
    		),
    		'Exam' => array(
    				'columns'           => 'exam_idexam',
    				'refTableClass'     => 'Application_Model_DbTable_Exam',
    				'refColumns'        => 'idexam'
    		)
    );


}

