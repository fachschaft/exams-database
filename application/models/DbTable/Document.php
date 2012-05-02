<?php 
 /**
 * exams-database
 * @copyright   Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo        -
 */

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

