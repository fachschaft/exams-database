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
    public function fetch($courseIds, $lecturerIds, $semesterIds, $examTypeIds)
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
                     'ext.idexam_type = x.exame_type_idexame_type')
              ->join(array('est' => 'exam_sub_type'),
                     'est.idexam_sub_type = x.exame_sub_type_idexame_sub_type')
              ->join(array('ehcg' => 'exam_has_course_group'),
                     'ehcg.exam_idexam = x.idexam')
              ->join(array('cog' => 'course_group'),
                     'cog.idcourse_group = ehcg.course_group_idcourse_group')
              ->join(array('cor' => 'course'),
                     'cor.courses_group_idcourses_group = cog.idcourse_group')
              ->group('idexam');

        if(!empty($courseIds) && $courseIds != -1 && !in_array(-1, $courseIds))
            $select->where('cor.idcourse IN (?)', $courseIds);
        if(!empty($lecturerIds) && $lecturerIds != -1 && !in_array(-1, $lecturerIds))
            $select->where('lec.idlecturer IN (?)', $lecturerIds);
        if(!empty($semesterIds) && $semesterIds != -1 && !in_array(-1, $semesterIds))
            $select->where('sem.idsemester IN (?)', $semesterIds);
        if(!empty($examTypeIds) && $examTypeIds != -1 && !in_array(-1, $examTypeIds))
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
              ->join(array('ehcg' => 'exam_has_course_group'),
                     'ehcg.exam_idexam = x.idexam')
              ->join(array('cog' => 'course_group'),
                     'cog.idcourse_group = ehcg.course_group_idcourse_group')
              ->join(array('cor' => 'course'),
                     'cor.courses_group_idcourses_group = cog.idcourse_group')
              ->where('idexam = ?', $row['idexam']);
              $resultSetCourses = $this->getDbTable()->getAdapter()->fetchAll($selectCourse);
            $courses = array();
            foreach($resultSetCourses as $id => $cours)
            {
                $courses[$id] = $cours['name'];
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
                $lecturers[$id] = $lect['name'];
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

