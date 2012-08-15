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

class Application_Model_SemesterMapper
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
            $this->setDbTable('Application_Model_DbTable_Semester');
        }
        return $this->_dbTable;
    }
    
    public function fetchAll()
    {
        $select = $this->getDbTable()->select()->order(array('begin_time DESC'));
        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Semester();
            $entry->setId($row->idsemester)
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function add($semester)
    {
    	$data = array(
    			'name'         => $semester->name,
    			'begin_time'  => date( 'Y-m-d', $semester->begin_time).' 00:00:00'
    	);
    	
    	
    	$insert = $this->getDbTable()->insert($data);
    	return $insert;    	
    }
    
    public function checkFuthereSemesterExists()
    {
    	// from the current time, atleast one semester into the future exists
    	// ws = 01.10.
    	// ss = 01.04.
    	
    	$select = $this->getDbTable()->select()->where('begin_time IS NOT NULL AND begin_time > NOW()')->order(array('begin_time DESC'));
    	$resultSet = $this->getDbTable()->fetchAll($select);
    	
    	if(count($resultSet) < 2) {
    		$select2 = $this->getDbTable()->select()->where('begin_time IS NOT NULL')->order(array('begin_time DESC'));
    		$resultSet2 = $this->getDbTable()->fetchAll($select2);
    		
    		$last_semester_start = 0;
    		
    		if(count($resultSet2) == 0) {
    			// add current semester (server time back)
    			$currentMonth = date("n", time());
    			if($currentMonth < 10) {
    				$last_semester_start = mktime(0,0,0,10,1, (date("Y", time())-1));
    			} else {
    				$last_semester_start = mktime(0,0,0,4,1, date("Y", time()));
    			}
    			
    		} else {
    			$last_semester_start = strtotime($resultSet2[0]->begin_time);
    			
    		}
    		
    		$newTime = new DateTime();
    		$newTime->setTimestamp(time());
    		$newTime->add(new DateInterval('P6M'));
    		
    		while($last_semester_start < $newTime->getTimestamp())
    		{
    			
    			$month = date("n", $last_semester_start);
    			
    			
    			if($month == 4) {
    				//04 - so add 01.10.
    				$newSemTime = mktime(0,0,0,10,1, date("Y", $last_semester_start));
    				$newSem = "WS ".date("Y", $last_semester_start)."/".(date("y", $last_semester_start)+1);
    				 
    			} else {
    				//10 - so add 01.04.
    				$newSemTime = mktime(0,0,0,4,1, date("Y", $last_semester_start)+1);
    				$newSem = "SS ".(date("Y", $last_semester_start)+1);
    			}

    			$semester = new Application_Model_Semester();
    			$semester->name = $newSem;
    			$semester->begin_time = $newSemTime;
    			
    			$this->add($semester);
    			
    			$last_semester_start = $newSemTime;
    		}
    		
    	}
    	
    }

}

