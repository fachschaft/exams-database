<?php

class Application_Model_DbTable_DegreeHasCourse extends Zend_Db_Table_Abstract
{

    protected $_name = 'degree_has_course';
    protected $_primary = array('degree_iddegree', 'course_idcourse');

        protected $_referenceMap    = array(
        'Degree' => array(
            'columns'           => 'degree_iddegree',
            'refTableClass'     => 'Application_Model_DbTable_Degree',
            'refColumns'        => 'iddegree'
        ),
        'Course' => array(
            'columns'           => 'course_idcourse',
            'refTableClass'     => 'Application_Model_DbTable_Course',
            'refColumns'        => 'idcourse'
        )
    );


}

