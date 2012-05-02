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

