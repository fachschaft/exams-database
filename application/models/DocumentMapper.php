<?php

class Application_Model_DocumentMapper
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
            $this->setDbTable('Application_Model_DbTable_Document');
        }
        return $this->_dbTable;
    }
    
    public function fetch($documentId)
    {
       $element = $this->getDbTable()->find($documentId)->current();
       $entry = new Application_Model_Document();

       $entry->setId($element['iddocument'])
                  ->setExtention($element['extention'])
                  ->setsubmitFileName($element['submit_file_name'])
                  ->setfileName($element['file_name'])
                  ->setmimeType($element['mime_type'])
                  ->setuploadDate($element['upload_date'])
                  ->setExamId($element['exam_idexam'])
				  ->setDeleteState($element['deleted'])
                  ;
                  
        return $entry;
    }
    
    public function fetchByExamId($examId)
    {
        $examTable = new Application_Model_DbTable_Exam();
        $resultSet = $examTable->find($examId)->current()->findDependentRowset('Application_Model_DbTable_Document', 'Exam');

        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Document();
            $entry->setId($row->iddocument)
                  ->setExtention($row->extention)
                  ->setsubmitFileName($row->submit_file_name)
                  ->setfileName($row->file_name)
                  ->setmimeType($row->mime_type)
                  ->setuploadDate($row->upload_date)
                  ->setExamId($row->exam_idexam)
				  ->setDeleteState($row->deleted)
                  ;
            $entries[] = $entry;
        }
        return $entries;
    }
	
	public function updateDownloadCounter($documentId)
	{
		$this->getDbTable()->getAdapter()->query("UPDATE  `document` SET  `downloads` =  `downloads`+1 WHERE `iddocument` =".$documentId.";");
	}
	
	
	public function updateReviewState($documentId)
	{
		$this->getDbTable()->getAdapter()->query("UPDATE  `document` SET  `reviewed` = 1 WHERE `iddocument` =".$documentId.";");
		$this->addLogMessage($documentId, 'Document (ID: '.$documentId.') downloaded (hopely reviewed too) by %user%.');
	}

    
    public function saveNew($document)
    {
        $data = array(
                    'extention'         => $document->extention,
                    'submit_file_name'  => $document->submitFileName,
                    'file_name'         => $document->fileName,
                    'mime_type'         => $document->mimeType,
                    'upload_date'       => new Zend_Db_Expr('NOW()'),
                    'exam_idexam'       => $document->ExamId,
					'md5_sum'			=> $document->CheckSum
                );
            
            
            $insert = $this->getDbTable()->insert($data);
    }
	
	private function addLogMessage($documentId, $message) {
		$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_log` (`exam_idexam` ,`message`)
															  VALUES ((SELECT exam_idexam FROM `document` WHERE iddocument = ".$documentId." GROUP BY exam_idexam),  '".$message."')");
	}

}

