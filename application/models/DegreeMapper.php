<?php

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
        $resultSet = $this->getDbTable()->fetchAll(
                     $this->getDbTable()->select()
                                        ->where('degree_group_iddegree_group = ?', $groupId)
                                        );
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Degree();
            $entry->setId($row->iddegree)
                  ->setGroup($row->degree_group_iddegree_group)
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }

}

