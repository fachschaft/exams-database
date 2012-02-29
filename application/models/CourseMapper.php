<?php

class Application_Model_CourseMapper
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
            $this->setDbTable('Application_Model_DbTable_Course');
        }
        return $this->_dbTable;
    }
    
    public function fetchByDegree($degreeId)
    {    
        $degree = new Application_Model_DbTable_Degree();

        //$profiler = new Zend_Db_Profiler();
        //$profiler->setEnabled(true);
        //$degree->getAdapter()->setProfiler($profiler);

        
        $resultSet = $degree->find($degreeId)->current()
                            ->findManyToManyRowset('Application_Model_DbTable_Course',
                                                   'Application_Model_DbTable_DegreeHasCourse',
                                                   'Degree', 'Course');
                                                   
        
        //var_dump($profiler);

         
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Course();
            $entry->setId($row['idcourse'])
                  ->setName($row['name']);
            $entries[] = $entry;
        }
        
        return $entries;
    }

}

