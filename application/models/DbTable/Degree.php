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

