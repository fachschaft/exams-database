<?php

class Application_Model_DbTable_Degree extends Zend_Db_Table_Abstract
{

    protected $_name = 'degree';
    protected $_primary = 'iddegree';

        protected $_referenceMap    = array(
        'Group' => array(
            'columns'           => 'degree_group_iddegree_group',
            'refTableClass'     => 'Application_Model_DbTable_DegreeGroup',
            'refColumns'        => 'iddegree_group'
        )
    );
}

