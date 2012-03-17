<?php

class Application_Model_DbTable_Document extends Zend_Db_Table_Abstract
{

    protected $_name = 'document';
    protected $_primary = 'iddocument';

        protected $_referenceMap    = array(
        'Exam' => array(
            'columns'           => 'exam_idexam',
            'refTableClass'     => 'Application_Model_DbTable_Exam',
            'refColumns'        => 'idexam'
        )
    );


}

