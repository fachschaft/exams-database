<?php

class Application_Model_ExamMapper
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
            $this->setDbTable('Application_Model_DbTable_Exam');
        }
        return $this->_dbTable;
    }
    
    private function getExam($examId)
    {
  		// define the join select over the exam itself
    	$select = $this->getDbTable()->getAdapter()->select()
    	->from(array('x' => 'exam'),
    			array(	'idexam', 
    				  	'autor',
    				  	'comment',
    					'create_date',
    					'modified_last_date',
    					'degree_iddegree',
    					'exam_status_idexam_status',
    					'semester_idsemester',
    					'exam_type_idexam_type',
    					'exam_sub_type_idexam_sub_type',
    					'university_iduniversity',
    					'exam_degree_idexam_degree',
    				  	'sem.name as semester_name', 
    					'uni.name as university_name', 
    					'ext.name as type_name', 
    					'est.name as sub_typ_name',
    					'deg.name as degree_name',
    					'sta.name as status_name',
    					'exdeg.name as exam_degree_name',
    					))
    			->join(array('sta' => 'exam_status'),
    					'sta.idexam_status = x.exam_status_idexam_status')
    			->join(array('deg' => 'degree'),
    					'deg.iddegree = x.degree_iddegree')
    			->join(array('exdeg' => 'exam_degree'),
    					'exdeg.idexam_degree = x.exam_degree_idexam_degree')
    			->join(array('uni' => 'university'),
    					'uni.iduniversity = x.university_iduniversity')
    			->join(array('sem' => 'semester'),
    					'sem.idsemester = x.semester_idsemester')
    			->join(array('ext' => 'exam_type'),
    					'ext.idexam_type = x.exam_type_idexam_type')
    			->join(array('est' => 'exam_sub_type'),
    					'est.idexam_sub_type = x.exam_sub_type_idexam_sub_type')
    			 ->where('x.idexam = '. $examId);
    			
    	$result = $this->getDbTable()->getAdapter()->fetchAll($select);
    	
    	// if one returns all was nice, if zero rows return there was not every joint partner available, if more than one returns some double entry may be in the database 
    	if (count($result) != 1) {
    		throw new Exception ( "Inconsistent database for exam id: ".$examId." - Call an admin!" );
    	}
    	$row = $result[0];
    	
    	// fill the exam
    	$exam = new Application_Model_Exam();
   	
    	$exam->setId($row['idexam']);
    	$exam->setAutor($row['autor']);
    	$exam->setComment($row['comment']);

    	$exam->setCreated($row['create_date']);
    	$exam->setModified($row['modified_last_date']);

    	$exam->setDegree(new Application_Model_Degree(array('id'=>$row['degree_iddegree'], 'name'=>$row['degree_name'])));
    	$exam->setStatus(new Application_Model_ExamStatus(array('id'=>$row['exam_status_idexam_status'], 'name'=>$row['status_name'])));
    	$exam->setSemester(new Application_Model_Semester(array('id'=>$row['semester_idsemester'], 'name'=>$row['semester_name'])));
    	$exam->setType(new Application_Model_ExamType(array('id'=>$row['exam_type_idexam_type'], 'name'=>$row['type_name'])));
    	$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$row['exam_sub_type_idexam_sub_type'], 'name'=>$row['sub_typ_name'])));
    	$exam->setUniversity(new Application_Model_ExamUniversity(array('id'=>$row['university_iduniversity'], 'name'=>$row['university_name'])));

    	$exam->setWrittenDegree(new Application_Model_ExamDegree(array('id'=>$row['exam_degree_idexam_degree'], 'name'=>$row['exam_degree_name'])));

    	
    	// collect all lecturer
    	$resultSetLecturer = $this->getDbTable()->find($row['idexam'])->current()
							    	->findManyToManyRowset(	'Application_Model_DbTable_Lecturer',
							    							'Application_Model_DbTable_ExamHasLecturer',
							    							'Exam', 'Lecturer');
    	$entriesLec   = array();
    	foreach ($resultSetLecturer as $row2) {
    		$entriesLec[] = new Application_Model_Lecturer(array('id'=>$row2['idlecturer'],'name'=>$row2['name'],'degree'=>$row2['degree'],'firstName'=>$row2['first_name']));
    		//$entriesLec[$row2['idlecturer']] = $row2['name'] .", ". $row2['degree'] ." " . $row2['first_name'];
    	}
    	$exam->setLecturer($entriesLec);
    	
    	
    	// collect all courses which directly connected to the exam
    	$resultSetCourse = $this->getDbTable()->find($row['idexam'])->current()
    	->findManyToManyRowset(	'Application_Model_DbTable_Course',
    							'Application_Model_DbTable_ExamHasCourse',
    							'Exam', 'Course');
    	$entriesCor   = array();
    	$courseIds		=array(); // for later check in the indrect connected courses
    	foreach ($resultSetCourse as $row2) {
    		$entriesCor[] = new Application_Model_Course(array('id'=>$row2['idcourse'],'name'=>$row2['name']));
    		//$entriesCor[$row2['idcourse']] = $row2['name'];
    		$courseIds[] = $row2['idcourse'];
    	}    	
    	$exam->setCourse($entriesCor);
    	
    	
    	// collect all indirect connected courses
    	$course = new Application_Model_DbTable_Course();
    	$resultSetCourses = $course->find($courseIds);
    	
    	foreach($resultSetCourses as $CorseRel) {
    	$resultSetCourses2 = $CorseRel
    	->findManyToManyRowset(	'Application_Model_DbTable_Course',
    							'Application_Model_DbTable_CourseHasCourse',
    							'Course', 'Course1');
	    	$entriesCors   = array();
	    	foreach ($resultSetCourses2 as $row2) {
	    		// remove the courses which are allredy in the directly connectet set
	    		if(!in_array($row2['idcourse'], $courseIds)) {
	    			$courseIds[] = $row2['idcourse'];
	    			$entriesCors[] = new Application_Model_Course(array('id'=>$row2['idcourse'],'name'=>$row2['name']));
	    			//$entriesCors[$row2['idcourse']] = $row2['name'];
	    		}
	    	}
	    	$resultSetCourses3 = $CorseRel
	    	->findManyToManyRowset(	'Application_Model_DbTable_Course',
					    			'Application_Model_DbTable_CourseHasCourse',
					    			'Course1', 'Course');
	    	foreach ($resultSetCourses3 as $row3) {
	    		// remove the courses which are allredy in the directly connectet set and ind the indirect connected set from the frist run
	    		if(!in_array($row3['idcourse'], $courseIds)) {
	    			$entriesCors[] = new Application_Model_Course(array('id'=>$row3['idcourse'],'name'=>$row3['name']));
	    		}
	    	}
    	}
    	$exam->setCourseConnected($entriesCors);
    	
    	
    	// grab all documents and store them
    	$mapper = new Application_Model_DocumentMapper();
    	$docs = $mapper->fetchByExamId($row['idexam']);
    	$entry = array();
    	foreach ($docs as $doc)
    	{
    		$entry[] = $doc;
    	}
    	$exam->setDocuments($entry);
    	
    	    	
    	return $exam;
    }
    
    
    // this function takes single integer or arrays
    public function fetch($courseIds, $lecturerIds, $semesterIds, $examTypeIds, $degree, array $status = array(), $withReflexive = true)
    {  
	
		if(!empty($status))
		{
			$status = "x.exam_status_idexam_status IN (" . implode(",", $status).") ";
		}
		
		$where2 = "";
		if($withReflexive) {
			$status .= " AND ";
			$where2 = '((cor.idcourse = chc.course_idcourse1) OR (cor.idcourse = chc.course_idcourse))';
		}

        $select = $this->getDbTable()->getAdapter()->select()
              ->from(array('x' => 'exam'),
                     array('idexam'))
              ->join(array('uni' => 'university'),
                     'uni.iduniversity = x.university_iduniversity')
			  ->join(array('ehl' => 'exam_has_lecturer'),
                     'ehl.exam_idexam = x.idexam')
              ->join(array('lec' => 'lecturer'),
                     'lec.idlecturer = ehl.lecturer_idlecturer')
              ->join(array('sem' => 'semester'),
                     'sem.idsemester = x.semester_idsemester')
              ->join(array('ext' => 'exam_type'),
                     'ext.idexam_type = x.exam_type_idexam_type')
              ->join(array('est' => 'exam_sub_type'),
                     'est.idexam_sub_type = x.exam_sub_type_idexam_sub_type')
              ->join(array('ehcg' => 'exam_has_course'),
                     'ehcg.exam_idexam = x.idexam')
              ->join(array('cor' => 'course'),
                     'cor.idcourse = ehcg.course_idcourse')
              ->where($status.$where2)
              ->group('idexam')
              ->order('semester_idsemester DESC');
			  
			  if($withReflexive) $select->join(array('chc' => 'course_has_course'),'');

        if((!is_array($courseIds) && $courseIds != -1) || (is_array($courseIds) && !in_array(-1, $courseIds)))
        {
            $select->where('chc.course_idcourse1 IN (?) OR chc.course_idcourse in (?)', $courseIds);
        } else if($degree == -1) {
		}
		else {
            // if there is no corse id set, we select by degree
            $select->join(array('dhc' => 'degree_has_course'),
                                'cor.idcourse = dhc.course_idcourse')
                    ->join(array('deg' => 'degree'),
                                'dhc.degree_iddegree = deg.iddegree')
                    ->where('deg.iddegree IN (?)', $degree);
        }
        if((!is_array($lecturerIds) && $lecturerIds != -1) || (is_array($lecturerIds) && !in_array(-1, $lecturerIds)))
            $select->where('lec.idlecturer IN (?)', $lecturerIds);
        if((!is_array($semesterIds) && $semesterIds != -1) || (is_array($semesterIds) && !in_array(-1, $semesterIds)))
            $select->where('sem.idsemester IN (?)', $semesterIds);
        if((!is_array($examTypeIds) && $examTypeIds != -1) || (is_array($examTypeIds) && !in_array(-1, $examTypeIds)))
            $select->where('ext.idexam_type IN (?)', $examTypeIds);
              
        
        $resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);

        $entries   = array();
        foreach ($resultSet as $row) {
        	$entries[] = $this->getExam($row['idexam']);
        }

        return $entries;
    }
	
	public function fetchUnchecked()
	{
		return $this->fetch("-1", "-1", "-1", "-1", "-1", array(Application_Model_ExamStatus::Unchecked), false);
	}
	
	public function fetchReported()
	{
		return $this->fetch("-1", "-1", "-1", "-1", "-1", array(Application_Model_ExamStatus::Reported), false);
	}
	
	public function fetchPublic(){
		return $this->fetch("-1", "-1", "-1", "-1", "-1", array(Application_Model_ExamStatus::PublicExam, Application_Model_ExamStatus::Reported), false);
	}
	
	public function find($id)
    {
        return $this->getExam($id);
    }
		
	public function saveAsNewExam($exam) {
		$data = array (
				'semester_idsemester' 			=> $exam->semester->id,
				'exam_type_idexam_type' 		=> $exam->type->id,
				'exam_sub_type_idexam_sub_type' => $exam->subType->id,
				'degree_iddegree' 				=> $exam->degree->id,
				'autor'							=> $exam->autor,
				'exam_status_idexam_status' 	=> Application_Model_ExamStatus::NothingUploaded,
				'exam_degree_idexam_degree' 	=> $exam->degree->id,
				'university_iduniversity' 		=> $exam->university->id,
				'comment' 						=> $exam->comment,
				'create_date' 					=> new Zend_Db_Expr ( 'NOW()' ),
				'modified_last_date' 			=> new Zend_Db_Expr ( 'NOW()' ) 
		);
		
		$insert = $this->getDbTable ()->insert ( $data );
		
		// insert the connection to the corse
		foreach ( $exam->course as $course ) {
			if ($course->id != "-1") {
				$this->getDbTable ()->getAdapter ()->query ( "INSERT INTO  `exam_has_course` (`exam_idexam` ,`course_idcourse`)
															  VALUES ('" . $insert . "',  '" . $course->id . "')" );
			}
		
		}
		
		// insert the connection to the lecturer
		foreach ( $exam->lecturer as $lecturer ) {
			if ($lecturer->id != "-1") {
				$this->getDbTable ()->getAdapter ()->query ( "INSERT INTO  `exam_has_lecturer` (`exam_idexam` ,`lecturer_idlecturer`)
                                                         VALUES ('" . $insert . "',  '" . $lecturer->id . "')" );
			}
		
		}
		
		return $insert;
	}
	
	public function updateExam(Application_Model_Exam $exam)
	{
		$exam_old = $this->find($exam->id);

		// delete foreign
		// course
		foreach($this->array_object_diff_by_id($exam_old->course, $exam->course) as $element) {
			$this->getDbTable()->getAdapter()->query("DELETE FROM `exam_has_course` WHERE `exam_idexam` = ".$exam->id." AND `course_idcourse` = ".$element->id.";");
		}
		
		// lecturer
		foreach($this->array_object_diff_by_id($exam_old->Lecturer, $exam->Lecturer) as $element) {
			$this->getDbTable()->getAdapter()->query("DELETE FROM `exam_has_lecturer` WHERE `exam_idexam` = ".$exam->id." AND `lecturer_idlecturer` = ".$element->id.";");
		}

		
		// setup new foreign key
		// course
		foreach($this->array_object_diff_by_id($exam->course, $exam_old->course) as $element) {
			$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_has_course` (`exam_idexam` ,`course_idcourse`) VALUES ('".$exam->id."',  '".$element->id."')");
		}
		
		// lecturer
		foreach($this->array_object_diff_by_id($exam->Lecturer, $exam_old->Lecturer) as $element) {
			$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_has_lecturer` (`exam_idexam` ,`lecturer_idlecturer`) VALUES ('".$exam->id."',  '".$element->id."')");
		}

		
		// update exam
		$this->getDbTable()->getAdapter()->query("UPDATE  `exam` SET
												`semester_idsemester` =  '".$exam->Semester->id."',
												`exam_type_idexam_type` =  '".$exam->Type->id."',
												`exam_sub_type_idexam_sub_type` =  '".$exam->SubType->id."',
												`exam_degree_idexam_degree` =  '".$exam->Degree->id."',
												`university_iduniversity` =  '".$exam->university->id."',
												`comment` =  '".$exam->comment."',
												`autor` =  '".$exam->autor."',
												`modified_last_date` = NOW()
												WHERE  `idexam` =".$exam->id.";");
												
		
		$this->addLogMessage($exam->id, 'Exam details updated by %user%.');	

	}
	
	public function updateExamStatusToUnchecked($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
		$this->addLogMessage($examId, 'Exam files uploaded by user.');
    }
	
	public function updateExamStatusToDisapprove($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
		$this->addLogMessage($examId, 'Exam disapproved by %user%.');
		//remove the exam from the search index
		$index = new Application_Model_ExamSearch();
		$index->removeFileFromIndex($examId);
    }
	
	public function updateExamStatusToChecked($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::PublicExam."', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."';");
		$this->addLogMessage($examId, 'Exam approved by %user%.');
		// Add the exam to the search index
		$index = new Application_Model_ExamSearch();
		//TODO(aamuuninen) wait for "entmurxing", then utilize sensible keywords
		$index->addFileToIndex($examId);
    }
	
	public function updateExamStatusToDelete($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Deleted."', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."';");
		$this->getDbTable()->getAdapter()->query("UPDATE `document` SET  `deleted` =  '1' WHERE  `exam_idexam` =".$examId.";");
		$this->addLogMessage($examId, 'Exam deleted by %user%.');
    }
    
    public function updateExamStatusToReported($examId, $reason)
    {
    	//TODO is changing the last modified date here correct?
    	$this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Reported."' WHERE `idexam` =".$examId.";");
    	$this->addLogMessage($examId, 'Exam was reported with reason ' . $reason . '.');
    }
    
    public function updateExamStatusUnreport($examId)
    {
    	$this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::PublicExam."', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Reported."';");
    	$this->addLogMessage($examId, 'Exam unreport by %user%.');
    }
	
	public function returnQuicksearchIndexKeywords($id) {
		if (! isset ( $id ))
			throw new Exception ( "No Id given for index keyword generation", 500 );
		
		$keywordsArray = array ();
		$exam = $this->find ( $id );
		foreach ( $exam->course as $course ) {
			$keywordsArray [] = $course->name;
		}
		foreach ( $exam->lecturer as $lecturer ) {
			$keywordsArray [] = $lecturer->name;
		}
		
		foreach ( $exam->courseConnected as $connected ) {
			$keywordsArray [] = $connected->name;
		}
		
		return implode ( ' ', $keywordsArray);
	}
    
	private function addLogMessage($examId, $message) {
		$this->getDbTable()->getAdapter()->query("INSERT INTO  `exam_log` (`exam_idexam` ,`message`)
															  VALUES ('".$examId."',  '".$message."')");
	}
	
	public function updateDownloadCounter($examId)
	{
		$this->getDbTable()->getAdapter()->query("UPDATE `exam` SET  `downloads` =  `downloads`+1 WHERE `idexam` =".$examId.";");
		
		$result = $this->getDbTable()->getAdapter()->query("SELECT idexam_download_statistic_day FROM `exam_download_statistic_day` 
												  	   		WHERE `date` = DATE(NOW()) AND `exam_idexam` = '".$examId."';");

		$count = 0;
		foreach ($result as $res) $count++;
		
		if($count > 1) {
			throw new Exception ( 'Inconsistent database, call an admin!', 500 );
		}
		if($count == 0)
		{
			try {
				$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_download_statistic_day` (`exam_idexam`, `date`, `downloads`)
						VALUES ('".$examId."', NOW(), '1');");
			} catch (Exception $e) {
				//ToDo(leinfeda): Add log entry, tried to insert and failed, this my be because there was a insert while the result select above and this insert try, this is not threadsafe!
			}
			
		}
		if($count == 1)
		{
			$this->getDbTable()->getAdapter()->query("UPDATE `exam_download_statistic_day` SET  `downloads` =  `downloads`+1 
													  WHERE `date` = DATE(NOW()) AND `exam_idexam` = '".$examId."';");
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

