<?php

class Application_Model_DocumentMapper
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
            $this->setDbTable('Application_Model_DbTable_Document');
        }
        return $this->_dbTable;
    }
    public function fetch($documentId)
    {
       $element = $this->getDbTable()->find($documentId)->current();
       $entry = new Application_Model_Document();
       
       $entry->setId($element['iddocument'])
                  ->setData($element['data'])
                  ->setExtention($element['extention'])
                  ;
                  
        return $entry;
    }

}

