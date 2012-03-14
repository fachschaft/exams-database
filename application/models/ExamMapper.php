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
              ->group('idexam');

        if((!is_array($courseIds) && $courseIds != -1) || (is_array($courseIds) && !in_array(-1, $courseIds)))
        {
            $select->where('cor.idcourse IN (?)', $courseIds);
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
                     array('idexam', 'cor.name as name'))
              ->join(array('ehcg' => 'exam_has_course'),
                     'ehcg.exam_idexam = x.idexam')
              ->join(array('cor' => 'course'),
                     'cor.idcourse = ehcg.course_idcourse')
              ->where('idexam = ?', $row['idexam']);
              $resultSetCourses = $this->getDbTable()->getAdapter()->fetchAll($selectCourse);
            $courses = array();
            foreach($resultSetCourses as $id => $cours)
            {
                $courses[$id] = $cours['name'];
            }
            
            // collect all related courses
            //ToDo:
            
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
            
            
            $entry->setId($row['idexam'])
                  ->setSemester($row['semester_name'])
                  ->setLecturer($lecturers)
                  ->setType($row['type_name'])
                  ->setSubType($row['sub_typ_name'])
                  ->setCourse($courses)
                  ;
            $entries[] = $entry;
        }

        return $entries;
    }

}

