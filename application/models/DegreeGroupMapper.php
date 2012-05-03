<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Model_DegreeGroupMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_DegreeGroup');
        }
        return $this->_dbTable;
    }
    
    
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll(null, array('order'));
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_DegreeGroup();
            $entry->setId($row->iddegree_group)
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function find($id)
    {
    	$res = $this->getDbTable()->find($id)->current();
    	return new Application_Model_DegreeGroup(array('id'=>$res->iddegree_group, 'name'=>$res->name));
    }
    
    public function addNewGroup($group_name)
    {
    	$this->getDbTable()->insert(array('name'=>$group_name));
    }
    
    public function delete(Application_Model_DegreeGroup $group)
    {
    	$this->getDbTable()->delete("iddegree_group = ".$group->id);
    }
    
    public function update(Application_Model_DegreeGroup $group)
    {
    	$this->getDbTable()->update(array('name' => $group->name), 'iddegree_group = '.$group->id);
    }

}

