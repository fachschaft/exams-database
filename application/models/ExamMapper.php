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
    public function fetch($courseIds, $lecturerIds, $semesterIds, $examTypeIds, $degree)
    {  

        $select = $this->getDbTable()->getAdapter()->select()
              ->from(array('x' => 'exam'),
                     array('idexam', 'comment', 'sem.name as semester_name', 'GROUP_CONCAT(lec.idlecturer) as lecturer', 'ext.name as type_name', 'est.name as sub_typ_name', 'GROUP_CONCAT(cor.idcourse) as courses'))
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
              ->join(array('chc' => 'course_has_course'),'')
              ->where('(cor.idcourse = chc.course_idcourse1) OR
                        (cor.idcourse = chc.course_idcourse)')
              ->group('idexam');

        if((!is_array($courseIds) && $courseIds != -1) || (is_array($courseIds) && !in_array(-1, $courseIds)))
        {
            $select->where('chc.course_idcourse1 IN (?) OR chc.course_idcourse in (?)', $courseIds);
        } else {
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
            
            $coursesIds = split(",", $row['courses']);
            
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
                $courses[$cours['idcourse']] = $cours['name'];
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
                $lecturers[$id] = $lect['name'] .', '. $lect['degree'] . ' ' . $lect['first_name'];
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
                  ;
            $entries[] = $entry;
            
            
            
            
        }

        return $entries;
    }

    public function saveAsNewExam($exam) {
        if($this->validateNewExam($exam)) {
            
            $data = array(
                    'semester_idsemester'           => $exam->semester,
                    'exam_type_idexam_type'         => $exam->type,
                    'exam_sub_type_idexam_sub_type' => $exam->subType,
                    // autor is missing in db, fix this ;)
                    'exam_status_idexam_status'     => 4,
                    'exam_degree_idexam_degree'     => $exam->degree,
                    'university_iduniversity'       => $exam->university,
                    'comment'                       => $exam->comment
                );
            
            
            $insert = $this->getDbTable()->insert($data);
            
            // insert the connection to the corse
            foreach ($exam->course as $course) {
                $this->getDbTable()->getAdapter()->query("INSERT INTO  `exam_has_course` (`exam_idexam` ,`course_idcourse`)
                                                         VALUES ('".$insert."',  '".$course."')");

            }
            
            // insert the connection to the lecturer
            foreach ($exam->lecturer as $lecturer) {
                $this->getDbTable()->getAdapter()->query("INSERT INTO  `exam_has_lecturer` (`exam_idexam` ,`lecturer_idlecturer`)
                                                         VALUES ('".$insert."',  '".$lecturer."')");

            }

        } else {
            // handle fail
        }
        
        return $insert;
    }
    
    // return true if the exam is valid, (has no id and no proboerty has a wrong value)
    private function validateNewExam($exam) {
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
}

