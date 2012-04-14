<?php

class Application_Form_ExamReport extends Zend_Form {
	
	protected $_reason = NULL;
	
	public function init() {

		$this->setMethod('post');
		$this->_reason = new Zend_Form_Element_Text('_reason');
		$this->_reason->setLabel('Explanation:')
		->setRequired(true)
		->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');
		
		$this->addElements ( array (
				$this->_reason,
				$submit
		) );
	}

}

