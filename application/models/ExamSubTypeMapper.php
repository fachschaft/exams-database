<?php

class Application_Model_ExamSubTypeMapper
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
            $this->setDbTable('Application_Model_DbTable_ExamSubType');
        }
        return $this->_dbTable;
    }
    
    public function fetchAll()
    {
        //$select = $this->getDbTable()->select()->order(array('idsemester DESC'));
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_ExamSubType();
            $entry->setId($row->idexam_sub_type)
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }

}

