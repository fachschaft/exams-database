<?php

class Application_Model_DbTable_CourseGroup extends Zend_Db_Table_Abstract
{

    protected $_name = 'course_group';
    
    protected $_primary = 'idcourse_group';

    //protected $_dependentTables = array('Application_Model_DbTable_Course', 'Application_Model_DbTable_ExamHasCourseGroup');
    
        protected $_referenceMap    = array(
        'Course' => array(
            'columns'           => 'idcourse_group',
            'refTableClass'     => 'Application_Model_DbTable_Course',
            'refColumns'        => 'course_group_idcourse_group'
        )
    );
    

}

