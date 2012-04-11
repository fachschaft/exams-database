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
    
    // this function takes single integer or arrays
    public function fetch($courseIds, $lecturerIds, $semesterIds, $examTypeIds, $degree, $status = "", $withReflexive = true)
    {  
	
		if($status != "")
		{
			$status = "x.exam_status_idexam_status = " . $status;
			
		}
		
		$where2 = "";
		if($withReflexive) {
			$status .= " AND ";
			$where2 = '((cor.idcourse = chc.course_idcourse1) OR (cor.idcourse = chc.course_idcourse))';
		}

        $select = $this->getDbTable()->getAdapter()->select()
              ->from(array('x' => 'exam'),
                     array('idexam', 'comment', 'autor', 'sem.name as semester_name', 'GROUP_CONCAT(lec.idlecturer) as lecturer', 'uni.name as university', 'ext.name as type_name', 'est.name as sub_typ_name', 'GROUP_CONCAT(cor.idcourse) as courses'))
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
              ->group('idexam');
			  
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
            $entry = new Application_Model_Exam();
                       
            $coursesIds = explode(",", $row['courses']);
            
<<<<<<< HEAD
            $coursesIds = explode(",", $row['courses']);
=======
>>>>>>> origin/admin
            
            // collect the courses
            $selectCourse = $this->getDbTable()->getAdapter()->select()
                            ->from(array('x' => 'exam'),
                     array('idexam', 'cor.name as name', 'cor.idcourse as idcourse'))
              ->join(array('ehcg' => 'exam_has_course'),
                     'ehcg.exam_idexam = x.idexam')
              ->join(array('cor' => 'course'),
                     'cor.idcourse = ehcg.course_idcourse')
              ->where('idexam = ?', $row['idexam']);
              $resultSetCourses = $this->getDbTable()->getAdapter()->fetchAll($selectCourse);
            $courses = array();
            $coursesIds = array();
            foreach($resultSetCourses as $id => $cours)
            {
                $courses[$cours['idcourse']] = $cours['name'];
                $coursesIds[] = $cours['idcourse'];
            }
            
            if($withReflexive) {
				// collect all related courses
				// EXAMPLE Select
				// SELECT * FROM `course_has_course` as chc JOIN `course` WHERE 
				// (chc.course_idcourse = 4 AND course.idcourse = chc.course_idcourse1) OR 
				// (chc.course_idcourse1 = 4 AND course.idcourse = chc.course_idcourse)
				$selectRelatedCourse = $this->getDbTable()->getAdapter()->select()
				  ->from(array('chc' => 'course_has_course'),
						 array('cor.idcourse as idcourse', 'cor.name as name'))
				  ->join(array('cor' => 'course'),'')
				  ->where('(chc.course_idcourse IN (?) AND cor.idcourse = chc.course_idcourse1) OR
							(chc.course_idcourse1 IN (?) AND cor.idcourse = chc.course_idcourse)', $coursesIds);
				$resultRelatedCourse = $this->getDbTable()->getAdapter()->fetchAll($selectRelatedCourse);
				 foreach($resultRelatedCourse as $id => $cours)
				{
					$courses[$cours['idcourse']] = $cours['name'] . "*";
				}
			}
            
            
            // collect the lecturer
            $selectLecturer = $this->getDbTable()->getAdapter()->select()
              ->from(array('x' => 'exam'),
                     array('idexam'))
              ->join(array('ehl' => 'exam_has_lecturer'),
                     'ehl.exam_idexam = x.idexam')
              ->join(array('lec' => 'lecturer'),
                     'lec.idlecturer = ehl.lecturer_idlecturer')
              ->where('idexam = ?', $row['idexam']);
              $resultSetLecturer = $this->getDbTable()->getAdapter()->fetchAll($selectLecturer);
            $lecturers = array();
            
            foreach($resultSetLecturer as $id => $lect)
            {
				if($lect['degree'] == "" && $lect['first_name'] == "") {
					$lecturers[$id] = $lect['name'];
				} else {
                $lecturers[$id] = $lect['name'] .', '. $lect['degree'] . ' ' . $lect['first_name'];
				}
            }
            
            // collect documents
            $documentsMapper = new Application_Model_DocumentMapper();
            $documents = $documentsMapper->fetchByExamId($row['idexam']);
            
            
            $entry->setId($row['idexam'])
                  ->setSemester($row['semester_name'])
                  ->setLecturer($lecturers)
                  ->setType($row['type_name'])
                  ->setSubType($row['sub_typ_name'])
                  ->setCourse($courses)
                  ->setDocuments($documents)
				  ->setComment($row['comment'])
				  ->setUniversity($row['university'])
				  ->setAutor($row['autor'])
                  ;
            $entries[] = $entry;
            
            
            
            
        }

        return $entries;
    }
	
	public function fetchAdmin()
	{
		// collect all exams with status 2 (unchecked)
		return $this->fetch("-1", "-1", "-1", "-1", "-1", "2", false);
	}
	
	public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
		$exam = new Application_Model_Exam();
        $exam->setId($row->idexam)
             ->setComment($row->comment)
			 ->setDegreeId($row->degree_iddegree)
             ->setSemester($row->semester_idsemester)
             ->setType($row->exam_type_idexam_type)
			 ->setSubType($row->exam_sub_type_idexam_sub_type)
			 ->setDegree($row->exam_degree_idexam_degree)
			 ->setUniversity($row->university_iduniversity)
			 ->setAutor($row->autor)
			 ->setStatus($row->exam_status_idexam_status);
				  
		return $exam;
    }
	
	public function findAdmin($id)
    {
        
		$select = $this->getDbTable()->getAdapter()->select()
              ->from(array('x' => 'exam'),
                     array('idexam', 'comment', 'autor', 'degree_iddegree', 'exam_status_idexam_status',
						   'exam_type_idexam_type', 'exam_sub_type_idexam_sub_type', 'exam_degree_idexam_degree',
						   'university_iduniversity',
						   'GROUP_CONCAT(cor.idcourse) as courses', 'GROUP_CONCAT(lec.idlecturer) as lecturer',
						   'GROUP_CONCAT(sem.idsemester) as semester'))
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
              ->where('x.idexam = '. $id)
              ->group('idexam');
			  
			  $resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);
		
        if (0 == count($resultSet)) {
			//TODO: if no lecturer set, cout = 0 ... fix this
			throw new Zend_Exception ("No result (count = 0).");
            return;
        }

		foreach ($resultSet as $row) {
		$semester = explode(',' , $row['semester']);
		if(count($semester) > 0) $semester = $semester[0];
		
		$exam = new Application_Model_Exam();
        $exam->setId($row['idexam'])
				  ->setDegreeId($row['degree_iddegree'])
                  ->setSemester($semester)
                  ->setLecturer(explode(',' , $row['lecturer']))
                  ->setType($row['exam_type_idexam_type'])
                  ->setSubType($row['exam_sub_type_idexam_sub_type'])
                  ->setCourse(explode(',' , $row['courses']))
				  ->setComment($row['comment'])
				  ->setUniversity($row['university_iduniversity'])
				  ->setAutor($row['autor'])
				  ->setDegree($row['exam_degree_idexam_degree'])
                  ;
		}
		
		return $exam;
    }

    public function saveAsNewExam($exam) 
	{
        if($this->validateNewExam($exam)) {
            $data = array(
                    'semester_idsemester'           => $exam->semester,
                    'exam_type_idexam_type'         => $exam->type,
                    'exam_sub_type_idexam_sub_type' => $exam->subType,
					'degree_iddegree' 				=> $exam->degreeId,
                    //TODO autor is missing in db, fix this ;)
                    'exam_status_idexam_status'     => 1,
                    'exam_degree_idexam_degree'     => $exam->degree,
                    'university_iduniversity'       => $exam->university,
                    'comment'                       => $exam->comment,
					'create_date'					=> new Zend_Db_Expr('NOW()'),
					'modified_last_date'			=> new Zend_Db_Expr('NOW()')
                );
			
            
            
            $insert = $this->getDbTable()->insert($data);
            
            // insert the connection to the corse
            foreach ($exam->course as $course) {
				if($course != "-1") {
					$this->getDbTable()->getAdapter()->query("INSERT INTO  `exam_has_course` (`exam_idexam` ,`course_idcourse`)
															  VALUES ('".$insert."',  '".$course."')");
				}

            }
            
            // insert the connection to the lecturer
            foreach ($exam->lecturer as $lecturer) {
				if($lecturer != "-1") {
                $this->getDbTable()->getAdapter()->query("INSERT INTO  `exam_has_lecturer` (`exam_idexam` ,`lecturer_idlecturer`)
                                                         VALUES ('".$insert."',  '".$lecturer."')");
				}

            }

        } else {
            // TODO handle fail
        }
        
        return $insert;
    }
	
	public function updateExam($exam)
	{
		$exam_old = $this->findAdmin($exam->id);
		
		var_dump($exam->semester);
		
		// delete foreign
		// course
		foreach(array_diff($exam_old->course, $exam->course) as $element) {
			$this->getDbTable()->getAdapter()->query("DELETE FROM `exam_has_course` WHERE `exam_idexam` = ".$exam->id." AND `course_idcourse` = ".$element.";");
		}
		
		// lecturer
		foreach(array_diff($exam_old->Lecturer, $exam->Lecturer) as $element) {
			$this->getDbTable()->getAdapter()->query("DELETE FROM `exam_has_lecturer` WHERE `exam_idexam` = ".$exam->id." AND `lecturer_idlecturer` = ".$element.";");
		}

		
		// setup new foreign key
		// course
		foreach(array_diff($exam->course, $exam_old->course) as $element) {
			$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_has_course` (`exam_idexam` ,`course_idcourse`) VALUES ('".$exam->id."',  '".$element."')");
		}
		
		// lecturer
		foreach(array_diff($exam->Lecturer, $exam_old->Lecturer) as $element) {
			$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_has_lecturer` (`exam_idexam` ,`lecturer_idlecturer`) VALUES ('".$exam->id."',  '".$element."')");
		}

		
		// update exam
		$this->getDbTable()->getAdapter()->query("UPDATE  `exam` SET
												`semester_idsemester` =  '".$exam->Semester."',
												`exam_type_idexam_type` =  '".$exam->Type."',
												`exam_sub_type_idexam_sub_type` =  '".$exam->SubType."',
												`exam_degree_idexam_degree` =  '".$exam->Degree."',
												`university_iduniversity` =  '".$exam->university."',
												`comment` =  '".$exam->comment."',
												`autor` =  '".$exam->autor."',
												`modified_last_date` = NOW()
												WHERE  `idexam` =".$exam->id.";");
												
		
		$this->addLogMessage($exam->id, 'Exam details updated by %user%.');	

	}
	
	public function updateExamStatusToUnchecked($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '2', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
		$this->addLogMessage($examId, 'Exam files uploaded by user.');
    }
	
	public function updateExamStatusToDisapprove($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '2', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
		$this->addLogMessage($examId, 'Exam disapproved by %user%.');
		//remove the exam from the search index
		$index = new Application_Model_ExamSearch();
		$index->removeFileFromIndex($examId);
    }
	
	public function updateExamStatusToChecked($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '3', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '2';");
		$this->addLogMessage($examId, 'Exam approved by %user%.');
		// Add the exam to the search index
		$index = new Application_Model_ExamSearch();
		//TODO(aamuuninen) wait for "entmurxing", then utilize sensible keywords
 		$keywords = "foo bar";
		$index->addFileToIndex($examId, $keywords);
    }
	
	public function updateExamStatusToDelete($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '4', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '2';");
		$this->getDbTable()->getAdapter()->query("UPDATE `document` SET  `deleted` =  '1' WHERE  `exam_idexam` =".$examId.";");
		$this->addLogMessage($examId, 'Exam deleted by %user%.');
    }
    
    public function updateExamStatusToReported($examId)
    {
    	//TODO is changing the last modified date here correct?
    	$this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '5', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
    	$this->addLogMessage($examId, 'Exam was reported.');
    }
    // return true if the exam is valid, (has no id and no proboerty has a wrong value)
    private function validateNewExam($exam) 
	{
        /*
        protected $_id;
        protected $_semester;
        protected $_type;
        protected $_subType;
        protected $_lecturer;
        protected $_document;
        protected $_course;
        protected $_comment;
        protected $_degree;
        protected $_university;
        protected $_autor;
        */
        
        if($exam->Id != "") {
            return false;
        }
        if($exam->Semester == "" || !is_int((int)$exam->Semester)) {
            return false;
        }
        return true;
    }
	
	private function addLogMessage($examId, $message) {
		$this->getDbTable()->getAdapter()->query("INSERT INTO  `exam_log` (`exam_idexam` ,`message`)
															  VALUES ('".$examId."',  '".$message."')");
	}
}

