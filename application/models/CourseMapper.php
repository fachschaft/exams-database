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
        $resultSet = $this->GetDbTable()->fetchAll(null, array('name ASC'));

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
    	
    	$connected = array();
    	$connectedIds = array();
    	$resultConnected = $this->getDbTable()->find($id)->current()
										    	->findManyToManyRowset('Application_Model_DbTable_Course',
										    			'Application_Model_DbTable_CourseHasCourse',
										    			'Course1', 'Course');
    	foreach ($resultConnected as $rowCon) {
    		$connectedIds[] = $rowCon->idcourse;
    		$connected[] = new Application_Model_Course(array('id'=>$rowCon->idcourse, 'name'=>$rowCon->name));
    	}
    	
    	$resultConnected2 = $this->getDbTable()->find($id)->current()
									    	->findManyToManyRowset('Application_Model_DbTable_Course',
									    			'Application_Model_DbTable_CourseHasCourse',
									    			'Course', 'Course1');
    	foreach ($resultConnected2 as $rowCon) {
    		if(!in_array($rowCon->idcourse ,$connectedIds))
    		$connected[] = new Application_Model_Course(array('id'=>$rowCon->idcourse, 'name'=>$rowCon->name));
    	}
    	
    	return new Application_Model_Course(array('id'=>$res->idcourse, 'name'=>$res->name, 'degrees'=>$degrees, 'connectedCourse'=>$connected));
    	
    }
    
    public function delete(Application_Model_Course $course)
    {
    	$this->getDbTable()->delete('idcourse = '.$course->id);
    }
    
    public function update(Application_Model_Course $course)
    {
    	//$this->getDbTable()->update(array(), $where)
    	$course_old = $this->find($course->id);
    	
    	
    	// delete foreign
    	// degree
    	foreach($this->array_object_diff_by_id($course_old->degrees, $course->degrees) as $element) {
    		$this->getDbTable()->getAdapter()->query("DELETE FROM `degree_has_course` WHERE `degree_iddegree` = ".$element->id." AND `course_idcourse` = ".$course->id.";");
    	}
    	
    	// course
    	foreach($this->array_object_diff_by_id($course_old->connectedCourse, $course->connectedCourse) as $element) {
    		$this->getDbTable()->getAdapter()->query("DELETE FROM `course_has_course` WHERE (`course_idcourse` = ".$element->id." AND `course_idcourse1` = ".$course_old->id.") OR (`course_idcourse1` = ".$element->id." AND `course_idcourse` = ".$course_old->id.");");
    	}
    	
    	// setup new foreign key
		// degree
		foreach($this->array_object_diff_by_id($course->degrees, $course_old->degrees) as $element) {
			$this->getDbTable()->getAdapter()->query("INSERT INTO `degree_has_course` (`degree_iddegree` ,`course_idcourse`) VALUES ('".$element->id."',  '".$course->id."')");
		}
		
		// course
		foreach($this->array_object_diff_by_id($course->connectedCourse, $course_old->connectedCourse) as $element) {
			if($course_old->id != $element->id)
			$this->getDbTable()->getAdapter()->query("INSERT INTO `course_has_course` (`course_idcourse` ,`course_idcourse1`) VALUES ('".$course->id."',  '".$element->id."')");
		}
		
		$this->getDbTable()->update(array('name'=>$course->name), 'idcourse = '.$course->id);
    	
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

