<?php

class Application_Model_DbTable_DegreeHasLecturer extends Zend_Db_Table_Abstract
{

    protected $_name = 'degree_has_lecturer';
    protected $_primary = array('degree_iddegree', 'lecturer_idlecturer');
    
    protected $_referenceMap    = array(
    		'Degree' => array(
    				'columns'           => 'degree_iddegree',
    				'refTableClass'     => 'Application_Model_DbTable_Degree',
    				'refColumns'        => 'iddegree'
    		),
    		'Lecturer' => array(
    				'columns'           => 'lecturer_idlecturer',
    				'refTableClass'     => 'Application_Model_DbTable_Lecturer',
    				'refColumns'        => 'idlecturer'
    		)
    );


}

