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
       
       $examMapper = new Application_Model_ExamMapper();

       $entry->setId($element['iddocument'])
                  ->setExtention($element['extention'])
                  ->setsubmitFileName($element['submit_file_name'])
                  ->setfileName($element['file_name'])
                  ->setmimeType($element['mime_type'])
                  ->setuploadDate($element['upload_date'])
                  ->setExamId($element['exam_idexam'])
				  ->setDeleteState($element['deleted'])
				  ->setDisplayName($element['display_name']);
		$entry->setCollection($element['collection']);
		$entry->setExam($examMapper->find($element['exam_idexam']));
                  
        return $entry;
    }
    
    public function fetchByExamId($examId)
    {
        $examTable = new Application_Model_DbTable_Exam();
        $resultSet = $examTable->find($examId)->current()->findDependentRowset('Application_Model_DbTable_Document', 'Exam');

        $entries   = array();
        foreach ($resultSet as $row) {
			if($row->deleted == 1) continue;
            $entry = new Application_Model_Document();
            $entry->setId($row->iddocument)
                  ->setExtention($row->extention)
                  ->setsubmitFileName($row->submit_file_name)
                  ->setfileName($row->file_name)
                  ->setmimeType($row->mime_type)
                  ->setuploadDate($row->upload_date)
                  ->setExamId($row->exam_idexam)
				  ->setDeleteState($row->deleted)
				  ->setReviewed($row->reviewed)
				  ->setDownloads($row->downloads)
				  ->setDisplayName($row->display_name);
			$entry->setCollection($row->collection);
            $entries[] = $entry;
        }
        return $entries;
    }
	
	public function updateDownloadCounter($documentId)
	{
		$this->getDbTable()->getAdapter()->query("UPDATE  `document` SET  `downloads` =  `downloads`+1 WHERE `iddocument` =".$documentId.";");
		
		
		// update document statistic
		$result = $this->getDbTable()->getAdapter()->query("SELECT iddocument_download_statistic_day FROM `document_download_statistic_day`
				WHERE `date` = DATE(NOW()) AND `document_iddocument` = '".$documentId."';");
		
		$count = 0;
		foreach ($result as $res) $count++;
		
		if($count > 1) {
			throw new Exception ( 'Inconsistent database, call an admin!', 500 );
		}
		if($count == 0)
		{
			try {
				$this->getDbTable()->getAdapter()->query("INSERT INTO `document_download_statistic_day` (`document_iddocument`, `date`, `downloads`)
						VALUES ('".$documentId."', NOW(), '1');");
			} catch (Exception $e) {
				//ToDo(leinfeda): Add log entry, tried to insert and failed, this my be because there was a insert while the result select above and this insert try, this is not threadsafe!
			}
			
		}
		if($count == 1)
		{
			$this->getDbTable()->getAdapter()->query("UPDATE `document_download_statistic_day` SET  `downloads` =  `downloads`+1
					WHERE `date` = DATE(NOW()) AND `document_iddocument` = '".$documentId."';");
		}
		
		// update exam statistic
		$doc = $this->fetch($documentId);
		$examMpper = new Application_Model_ExamMapper();
		$examMpper->updateDownloadCounter($doc->examId);
	}
	
	
	public function updateReviewState($documentId)
	{
		$this->getDbTable()->getAdapter()->query("UPDATE  `document` SET  `reviewed` = 1 WHERE `iddocument` =".$documentId.";");
		$this->addLogMessage($documentId, 'Document (ID: '.$documentId.') downloaded (hopely reviewed too) by %user%.');
	}
	
	public function deleteDocument($documentId)
	{
		$this->getDbTable()->getAdapter()->query("UPDATE  `document` SET  `deleted` = 1, delete_date = NOW() WHERE `iddocument` =".$documentId.";");
		$this->addLogMessage($documentId, 'Document (ID: '.$documentId.') deleted by %user%.');
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
					'md5_sum'			=> $document->CheckSum,
        			'display_name'		=> $document->displayName
                );
            
            
            $insert = $this->getDbTable()->insert($data);
            return $insert;
    }
    
    public function updateDisplayName(Application_Model_Document $document)
    {
    	$this->getDbTable()->update(array('display_name' => $document->displayName), 'iddocument ='.$document->id);
    }
    
    public function markDocumentToCollection(Application_Model_Document $document)
    {
    	$this->getDbTable()->getAdapter()->query("UPDATE  `document` SET  `collection` = 1 WHERE `iddocument` =".$document->id.";");
    }
    
    public function updateMimeType(Application_Model_Document $document)
    {
    	$this->getDbTable()->update(array('mime_type' => $document->mimeType), 'iddocument ='.$document->id);
    }
    
    public function checkDocumentExtentions()
    {
    	$data = $this->getDbTable()->fetchAll();
    	
    	foreach ($data as $doc)
    	{
    		if(!preg_match('%^[0-9a-zA-Z]{2,3}$%' ,$doc->extention))
    		{
    			echo "Exam (".$doc->iddocument.") extension dose not match the pattern: ".$doc->extention ."<br>";
    		}
    	}
    }
	
	private function addLogMessage($documentId, $message) {
		$this->getDbTable()->getAdapter()->query("INSERT INTO `exam_log` (`exam_idexam` ,`message`)
															  VALUES ((SELECT exam_idexam FROM `document` WHERE iddocument = ".$documentId." GROUP BY exam_idexam),  '".$message."')");
	}

}

