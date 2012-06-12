<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakult�t Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Model_DegreeMapper
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
            $this->setDbTable('Application_Model_DbTable_Degree');
        }
        return $this->_dbTable;
    }
    
    public function fetchByGroup($groupId)
    {
        $groups = new Application_Model_DbTable_DegreeGroup();
        $resultSet = $groups->find($groupId)->current()->findDependentRowset('Application_Model_DbTable_Degree', 'Group', $this->getDbTable()->select()->order('order'));

        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Degree();
            $entry->setId($row->iddegree)
                  ->setGroup(new Application_Model_DegreeGroup(array('id'=>$row->degree_group_iddegree_group)))
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function fetchAll()
    {
        $groups = new Application_Model_DbTable_Degree();
        $resultSet = $groups->fetchAll(null, array('order'));
        
        $groupMapper = new Application_Model_DegreeGroupMapper();

        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Degree();
            $entry->setId($row->iddegree)
                  ->setGroup($groupMapper->find($row->degree_group_iddegree_group))
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function find($id)
    {
    	$res = $this->getDbTable()->find($id)->current();
    	return new Application_Model_Degree(array('id'=>$res->iddegree, 'name'=>$res->name, 'group'=>new Application_Model_DegreeGroup(array('id'=>$res->degree_group_iddegree_group))));
    }
    
    public function add(Application_Model_Degree $degree)
    {
    	$filter = new Zend_Filter_HtmlEntities();
    	$this->getDbTable()->insert(array('name'=>$filter->filter($degree->name),
    			'name_unescaped'=>Custom_Formatter_EscapeSpecialChars::escape($filter->filter($degree->name)),
    			'degree_group_iddegree_group'=>$degree->group->id));
    }
    
    public function updateGroup(Application_Model_Degree $degree)
    {
    	$this->getDbTable()->update(array('degree_group_iddegree_group'=>$degree->group->id, 'name'=>$degree->name), 'iddegree = '.$degree->id);
    }
    
    
    public function delete(Application_Model_Degree $degree)
    {
    	$this->getDbTable()->delete('iddegree = '.$degree->id);
    }

}

