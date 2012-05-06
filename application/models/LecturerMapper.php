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
        
        /*$select = $this->getDbTable()->getAdapter()->select()
              ->from(array('l' => 'lecturer'),
                     array('idlecturer', 'name', 'first_name',	'degree'))
              ->join(array('dhl' => 'degree_has_lecturer'),
                     'dhl.lecturer_idlecturer = l.idlecturer')
              ->where('dhl.degree_iddegree = ?', $degree->id)
              ->order(array('order', 'name'));
        
        
        $resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);*/
        
        $resultSet = $this->getDbTable()->getAdapter()->query("
        		SELECT lecturer.idlecturer, lecturer.degree, lecturer.first_name, lecturer.name,
        		replace(
        		replace(
        		replace(
        		replace(
        		replace(
        		replace( lecturer.name,
        		'&auml;', 'a'),
        		'&Auml;', 'A'),
        		'&ouml;', 'o'),
        		'&Ouml;', 'O'),
        		'&uuml;', 'u'),
        		'&Uuml;', 'U')
        		as unescaped_name FROM
        		lecturer JOIN degree_has_lecturer
        		ON degree_has_lecturer.lecturer_idlecturer = lecturer.idlecturer
        		JOIN degree ON degree_has_lecturer.degree_iddegree = degree.iddegree
        		WHERE degree.iddegree = ".$degree->id."
        		ORDER BY unescaped_name;
        		");
         
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
    
    public function add(Application_Model_Lecturer $lecturer)
    {
    	$new_lecturer_id = $this->getDbTable()->insert(array('name'=>$lecturer->name, 'first_name'=>$lecturer->firstName, 'degree'=>$lecturer->degree));
    	
        	// addign degree connections
    	$degHasLec = new Application_Model_DbTable_DegreeHasLecturer();
    	foreach($lecturer->degrees as $degree)
    	{
    		$degHasLec->insert(array('degree_iddegree'=>$degree->id, 'lecturer_idlecturer'=>$new_lecturer_id));
    	}
    }
    
    public function find($id)
    {
    	$cur = $this->getDbTable()->find($id)->current();
    	$degrees = array();
    	$resultDegrees = $this->getDbTable()->find($id)->current()
									    	->findManyToManyRowset('Application_Model_DbTable_Degree',
									    			'Application_Model_DbTable_DegreeHasLecturer',
									    			'Lecturer', 'Degree');
    	foreach ($resultDegrees as $rowDeg) {
    		$degrees[] = new Application_Model_Course(array('id'=>$rowDeg->iddegree, 'name'=>$rowDeg->name));
    	}
    	
    	
    	return new Application_Model_Lecturer(array('id'=>$cur->idlecturer, 'name'=>$cur->name, 'firstName'=>$cur->first_name, 'degree'=>$cur->degree, 'degrees'=>$degrees));
    }
    
    public function delete(Application_Model_Lecturer $lecturer)
    {
    	$this->getDbTable()->delete('idlecturer = '.$lecturer->id);
    }
    
    public function update(Application_Model_Lecturer $lecturer)
    {
    	$this->getDbTable()->update(array('name'=>$lecturer->name, 'first_name'=>$lecturer->firstName, 'degree'=>$lecturer->degree), 'idlecturer = '.$lecturer->id);
    	
    	$lecturer_old = $this->find($lecturer->id);
    	 
    	 
    	// delete foreign
    	// degree
    	foreach($this->array_object_diff_by_id($lecturer_old->degrees, $lecturer->degrees) as $element) {
    		$this->getDbTable()->getAdapter()->query("DELETE FROM `degree_has_lecturer` WHERE `degree_iddegree` = ".$element->id." AND `lecturer_idlecturer` = ".$lecturer->id.";");
    	}
    	 
    	// setup new foreign key
    	// degree
    	foreach($this->array_object_diff_by_id($lecturer->degrees, $lecturer_old->degrees) as $element) {
    		$this->getDbTable()->getAdapter()->query("INSERT INTO `degree_has_lecturer` (`degree_iddegree` ,`lecturer_idlecturer`) VALUES ('".$element->id."',  '".$lecturer->id."')");
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

