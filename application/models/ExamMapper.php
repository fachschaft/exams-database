<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

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
    		$resCount = $this->getDbTable()->find($examId)->count();
    		if($resCount == 0) {
    			throw new Exception ( "Tried to call a not existing exam", 404);
    		} else {
    			throw new Exception ( "Inconsistent database for exam id: ".$examId." - Call an admin! (count = ".count($result).")" );
    		}
    	}
    	$row = $result[0];
    	
    	$exam = $this->fillExam ($row);
 	
    	return $exam;
    }
    
	/**
	 * 
	 */private function fillExam($data) {
		// fill the exam
    	$exam = new Application_Model_Exam();
   	
    	$exam->setId($data['idexam']);
    	$exam->setAutor($data['autor']);
    	$exam->setComment($data['comment']);

    	$exam->setCreated($data['create_date']);
    	$exam->setModified($data['modified_last_date']);

    	$exam->setDegree(new Application_Model_Degree(array('id'=>$data['degree_iddegree'], 'name'=>$data['degree_name'])));
    	$exam->setStatus(new Application_Model_ExamStatus(array('id'=>$data['exam_status_idexam_status'], 'name'=>$data['status_name'])));
    	$exam->setSemester(new Application_Model_Semester(array('id'=>$data['semester_idsemester'], 'name'=>$data['semester_name'])));
    	$exam->setType(new Application_Model_ExamType(array('id'=>$data['exam_type_idexam_type'], 'name'=>$data['type_name'])));
    	$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$data['exam_sub_type_idexam_sub_type'], 'name'=>$data['sub_typ_name'])));
    	$exam->setUniversity(new Application_Model_ExamUniversity(array('id'=>$data['university_iduniversity'], 'name'=>$data['university_name'])));

    	$exam->setWrittenDegree(new Application_Model_ExamDegree(array('id'=>$data['exam_degree_idexam_degree'], 'name'=>$data['exam_degree_name'])));

    	
    	// collect all lecturer
    	$resultSetLecturer = $this->getDbTable()->find($data['idexam'])->current()
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
    	$resultSetCourse = $this->getDbTable()->find($data['idexam'])->current()
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
    	$docs = $mapper->fetchByExamId($data['idexam']);
    	$entry = array();
    	foreach ($docs as $doc)
    	{
    		$entry[] = $doc;
    	}
    	$exam->setDocuments($entry);
		return $exam;
	}

    /**
     * @param unknown_type $courseIds
     * @param unknown_type $lecturerIds
     * @param unknown_type $semesterIds
     * @param unknown_type $examTypeIds
     * @param unknown_type $degree
     * @param array $status
     * @param unknown_type $withReflexive
     * @return Application_Model_Exam
     */
    public function fetchQuick($courseIds, $lecturerIds, $semesterIds, $examTypeIds, $degree, array $status = array(), $withReflexive = true, array $examId = array('-1'))
    {

    	$explode_Group_Concat_Delimiter = "#&$#�"; // this string will be used to seperate the strings in the database, e.G. " prof 1 # prof 2 " use a string which will NOT be in an string from the selected fields
    	
    	
    	if(!is_array($courseIds) && $courseIds == "-1") $courseIds = array(); // course is "-1" so we want select all
    	if(!is_array($courseIds) && $courseIds != "-1") $courseIds = array($courseIds); // couse id is a single number, so wrap
    	if(is_array($courseIds) && in_array("-1", $courseIds)) $courseIds = array(); // in the given array is the value -1, we want selet all
    	
    	if(!is_array($lecturerIds) && $lecturerIds == "-1") $lecturerIds = array();
    	if(!is_array($lecturerIds) && $lecturerIds != "-1") $lecturerIds = array($lecturerIds);
    	if(is_array($lecturerIds) && in_array("-1", $lecturerIds)) $lecturerIds = array();
    	
    	if(!is_array($semesterIds) && $semesterIds == "-1") $semesterIds = array();
    	if(!is_array($semesterIds) && $semesterIds != "-1") $semesterIds = array($semesterIds);
    	if(is_array($semesterIds) && in_array("-1", $semesterIds)) $semesterIds = array();
    	
    	if(!is_array($examTypeIds) && $examTypeIds == "-1") $examTypeIds = array();
    	if(!is_array($examTypeIds) && $examTypeIds != "-1") $examTypeIds = array($examTypeIds);
    	if(is_array($examTypeIds) && in_array("-1", $examTypeIds)) $examTypeIds = array();
    	
    	if(!is_array($examId) && $examId == "-1") $examId = array();
    	if(!is_array($examId) && $examId != "-1") $examId = array($examId);
    	if(is_array($examId) && in_array("-1", $examId)) $examId = array();
    	
    	// WHERE COURSE
    	$where_base_elements = array();
    	$where_course = "";
    	
    	// DEGREE
    	if($degree != -1)
    	$where_base_elements[] = "degree.iddegree = ".$degree;
    	
    	// COURSE
    	if(is_array($courseIds) && !empty($courseIds))
    		$where_base_elements[] = "course.idcourse IN (".implode(',', $courseIds).")";
    	
    	if(!empty($where_base_elements))
    	$where_course = "WHERE ".implode(" AND ", $where_base_elements);
    	
    	
    	// WHERE BASE   	
    	$where_base = "";
    	$where_lecturer_emelents = array();
    	$join_lecturer = "";
    	
    	// LECTURER
    	if(is_array($lecturerIds) && !empty($lecturerIds)) {
    		$where_lecturer_emelents[] = "lecturer.idlecturer IN (".implode(',', $lecturerIds).")";
    		$join_lecturer =   "JOIN exam_has_lecturer ON exam_has_lecturer.exam_idexam = exam.idexam
    							JOIN lecturer ON lecturer.idlecturer = exam_has_lecturer.lecturer_idlecturer";
    	}
    	
    	// SEMESTER
    	if(is_array($semesterIds) && !empty($semesterIds))
    		$where_lecturer_emelents[] = "semester.idsemester IN (".implode(',', $semesterIds).")";
    	
    	// EXAM TYPE
    	if(is_array($examTypeIds) && !empty($examTypeIds))
    		$where_lecturer_emelents[] = "exam_type.idexam_type IN (".implode(',', $examTypeIds).")";
    	
    	// EXAM Status
    	if(!empty($status))
    		$where_lecturer_emelents[] = "exam.exam_status_idexam_status IN (".implode(',', $status).")";
    	
    	// for single Exams only:
    	if(!empty($examId))
    	{
    		$where_course = "";
    		$where_lecturer_emelents = array();
    		$where_lecturer_emelents[] = "exam.idexam IN (".implode(',', $examId).")";
    	}

    	if(!empty($where_lecturer_emelents))
    		$where_base = "WHERE ".implode(" AND ", $where_lecturer_emelents);
    	
    	
    	
    	// DEFINE REFLEXIV COURSE TO COURSE SELECT
    	$reflexiv_part = "
    		UNION
			
				Select base_courses.idcourse, course.idcourse as course1 FROM ( SELECT idcourse FROM `course`
				JOIN `degree_has_course` ON course.idcourse = degree_has_course.course_idcourse 
				JOIN degree ON degree.iddegree = degree_has_course.degree_iddegree
			
				".$where_course."
				GROUP BY course.idcourse ) AS base_courses
			
				# JOIN THE BASE COURSE WITH THE COURSE HAS COURSE TABLE
				LEFT JOIN course_has_course ON base_courses.idcourse = course_has_course.course_idcourse
				LEFT JOIN course ON course_has_course.course_idcourse1 = course.idcourse
				WHERE course.idcourse IS NOT NULL
			
			UNION
			
				Select base_courses.idcourse, course.idcourse as course1 FROM ( SELECT idcourse FROM `course`
				JOIN `degree_has_course` ON course.idcourse = degree_has_course.course_idcourse 
				JOIN degree ON degree.iddegree = degree_has_course.degree_iddegree
			
				".$where_course."
				GROUP BY course.idcourse ) AS base_courses
			
				# JOIN THE BASE COURSE WITH THE COURSE HAS COURSE TABLE
				LEFT JOIN course_has_course ON base_courses.idcourse = course_has_course.course_idcourse1
				LEFT JOIN course ON course_has_course.course_idcourse = course.idcourse
				WHERE course.idcourse IS NOT NULL
		";
    	
    	if(!$withReflexive) $reflexiv_part = "";
    	
    	/*
    	 SELECT idexam, 
			autor, 
			comment,
			create_date,
			modified_last_date,
			degree_iddegree,
			exam_status_idexam_status,
			semester_idsemester,
			exam_type_idexam_type,
			exam_sub_type_idexam_sub_type,
			university_iduniversity,
			exam_degree_idexam_degree,
			semester.name as semester_name,
			university.name as university_name, 
			exam_type.name as type_name,
			degree.name as degree_name,
			exam_sub_type.name as sub_typ_name,
			exam_status.name as status_name,
			exam_degree.name as exam_degree_name,
			
			GROUP_CONCAT(idcourse SEPARATOR ',') as course_idsX,
			GROUP_CONCAT(course_ids SEPARATOR ',') as course_idsX2
			    				
			FROM
			(	

			SELECT course_union.idcourse , GROUP_CONCAT(course_union.course1 SEPARATOR ',') as course_ids, count(course_union.course1) as count FROM 
				(
			
					SELECT idcourse, NULL as course1 FROM `course`
						JOIN `degree_has_course` ON course.idcourse = degree_has_course.course_idcourse 
						JOIN degree ON degree.iddegree = degree_has_course.degree_iddegree
						WHERE degree.iddegree = 14
						GROUP BY course.idcourse
					
					UNION
					
						SELECT base_courses.idcourse,  course.idcourse as course1 FROM 
							( 
							SELECT idcourse FROM `course`
								JOIN `degree_has_course` ON course.idcourse = degree_has_course.course_idcourse 
								JOIN degree ON degree.iddegree = degree_has_course.degree_iddegree
								WHERE degree.iddegree = 14
								GROUP BY course.idcourse 
							) AS base_courses
								
						# JOIN THE BASE COURSE WITH THE COURSE HAS COURSE TABLE
						LEFT JOIN course_has_course ON base_courses.idcourse = course_has_course.course_idcourse
						LEFT JOIN course ON course_has_course.course_idcourse1 = course.idcourse
						WHERE course.idcourse IS NOT NULL
								
					UNION
								
						SELECT base_courses.idcourse, course.idcourse as course1 FROM 
							( 
							SELECT idcourse FROM `course`
								JOIN `degree_has_course` ON course.idcourse = degree_has_course.course_idcourse 
								JOIN degree ON degree.iddegree = degree_has_course.degree_iddegree
								WHERE degree.iddegree = 14
								GROUP BY course.idcourse 
							) AS base_courses
								
						# JOIN THE BASE COURSE WITH THE COURSE HAS COURSE TABLE
						LEFT JOIN course_has_course ON base_courses.idcourse = course_has_course.course_idcourse1
						LEFT JOIN course ON course_has_course.course_idcourse = course.idcourse
						WHERE course.idcourse IS NOT NULL
					) as course_union
					GROUP BY idcourse
			) as course
			
			# JOIN THE COURSES WITH THE EXAMS
			JOIN exam_has_course ON exam_has_course.course_idcourse = course.idcourse
			JOIN exam ON exam.idexam = exam_has_course.exam_idexam
			  				
			# JOIN WITH ALL 1:N ATTRIBUTES
			JOIN semester ON semester.idsemester = exam.semester_idsemester
			JOIN university ON university.iduniversity = exam.university_iduniversity
			JOIN exam_type ON exam_type.idexam_type = exam.exam_type_idexam_type
			JOIN exam_sub_type ON exam_sub_type.idexam_sub_type = exam.exam_sub_type_idexam_sub_type
			JOIN exam_status ON exam_status.idexam_status = exam.exam_status_idexam_status
			JOIN exam_degree ON exam_degree.idexam_degree = exam.exam_degree_idexam_degree
			JOIN degree ON degree.iddegree = exam.degree_iddegree
			
			GROUP BY exam.idexam
			
			ORDER BY semester.idsemester DESC
    	 */
    	
    	$resultSet = $this->getDbTable()->getAdapter()->query("
    			SELECT
    			idexam,
    			autor,
    			comment,
    			create_date,
    			modified_last_date,
    			degree_iddegree,
    			exam_status_idexam_status,
    			semester_idsemester,
    			exam_type_idexam_type,
    			exam_sub_type_idexam_sub_type,
    			university_iduniversity,
    			exam_degree_idexam_degree,
    			semester.name as semester_name,
    			university.name as university_name,
    			exam_type.name as type_name,
    			degree.name as degree_name,
    			exam_sub_type.name as sub_typ_name,
    			exam_status.name as status_name,
    			exam_degree.name as exam_degree_name,
    			
    			GROUP_CONCAT(course.idcourse SEPARATOR '".$explode_Group_Concat_Delimiter."') as course_ids,
    			
    			GROUP_CONCAT(lecturer.idlecturer SEPARATOR '".$explode_Group_Concat_Delimiter."') as lecturer_ids,
    			
    			GROUP_CONCAT(document.iddocument SEPARATOR '".$explode_Group_Concat_Delimiter."') as document_ids,
    			    			
    			GROUP_CONCAT(idcourse SEPARATOR '".$explode_Group_Concat_Delimiter."') as course_idsX,
    			GROUP_CONCAT(course_ids2 SEPARATOR '".$explode_Group_Concat_Delimiter."') as course_idsX2
    				
    			FROM
    				
    			(
    			#SELECT idcourse, course_ids2 FROM
				#(
    			
    			SELECT course_union.idcourse, GROUP_CONCAT(course_union.course1 SEPARATOR '".$explode_Group_Concat_Delimiter."') as course_ids2 
					FROM 
    				(
    			
	    			SELECT 
	    			idcourse,
    				NULL as course1
	    			
	
	    			FROM `course`
	    			JOIN `degree_has_course` ON course.idcourse = degree_has_course.course_idcourse
	    			JOIN degree ON degree.iddegree = degree_has_course.degree_iddegree
	    				
	    			".$where_course."
	    			GROUP BY course.idcourse
	    				
	    			".$reflexiv_part."
	    				
	    			
    			
    			) as course_union
    			GROUP BY idcourse
    			#) as course
    			) as course
    			
    				
    			# JOIN THE COURSES WITH THE EXAMS
    			JOIN exam_has_course ON exam_has_course.course_idcourse = course.idcourse
    			JOIN exam ON exam.idexam = exam_has_course.exam_idexam
    				
    			# JOIN WITH ALL 1:N ATTRIBUTES
    			JOIN semester ON semester.idsemester = exam.semester_idsemester
    			JOIN university ON university.iduniversity = exam.university_iduniversity
    			JOIN exam_type ON exam_type.idexam_type = exam.exam_type_idexam_type
    			JOIN exam_sub_type ON exam_sub_type.idexam_sub_type = exam.exam_sub_type_idexam_sub_type
    			JOIN exam_status ON exam_status.idexam_status = exam.exam_status_idexam_status
    			JOIN exam_degree ON exam_degree.idexam_degree = exam.exam_degree_idexam_degree
    			JOIN degree ON degree.iddegree = exam.degree_iddegree
    			 
    			JOIN exam_has_lecturer ON exam_has_lecturer.exam_idexam = exam.idexam
    			JOIN lecturer ON lecturer.idlecturer = exam_has_lecturer.lecturer_idlecturer
    			
    			LEFT JOIN document ON document.exam_idexam = exam.idexam
    			
    			 
    			".$where_base."
    				
    				
    			GROUP BY exam.idexam
    			 
    			ORDER BY semester.idsemester DESC, exam.idexam DESC
    			");
    	
		
    	$entries	= array();
    	$cor_ids	= array();
    	$corC_ids	= array();
    	$lec_ids	= array();
    	$doc_ids 	= array();
    	
    	// fill the exam
    	foreach ($resultSet as $data) {
    		$exam = new Application_Model_Exam();
	    	
	    	$exam->setId($data['idexam']);
	    	$exam->setAutor($data['autor']);
	    	$exam->setComment($data['comment']);
	    	
	    	$exam->setCreated($data['create_date']);
	    	$exam->setModified($data['modified_last_date']);
	    	
	    	$exam->setDegree(new Application_Model_Degree(array('id'=>$data['degree_iddegree'], 'name'=>$data['degree_name'])));
	    	$exam->setStatus(new Application_Model_ExamStatus(array('id'=>$data['exam_status_idexam_status'], 'name'=>$data['status_name'])));
	    	$exam->setSemester(new Application_Model_Semester(array('id'=>$data['semester_idsemester'], 'name'=>$data['semester_name'])));
	    	$exam->setType(new Application_Model_ExamType(array('id'=>$data['exam_type_idexam_type'], 'name'=>$data['type_name'])));
	    	$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$data['exam_sub_type_idexam_sub_type'], 'name'=>$data['sub_typ_name'])));
	    	$exam->setUniversity(new Application_Model_ExamUniversity(array('id'=>$data['university_iduniversity'], 'name'=>$data['university_name'])));
	    	
	    	$exam->setWrittenDegree(new Application_Model_ExamDegree(array('id'=>$data['exam_degree_idexam_degree'], 'name'=>$data['exam_degree_name'])));
	    	
	    	$exam->setCourse(array());
	    	$exam->setCourseConnected(array());
	    	
	    	
	    	$entries[$data['idexam']] = $exam;
	    	
	    	$cor_ids[$data['idexam']] = explode($explode_Group_Concat_Delimiter, $data['course_idsX']);
	    	
	    	$corC_ids[$data['idexam']] = explode($explode_Group_Concat_Delimiter, $data['course_idsX2']);
	    	
	    	$lec_ids[$data['idexam']] = explode($explode_Group_Concat_Delimiter, $data['lecturer_ids']);
	    	
	    	if(isset($data['document_ids'])) {
	    		$doc_ids[$data['idexam']] = explode($explode_Group_Concat_Delimiter, $data['document_ids']);
	    	}
	    	
	    	
	    	
    	}
    	
    	
    	// grab all course and store them
    	if(isset($cor_ids) && !empty($cor_ids)) {
	    	$cor_idsX = array();
	    	foreach ($cor_ids as $ids)
	    	{
	    		foreach($ids as $id)
	    		{
	    			$cor_idsX[] = $id;
	    		}
	    	}
	
	    	if(!empty($cor_idsX)) {
		    	$resCor = $this->getDbTable()->getAdapter()->query("SELECT * FROM `course` WHERE `idcourse` IN (".implode(',', $cor_idsX).")");
		    	
		    	foreach($resCor as $lec)
		    	{
		    		foreach ($cor_ids as $key => $ids)
		    		{
		    			if(in_array($lec['idcourse'], $ids))
		    			{
		    				$origin_courses[] = $lec['idcourse']; // collect all origin courses
		    				$entries[$key]->addCourse(new Application_Model_Course(
		    						array('id'=>$lec['idcourse'], 'name'=>$lec['name'])
		    				)
		    				);
		    			}
		    		}
		    	}
	    	}    	
    	}
    	
    	
    	
    	// grab all connected course and store them
    	if(isset($corC_ids) && !empty($corC_ids)) {
	    	$corC_idsX = array();
	    	foreach ($corC_ids as $ids)
	    	{
	    		foreach($ids as $id)
	    		{
	    			if($id != "")
	    				$corC_idsX[] = $id;
	    		}
	    	}
	    	
	    	if(!empty($corC_idsX)) {
		    	$resCorC = $this->getDbTable()->getAdapter()->query("SELECT * FROM `course` WHERE `idcourse` IN (".implode(',', $corC_idsX).")");
		    	 
		    	foreach($resCorC as $lec)
		    	{
		    		foreach ($corC_ids as $key => $ids)
		    		{
		    			if(in_array($lec['idcourse'], $ids))
		    			{
		    				// check if the cours is not in the origin courses
		    				$found = false;
		    				foreach($entries[$key]->getCourse() as $cors)
		    				{
		    					if($cors->id == $lec['idcourse']) $found = true;
		    				}
		    				if(!$found) {
			    				$entries[$key]->addCourseConnected(new Application_Model_Course(
			    						array('id'=>$lec['idcourse'], 'name'=>$lec['name'])
			    				)
			    				);
		    				}
		    			}
		    		}
		    	}
	    	}
    	}
    	
    	// grab all lecturer and store them
    	if(isset($lec_ids) && !empty($lec_ids)) {
	    	$lec_idsX = array();
	    	foreach ($lec_ids as $ids)
	    	{
	    		foreach($ids as $id)
	    		{
	    			$lec_idsX[] = $id;
	    		}
	    	}
	    	
	    	if(!empty($lec_idsX)) {
		    	$resLec = $this->getDbTable()->getAdapter()->query("SELECT * FROM `lecturer` WHERE `idlecturer` IN (".implode(',', $lec_idsX).")");
		    	
		    	foreach($resLec as $lec)
		    	{
		    		foreach ($lec_ids as $key => $ids)
		    		{
		    			if(in_array($lec['idlecturer'], $ids))
		    			{
		    				$entries[$key]->addLecturer(new Application_Model_Lecturer(
		    						array('id'=>$lec['idlecturer'], 'name'=>$lec['name'], 'degree'=>$lec['degree'], 'firstName'=>$lec['first_name'])
		    				)
		    				);
		    			}
		    		}
		    	}
	    	}
    	}
    	
    	// grab all documents and store them
    	if(isset($doc_ids) && !empty($doc_ids) && $doc_ids != "") {
	    	$doc_idsX = array();
	    	foreach ($doc_ids as $ids)
	    	{
	    		foreach($ids as $id)
	    		{
	    			$doc_idsX[] = $id;
	    		}
	    	}

	    	if(!empty($doc_idsX)) {
		    	$resDocs = $this->getDbTable()->getAdapter()->query("SELECT * FROM `document` WHERE `deleted` = 0 AND `iddocument` IN (".implode(',', $doc_idsX).")");
		    	
		    	foreach($resDocs as $doc)
		    	{
		    		foreach ($doc_ids as $key => $ids)
		    		{
		    			if(in_array($doc['iddocument'], $ids))
		    			{ 
		    				$entries[$key]->addDocuments(new Application_Model_Document(
		    					array('id'=>$doc['iddocument'], 'extention'=>$doc['extention'], 'submitFileName'=>$doc['submit_file_name'],
		    							'fileName'=>$doc['file_name'], 'mimeType'=>$doc['mime_type'], 'uploadDate'=>$doc['upload_date'],
		    							'ExamId'=>$doc['exam_idexam'], 'DeleteState'=>$doc['deleted'],'DisplayName'=>$doc['display_name'], 'Collection'=>$doc['collection'])
		    					)
		    				); 
		    			}
		    		}
	    		}
	    	}
    	}
    	
    	return $entries;
    }
	
	public function fetchUnchecked()
	{
		return $this->fetchQuick("-1", "-1", "-1", "-1", "-1", array(Application_Model_ExamStatus::Unchecked), false);
	}
	
	public function countUnchecked()
	{
		$resDocs = $this->getDbTable()->getAdapter()->query("SELECT count(*) as count FROM `exam` WHERE `exam_status_idexam_status` = " . Application_Model_ExamStatus::Unchecked);
		$docCount = 0;
		foreach($resDocs as $doc)
		{
			$docCount = $doc['count'];
		}
		return $docCount;
	}
	
	public function fetchReported()
	{
		return $this->fetchQuick("-1", "-1", "-1", "-1", "-1", array(Application_Model_ExamStatus::Reported), false);
	}
	
	public function fetchPublic(){
		return $this->fetchQuick("-1", "-1", "-1", "-1", "-1", array(Application_Model_ExamStatus::PublicExam, Application_Model_ExamStatus::Reported), false);
	}
	
	public function find($id)
    {
        //return $this->getExam($id);
        $entrys = $this->fetchQuick(-1, -1, -1, -1, -1, array(), true, array($id));
        if (count($entrys) != 1) {
        	$resCount = $this->getDbTable()->find($id)->count();
        	if($resCount == 0) {
        		throw new Exception ( "Tried to call a not existing exam", 404);
        	} else {
        		throw new Exception ( "Inconsistent database for exam id: ".$id." - Call an admin! (count = ".count($entrys).")" );
        	}
    	}
    	return current($entrys);
    	
    	  	
    	
    	/*$data = $this->getDbTable()->find($id)->current();
    	
    	$exam = new Application_Model_Exam();
    	
    	$exam->setId($data['idexam']);
    	$exam->setAutor($data['autor']);
    	$exam->setComment($data['comment']);
    	
    	$exam->setCreated($data['create_date']);
    	$exam->setModified($data['modified_last_date']);
    	
    	$exam->setDegree(new Application_Model_Degree(array('id'=>$data['degree_iddegree'])));
    	$exam->setStatus(new Application_Model_ExamStatus(array('id'=>$data['exam_status_idexam_status'])));
    	$exam->setSemester(new Application_Model_Semester(array('id'=>$data['semester_idsemester'])));
    	$exam->setType(new Application_Model_ExamType(array('id'=>$data['exam_type_idexam_type'])));
    	$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$data['exam_sub_type_idexam_sub_type'])));
    	$exam->setUniversity(new Application_Model_ExamUniversity(array('id'=>$data['university_iduniversity'])));
    	
    	$exam->setWrittenDegree(new Application_Model_ExamDegree(array('id'=>$data['exam_degree_idexam_degree'])));
    	
    	$exam->setCourse(array());
    	$exam->setCourseConnected(array());
    	
    	
    	$course_mapper = new Application_Model_CourseMapper();
    	$exam->setCourse($course_mapper->find($exam->getId()));
    	
    	
    	return $exam;*/
    	
    }
    
    public function findUpload($id)
    {
    	$data = $this->getDbTable()->find($id)->current();
    	
    	$exam = new Application_Model_Exam();
    	
    	$exam->setId($data['idexam']);
    	$exam->setAutor($data['autor']);
    	$exam->setComment($data['comment']);
    	
    	$exam->setCreated($data['create_date']);
    	$exam->setModified($data['modified_last_date']);
    	
    	$exam->setDegree(new Application_Model_Degree(array('id'=>$data['degree_iddegree'])));
    	$exam->setStatus(new Application_Model_ExamStatus(array('id'=>$data['exam_status_idexam_status'])));
    	$exam->setSemester(new Application_Model_Semester(array('id'=>$data['semester_idsemester'])));
    	$exam->setType(new Application_Model_ExamType(array('id'=>$data['exam_type_idexam_type'])));
    	$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$data['exam_sub_type_idexam_sub_type'])));
    	$exam->setUniversity(new Application_Model_ExamUniversity(array('id'=>$data['university_iduniversity'])));
    	
    	$exam->setWrittenDegree(new Application_Model_ExamDegree(array('id'=>$data['exam_degree_idexam_degree'])));
    	
    	$exam->setCourse(array());
    	$exam->setCourseConnected(array());
    	
    	return $exam;
    	
    	
    }
		
	public function saveAsNewExam($exam) {
		$data = array (
				'semester_idsemester' 			=> $exam->semester->id,
				'exam_type_idexam_type' 		=> $exam->type->id,
				'exam_sub_type_idexam_sub_type' => $exam->subType->id,
				'degree_iddegree' 				=> $exam->degree->id,
				'autor'							=> $exam->autor,
				'exam_status_idexam_status' 	=> Application_Model_ExamStatus::NothingUploaded,
				'exam_degree_idexam_degree' 	=> $exam->writtenDegree->id,
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
												
		
		Application_Model_ExamLogManager::addLogMessage($exam->id, 'Exam details updated by %user%.');	

	}
	
	public function updateExamStatusToUnchecked($examId) 
	{
		$ex = new Application_Model_Exam();
		
		try { 
		$exam = new Application_Model_ExamMapper();
		$ex = $exam->find($examId);
		} catch (Exception $e) {
		}
		
		
		try {
		$noti = new Application_Model_Notification();
		$noti->sendNotification("New unchecked Exam " . date('Y-m-d H:i:s'), "New unchecked Exam " . date('Y-m-d H:i:s')."\n".$ex->getCourse()[0]->getName() ." von ".$ex->getLecturer()[0]->getName() . " im " . $ex->getSemester());
		} catch (Exception $e) {
			//ToDo: handle this
		}
		
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
		Application_Model_ExamLogManager::addLogMessage($examId, 'Exam files uploaded.');
    }
	
	public function updateExamStatusToDisapprove($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."', `modified_last_date` = NOW() WHERE `idexam` =".$examId.";");
		Application_Model_ExamLogManager::addLogMessage($examId, 'Exam disapproved by %user%.');
		//remove the exam from the search index
		$index = new Application_Model_ExamSearch();
		$index->removeFileFromIndex($examId);
    }
	
	public function updateExamStatusToChecked($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::PublicExam."', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."';");
		Application_Model_ExamLogManager::addLogMessage($examId, 'Exam approved by %user%.');
		// Add the exam to the search index
		$index = new Application_Model_ExamSearch();
		//TODO(aamuuninen) wait for "entmurxing", then utilize sensible keywords
		$index->addFileToIndex($examId);
    }
	
	public function updateExamStatusToDelete($examId) 
	{
        $this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Deleted."', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Unchecked."';");
		$this->getDbTable()->getAdapter()->query("UPDATE `document` SET  `deleted` =  '1' WHERE  `exam_idexam` =".$examId.";");
		Application_Model_ExamLogManager::addLogMessage($examId, 'Exam deleted by %user%.');
    }
    
    public function updateExamStatusToReported($examId, $reason)
    {
    	$ex = new Application_Model_Exam();
    	
    	try {
    		$exam = new Application_Model_ExamMapper();
    		$ex = $exam->find($examId);
    	} catch (Exception $e) {
    	}
    	
    	
    	try {
    		$noti = new Application_Model_Notification();
    		$noti->sendNotification("New reported Exam " . date('Y-m-d H:i:s'), "New reported Exam " . date('Y-m-d H:i:s')."\n".$ex->getCourse()[0]->getName() ." by ".$ex->getLecturer()[0]->getName(). ", " . $ex->getLecturer()[0]->getDegree() . " in " . $ex->getSemester()."\n"."Reason: ".$reason);
    	} catch (Exception $e) {
    		//ToDo: handle this
    	}
    	
    	//TODO is changing the last modified date here correct?
    	$this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Reported."' WHERE `idexam` =".$examId.";");
    	Application_Model_ExamLogManager::addLogMessage($examId, 'Exam was reported with reason ' . $reason . '.');
    }
    
    public function updateExamStatusUnreport($examId)
    {
    	$this->getDbTable()->getAdapter()->query("UPDATE `exam` SET `exam_status_idexam_status` =  '".Application_Model_ExamStatus::PublicExam."', `modified_last_date` = NOW() WHERE `idexam` =".$examId." AND `exam_status_idexam_status` =  '".Application_Model_ExamStatus::Reported."';");
    	Application_Model_ExamLogManager::addLogMessage($examId, 'Exam unreport by %user%.');
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
	
	
	public function checkDatabaseForInconsistetExams()
	{
		set_time_limit(0);
		$res = $this->getDbTable()->fetchAll();
		
		echo "<p>checked trough ". count($res) ." exams.</p><br>";
		
		foreach($res as $ex)
		{
			try {
				$this->find($ex['idexam']);
			} catch (Exception $e) {
				echo '<p>' . $e->getMessage() . '</p><lu>';
				
				if($ex['exam_status_idexam_status'] == 1) {
					echo "<li>no files uploaded</li>";
					$doc = new Application_Model_DbTable_Document();
					$doc_elements = $doc->fetchAll("exam_idexam = ". $ex['idexam']);
					if(count($doc_elements) != 0) {
						echo "<li>status is no files uploaded, but there connected files!</li>";
					}
				}
				
				// check if lecturer relation is ok
				$ehl = new Application_Model_DbTable_ExamHasLecturer();
				$ehl_elements = $ehl->fetchAll("exam_idexam = " . $ex['idexam']);
 				
					// check if ExamHasLecturer as wrong entrys
					if(count($ehl_elements) == 0)
					{
						echo ("<li>no connection in ExamHasLecturer table</li>");
					} else {
						// check if the found lecturer exists
						foreach ($ehl_elements as $ehl_element)
						{
							$lec = new Application_Model_DbTable_Lecturer();
							$lec_elements = $lec->find($ehl_element['lecturer_idlecturer']);
							if(count($lec_elements) == 0) {
								echo "<li>no lecturer found with id (".$ehl_element['lecturer_idlecturer'].") wrong entry in ExamHasLecturer?</li>";
							} else {
								if(count($lec_elements) > 1) {
									echo "<li>more than one lecturer? this can't be! (".$ehl_element['lecturer_idlecturer'].")</li>";
								} else {
									// check if the lecturer is connected to one or more degrees
									$dhl = new Application_Model_DbTable_DegreeHasLecturer();
									$lec_element = $lec_elements->current();
									$dhl_elements = $dhl->fetchAll("lecturer_idlecturer = " . $lec_element['idlecturer']);
									if(count($dhl_elements) == 0) {
										echo ("<li>no connection in DegreeHasLecturer table for lecturer id: (".$lec_element['idlecturer'].")</li>");
									} else {
									
									}
									
								}
							}
						}
					}
					
					// check if the relation to a cours is ok
					$ehc = new Application_Model_DbTable_ExamHasCourse();
					$ehc_elements = $ehc->fetchAll("exam_idexam = " . $ex['idexam']);
					if(count($ehc_elements) == 0)
					{
						echo ("<li>no connection in ExamHasCourse table</li>");
					} else {
						// check if the found course exists
						foreach ($ehc_elements as $ehc_element)
						{
							$cor = new Application_Model_DbTable_Course();
							$cor_elements = $cor->find($ehc_element['course_idcourse']);
							if(count($cor_elements) == 0) {
								echo "<li>no course found with id (".$ehc_element['course_idcourse'].") wrong entry in ExamHasCourse?</li>";
							} else {
								if(count($cor_elements) > 1) {
									echo "<li>more than one course? this can't be! (".$ehc_element['course_idcourse'].")</li>";
								} else {
									// ther is one course so check if the connections to the degrees exists
									$dhc = new Application_Model_DbTable_DegreeHasCourse();
									$cor_element = $cor_elements->current();
									$dhc_elements = $dhc->fetchAll("course_idcourse = " . $cor_element['idcourse']);
									if(count($dhc_elements) == 0) {
										echo ("<li>no connection in DegreeHasCourse table for course id: (".$cor_element['idcourse'].")</li>");
									} else {
										foreach($dhc_elements as $dhc_element)
										{
											$deg = new Application_Model_DbTable_Degree();
											$deg_elements = $deg->find($dhc_element['degree_iddegree']);
											if(count($deg_elements) == 0) {
												echo "<li>no degree found with id (".$dhc_element['degree_iddegree'].") wrong entry in DegreeHasCourse?</li>";
											}
										}
									}
								}
								
							}
						}
					}
					
					

					
				
				echo '</lu><br>';
			}
			
		}
		
		echo "<p>finished</p><br>";
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

