<?php
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.1
 * @since		1.1
 * @todo		-
 */

class Application_Model_CourseExaminationMapper {
	
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
			$this->setDbTable('Application_Model_DbTable_CourseExamination');
		}
		return $this->_dbTable;
	}
	
	
	public function addCourseExamination($course, $date, $comment="") {
		$dbTable = $this->getDbTable();
		//'1',  '2013-12-24',  'lol'
	
		$dbTable->getAdapter()->query("INSERT INTO  `course_examination` (`idcourse` ,`examination_date`, `comment`)
				VALUES ('".$course."',  '".$date."',  '".$comment."')");
	}
	
	public function deleteCourseExamination($id) {
		$dbTable = $this->getDbTable();
	
		$dbTable->getAdapter()->query("DELETE FROM `exams-database`.`course_examination` WHERE `course_examination`.`idcourse_examination` = ".$id.";");
	}
}

?>