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

