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
        $table = new Application_Model_DbTable_Course();
        
        $table->getAdapter();
        
        $select = $table->getAdapter()->select()
              ->from(array('c' => 'course'),
                     array('idcourse', 'name'))
              ->join(array('dhc' => 'degree_has_course'),
                     'dhc.course_idcourse = c.idcourse')
              ->where('dhc.degree_iddegree = ?', $degreeId);
        
        
        /*$select = $table->getAdapter()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->join('degree_has_course', 'degree_has_course.course_idcourse = courses_group_idcourses_group')
               ->where('degree_has_course.degree_iddegree = ?', $degreeId);*/
        
        // for a joint select ists importetn to fecht the select with ->getAdapter()
        
        $resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);
         
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

