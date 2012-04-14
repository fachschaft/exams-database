<?php

class Application_Model_DbTable_CourseHasCourse extends Zend_Db_Table_Abstract
{

    protected $_name = 'course_has_course';
    protected $_primary = array('course_idcourse', 'course_idcourse1');
    
    protected $_referenceMap    = array(
    		'Course' => array(
    				'columns'           => 'course_idcourse',
    				'refTableClass'     => 'Application_Model_DbTable_Course',
    				'refColumns'        => 'idcourse'
    		),
    		'Course1' => array(
    				'columns'           => 'course_idcourse1',
    				'refTableClass'     => 'Application_Model_DbTable_Course',
    				'refColumns'        => 'idcourse'
    		)
    );

}

