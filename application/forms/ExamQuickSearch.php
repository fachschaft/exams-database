<?php

class Application_Form_ExamQuickSearch extends Zend_Form
{

	protected $_query = NULL;
	
    public function init()
    {
    	$validator = new Zend_Validate_Regex("$\w{3}.*$");
    	$validator->setMessage("string has to start with 3 non wildcard characters", Zend_Validate_Regex::NOT_MATCH);
    	
    	$this->setMethod('post');
    	$this->setAction('/exams/quick-search');
		$this->_query = new Zend_Form_Element_Text('_query');
		$this->_query->setLabel('Keyword Search')
		->setRequired(true)
		->addValidator('NotEmpty', true)
		->addValidator( new Zend_Validate_StringLength(array('min'=>3)), true)
		->addValidator($validator);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Search');
		
		$this->addElements ( array (
				$this->_query,
				$submit 
		) );
    }

}

