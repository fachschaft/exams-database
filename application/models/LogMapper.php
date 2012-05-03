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

class Application_Model_LogMapper
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
            $this->setDbTable('Application_Model_DbTable_ExamLog');
        }
        return $this->_dbTable;
    }
	
	public function fetchByExam($examId)
	{
		$select = $this->getDbTable()->getAdapter()->select()
						->from(array('log' => 'exam_log'),
						       array('idexam_log', 'exam_idexam', 'message', 'date'))
						->where('log.exam_idexam = '. $examId);
						
		$resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);
		$log = new Application_Model_Log();
		$messages = array();
		foreach ($resultSet as $row) {
			$messages[] = $row['date'] . " - " . $row['message'];
		}
		
		$log->setlogMessages($messages);
		
		return $log;
	}
	

}

