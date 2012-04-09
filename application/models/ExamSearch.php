<?php

class Application_Model_ExamSearch {
	
	protected $indexpath = '/var/www/exams/exams-database/data/my-index';
	
	public function createIndex() {
		if (file_exists ( $this->indexpath )) {
			throw new Exception ( 'Index allready exists. Please use "renewIndex" to rebuild it' );
		}
		$index = Zend_Search_Lucene::create ( $this->indexpath );
	}
	
	public function deleteIndex($path = NULL) {
		if ($path == NULL)
			$path = $this->indexpath;
		foreach ( glob ( $path . '/*' ) as $file ) {
			if (is_dir ( $file ))
				$this->deleteIndex ( $file );
			else
				unlink ( $file );
		}
		rmdir ( $path );
	}
	
	public function getIndexSize() {
		$index = Zend_Search_Lucene::open ( $this->indexpath );
		$documents = $index->numDocs ();
		return $documents;
	}
	
	public function optimizeIndex() {
		$index = Zend_Search_Lucene::open ( $this->indexpath );
		$index->optimize ();
	}
	
	public function renewIndex() {
		if (file_exists ( $this->indexpath ))
			$this->deleteIndex ();
		$index = Zend_Search_Lucene::create ( $this->indexpath );
		
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
	
	public function addFileToIndex($filename, array $keywords) {
		$index = Zend_Search_Lucene::open ( $this->indexpath );
		$doc = new Zend_Search_Lucene_Document ();
		$doc->addField ( Zend_Search_Lucene_Field::Text ( 'filename', $filename ) );
		foreach ( $keywords as $keyword ) {
			$doc->addField ( Zend_Search_Lucene_Field::Keyword ( 'keyword', $keyword ) );
		}
	}
	
	public function removeFileFromIndex($filename) {
		$index = Zend_Search_Lucene::open ( $this->indexpath );
		$hits = $index->find ( 'filename:' . $filename );
		foreach ( $hits as $hit ) {
			$index->delete ( $hit->id );
		}
	}
	
	public function searchIndex($query) {
		$index = Zend_Search_Lucene::open ( $this->indexpath );
		$hits = $index->find ( $query );
		foreach ( $hits as $hit ) {
			// TODO(aamuuninen) do something sensible with the results
			echo $hit->filename;
		}
	}
}