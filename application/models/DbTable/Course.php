<?php

class Application_Model_DbTable_Course extends Zend_Db_Table_Abstract
{

    protected $_name = 'course';
    protected $_primary = 'idcourse';
    
    //protected $_dependentTables = array('Application_Model_DbTable_CourseGroup');

    protected $_referenceMap    = array(
        'CourseGroup' => array(
            'columns'           => 'course_group_idcourse_group',
            'refTableClass'     => 'Application_Model_DbTable_CourseGroup',
            'refColumns'        => 'idcourse_group'
        )
    );

}

