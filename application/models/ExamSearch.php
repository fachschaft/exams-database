<?php

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
		if (file_exists ( $this->_indexpath ))
			$this->deleteIndex ();
		$index = Zend_Search_Lucene::create ( $this->_indexpath );
		
		/*
		 * TODO(aamuuninen) fill the index with all the exams in the database
		 * $keywords_array = ...; for ($n = 0; $n < number_of_exams; $n++){ $doc
		 * = new Zend_Search_Lucene_Document();
		 * $doc->addField(Zend_Search_Lucene_Field::Text('filename',
		 * $row['filename']));
		 * $doc->addField(Zend_Search_Lucene_Field::Keyword('keyword',
		 * $keywords_array));
		 */
	}
	
	public function addFileToIndex($id, $keywords) {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$doc = new Zend_Search_Lucene_Document ();
		$doc->addField ( Zend_Search_Lucene_Field::Keyword( 'examid', $id ) );
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
	
	public function searchIndex($query) {
		$index = Zend_Search_Lucene::open ( $this->_indexpath );
		$hits = $index->find ( $query );
		foreach ( $hits as $hit ) {
			// TODO(aamuuninen) do something sensible with the results
			echo "Id: $hit->examid<br>";
			echo "Keywords for this record: $hit->keyword<br>";
		}
	}
}