<?php

class Application_Model_ExamSearch {
	
	protected $filepath = "/data/searchindex";
	
	public function createIndex() {
		if (file_exists ( $this->filepath )) {
			throw new Exception ( 'Index allready exists. Please use "renewIndex" to rebuild it' );
		}
		$index = Zend_Search_Lucene::create ( $this->filepath );
	}
	
	public function getIndexSize() {
		$index = Zend_Search_Lucene::open ( $this->filepath );
		$documents = $index->numDocs ();
		return $documents;
	}
	
	public function optimizeIndex() {
		$index = Zend_Search_Lucene::open ( $this->filepath );
		$index->optimize ();
	}
	
	public function renewIndex() {
		if (file_exists ( $this->filepath )) {
			unlink ( $this->filepath );
			$index = Zend_Search_Lucene::create ( $this->filepath );
			/*
			 * TODO(aamuuninen) fill the index with all the exams in the
			 * database $keywords_array = ...; for ($n = 0; $n <
			 * number_of_exams; $n++){ $doc = new Zend_Search_Lucene_Document();
			 * $doc->addField(Zend_Search_Lucene_Field::Text('filename',
			 * $row['filename']));
			 * $doc->addField(Zend_Search_Lucene_Field::Keyword('keyword',
			 * $keywords_array));
			 */
		}
	}
	
	public function addFileToIndex($filename, array $keywords) {
		$index = Zend_Search_Lucene::open ( $this->filepath );
		$doc = new Zend_Search_Lucene_Document ();
		$doc->addField ( Zend_Search_Lucene_Field::Text ( 'filename', $filename ) );
		foreach ( $keywords as $keyword ) {
			$doc->addField ( Zend_Search_Lucene_Field::Keyword ( 'keyword', $keyword ) );
		}
	}
	
	public function removeFileFromIndex($filename) {
		$index = Zend_Search_Lucene::open ( $this->filepath );
		$hits = $index->find ( 'filename:' . $filename );
		foreach ( $hits as $hit ) {
			$index->delete ( $hit->id );
		}
	}
	
	public function searchIndex($query) {
		$index = Zend_Search_Lucene::open ( $this->filepath );
		$index->find ( $query );
	}
}