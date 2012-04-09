<?php

class Application_Form_ExamQuickSearch extends Zend_Form
{

	protected $_query = NULL;
	
    public function init()
    {
    	$this->setMethod('post');
    	$this->setAction('/exams/quick-search');
		$this->_query = new Zend_Form_Element_Text('_query');
		$this->_query->setLabel('Keyword Search')
		->setRequired(true)
		->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Search');
		
		$this->addElements ( array (
				$this->_query,
				$submit 
		) );
    }

}

