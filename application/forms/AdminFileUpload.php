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

class Application_Form_AdminFileUpload extends Zend_Form {
	
	protected $_file;
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);
	
	
	public function init() {
		$this->setMethod ( 'post' );
		
		
		$this->_file = new Zend_Form_Element_File ( 'exam_file' );
		$config = Zend_Registry::get ( 'examDBConfig' );
		$this->_file->setLabel ( 'Uplaod Exam File:' )->addValidator ( 'Count', false, array (
				'min' => 1,
				'max' => $config ['max_upload_files'] 
		) )->addValidator ( 'Size', false, $config ['max_file_size'] )->setMaxFileSize ( $config ['max_file_size'] )->addValidator ( 'Extension', false, $config ['allowed_extentions'] )->setAttrib ( 'enctype', 'multipart/form-data' );
		$this->addElement ( $this->_file, 'exam_file' );
		
		//$this->setMultiFile ( $config ['default_upload_files_count'] );
		
		//
		$this->addElement ('hidden', 'action', array ('upload' => 'upload', 'decorators' => $this->_decoratorHidden));
		
		// Add the submit button
		$this->addElement ( 'submit', 'submit', array (
				'ignore' => true,
				'label' => 'Submit' 
		) );
	}

	
	public function setMultiFile($count) {
		$config = Zend_Registry::get ( 'examDBConfig' );
		if ($count > $config ['max_upload_files']) {
			$count = $config ['max_upload_files'];
		}
		$this->_file->setMultiFile ( $count );
	}

}

