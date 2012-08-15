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

class Application_Model_ExamSearch {
	
	protected $_indexpath = NULL;
	
	public function __construct() {
		// Set the index directory to the required relative path
		$path = getcwd () . '/../data/my-index';
		$this->setIndexpath ( $path );
	}
	
	private function getIndexpath() {
		return $this->_indexpath;
	}
	
	private function setIndexpath($indexpath) {
		$this->_indexpath = $indexpath;
	}
	
	// Create an empty Index, raise error if index exists
	public function createIndex() {
		if (file_exists ( $this->_indexpath )) {
			throw new Exception ( 'Index allready exists. Please use "renewIndex" to rebuild it' );
		}
		$index = Zend_Search_Lucene::create ( $this->_indexpath );
	}
	
	// Recursively remove the index files from the filesystem
	public function deleteIndex($path = NULL) {
		if ($path == NULL)
			$path = $this->_indexpath;
		foreach ( glob ( $path . '/*' ) as $file ) {
			if (is_dir ( $file ))
				$this->deleteIndex ( $file );
			else
				unlink ( $file );
		}
		rmdir ( $path );
	}
	
	public function getIndexSize() {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$documents = $index->numDocs ();
		return $documents;
	}
	
	public function optimizeIndex() {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$index->optimize ();
	}
	
	// Rebuild the index from the database in case of data corruption
	public function renewIndex() {
		//set_time_limit(0);
		if (file_exists ( $this->_indexpath ))
			$this->deleteIndex ();
		$index = Zend_Search_Lucene::create ( $this->_indexpath );
		$examMapper = new Application_Model_ExamMapper();
		$exams = $examMapper->fetchPublic();
		foreach ($exams as $exam)
		{
			$this->addFileToIndex($exam->getId());
			// reset the timeout timer each time the loop is executed 	
			set_time_limit(10);
		}
	}
	
	public function addFileToIndex($id) {
		$examMapper = new Application_Model_ExamMapper();
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$doc = new Zend_Search_Lucene_Document ();
		$doc->addField ( Zend_Search_Lucene_Field::Keyword( 'examid', $id ) );
		$keywords = $examMapper->returnQuicksearchIndexKeywords($id);
		$doc->addField ( Zend_Search_Lucene_Field::Text ( 'keyword', $keywords ) );
		$index->addDocument ( $doc );
	}
	
	public function removeFileFromIndex($id) {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$hits = $index->find ( 'examid:' . $id );
		foreach ( $hits as $hit ) {
			$index->delete ( $hit->id );
		}
	}
	
	//ToDo(leinfeda): Think about the charset: http://framework.zend.com/manual/en/zend.search.lucene.charset.html
	public function searchIndex($query) {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$hits = $index->find ( $query );
		$foundIds = array();
		foreach ( $hits as $hit ) {
			// Debug prints
/* 			echo "Id: $hit->examid<br>";
			echo "Keywords for this record: $hit->keyword<br>"; */
			$foundIds[] = $hit->examid;
		}
		return $foundIds;
	}
	
	public function searchExists($query) {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		Zend_Search_Lucene::setResultSetLimit(1);
		$hits = $index->find ( $query );
		if(count($hits) > 0) return true;
		return false;
	}
	
	
	public function searchExams($query, Application_Model_ExamStatus $status = null) {
		//Note: Default value for parameters with a class type hint can only be NULL
		if($status == null) $status = array(Application_Model_ExamStatus::PublicExam);
		$examIds = $this->searchIndex($query);
		$exams = array();
		if(!is_array($examIds)) {
			$examIds = array($examIds);
		}
		
		if(!empty($examIds)) {
			$examsMapper = new Application_Model_ExamMapper();
			$exams = $examsMapper->fetchQuick(-1, -1, -1, -1, -1, $status, true, $examIds);
		} else {
			return array();
		}
		
		return $exams;
	}
}