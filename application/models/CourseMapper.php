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
    
    public function fetchByDegree(Application_Model_Degree $degree)
    {    
        $degreeTb = new Application_Model_DbTable_Degree();

        //$profiler = new Zend_Db_Profiler();
        //$profiler->setEnabled(true);
        //$degree->getAdapter()->setProfiler($profiler);

        
        $resultSet = $degreeTb->find($degree->id)->current()
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
	
	public function fetchAll()
    {    
        $resultSet = $this->GetDbTable()->fetchAll();

        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Course();
            $entry->setId($row['idcourse'])
                  ->setName($row['name']);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    public function add(Application_Model_Course $course)
    {
    	$new_course_id = $this->getDbTable()->insert(array('name'=>$course->name));
    	
    	var_dump($new_course_id);
    	
    	// addign degree connections
    	$degHasCou = new Application_Model_DbTable_DegreeHasCourse();
    	foreach($course->degrees as $degree)
    	{
    		$degHasCou->insert(array('degree_iddegree'=>$degree->id, 'course_idcourse'=>$new_course_id));
    	}
    }
    
    public function find($id)
    {
    	$res = $this->getDbTable()->find($id)->current();
    	
    	$degrees = array();
    	$resultSet = $this->getDbTable()->find($id)->current()
								    		->findManyToManyRowset('Application_Model_DbTable_Degree',
													    			'Application_Model_DbTable_DegreeHasCourse',
													    			'Course', 'Degree');
    	foreach ($resultSet as $row) {
    		$degrees[] = new Application_Model_Degree(array('id'=>$row->iddegree, 'name'=>$row->name));
    	}
    	
    	return new Application_Model_Course(array('id'=>$res->idcourse, 'name'=>$res->name, 'degrees'=>$degrees));
    	
    }
    
    public function update(Application_Model_Course $course)
    {
    	//$this->getDbTable()->update(array(), $where)
    	$course_old = $this->find($course->id);
    	
    	
    	// delete foreign
    	// course
    	foreach($this->array_object_diff_by_id($course_old->degrees, $course->degrees) as $element) {
    		$this->getDbTable()->getAdapter()->query("DELETE FROM `degree_has_course` WHERE `degree_iddegree` = ".$element->id." AND `course_idcourse` = ".$course->id.";");
    	}
    	
    	// setup new foreign key
		// course
		foreach($this->array_object_diff_by_id($course->degrees, $course_old->degrees) as $element) {
			$this->getDbTable()->getAdapter()->query("INSERT INTO `degree_has_course` (`degree_iddegree` ,`course_idcourse`) VALUES ('".$element->id."',  '".$course->id."')");
		}
    	
    }
    
    /**
     * @return an array containing all the entries from ao1 that are not present in the other arrays by comparing the id of the objects
     */
    private function array_object_diff_by_id(array $ao1, array $ao2)
    {
    	$returnArray = array();
    	foreach ($ao1 as $a1)
    	{
    		$found = false;
    		foreach ($ao2 as $a2)
    		{
    			if($a1->id == $a2->id)
    			{
    				$found = true;
    			}
    		}
    		if(!$found)
    		{
    			$returnArray[] = $a1;
    		}
    	}
    	return $returnArray;
    }

}

