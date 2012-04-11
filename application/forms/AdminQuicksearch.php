<?php

class Application_Form_AdminQuicksearch extends Zend_Form {
	
	protected $_buttonNewIndex = NULL;
	protected $_buttonRebuildIndex = NULL;
	protected $_buttonDeleteIndex = NULL;
	protected $_buttonOptimizeIndex = NULL;
	
	public function init() {
		
		$this->setMethod ( 'post' );
		$this->setAction ( '/exams-admin/build-quicksearch-index' );
		
		$this->_buttonNewIndex = new Zend_Form_Element_Submit ( 'newIndex' );
		$this->_buttonNewIndex->setAttrib('id', 'newindex');
		
		$this->_buttonRebuildIndex = new Zend_Form_Element_Submit ( 'rebuildIndex' );
		$this->_buttonRebuildIndex->setAttrib('id', 'rebuildindex');
		
		$this->_buttonDeleteIndex = new Zend_Form_Element_Submit ( 'deleteIndex' );
		$this->_buttonDeleteIndex->setAttrib('id', 'deleteindex');
		
		$this->_buttonOptimizeIndex = new Zend_Form_Element_Submit ( 'optimizeIndex' );
		$this->_buttonOptimizeIndex->setAttrib('id', 'optimizeindex');
		

		$this->addElements ( array (
				$this->_buttonNewIndex, 
				$this->_buttonRebuildIndex,
				$this->_buttonDeleteIndex,
				$this->_buttonOptimizeIndex
		) );
	}

}