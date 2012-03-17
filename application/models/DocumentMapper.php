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
                  ;
            $entries[] = $entry;
        }
        return $entries;
    }

    
    public function saveNew($document)
    {
        $data = array(
                    'extention'         => $document->extention,
                    'submit_file_name'  => $document->submitFileName,
                    'file_name'         => $document->fileName,
                    'mime_type'         => $document->mimeType,
                    'upload_date'       => new Zend_Db_Expr('NOW()'),
                    'exam_idexam'       => $document->ExamId
                );
            
            
            $insert = $this->getDbTable()->insert($data);
    }

}

