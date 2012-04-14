<?php

class Application_Model_LecturerMapper
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
            $this->setDbTable('Application_Model_DbTable_Lecturer');
        }
        return $this->_dbTable;
    }
    
    public function fetchByDegree(Application_Model_Degree $degree)
    {   
        //$adapter = $this->getDbTable()->getAdapter();
        
        $select = $this->getDbTable()->getAdapter()->select()
              ->from(array('l' => 'lecturer'),
                     array('idlecturer', 'name', 'first_name',	'degree'))
              ->join(array('dhl' => 'degree_has_lecturer'),
                     'dhl.lecturer_idlecturer = l.idlecturer')
              ->where('dhl.degree_iddegree = ?', $degree->id);
        
        $resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);
         
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Lecturer();
            $entry->setId($row['idlecturer'])
                  ->setFirstName($row['first_name'])
                  ->setDegree($row['degree'])
                  ->setName($row['name']);
            $entries[] = $entry;
        }
        
        return $entries;
    }
	
	public function fetchAll()
    {    
        $resultSet = $this->GetDbTable()->fetchAll();

        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Lecturer();
            $entry->setId($row['idlecturer'])
                  ->setFirstName($row['first_name'])
                  ->setDegree($row['degree'])
                  ->setName($row['name']);
            $entries[] = $entry;
        }
        
        return $entries;
    }

}

