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

class Application_Form_ExamQuickSearch extends Application_Form_ExamTemplate
{

	protected $_query = NULL;
	
    public function init()
    {
    	$validator = new Zend_Validate_Regex("$\w{3}.*$");
    	$validator->setMessage("string has to start with 3 non wildcard characters", Zend_Validate_Regex::NOT_MATCH);
    	
    	$this->setMethod('post');
    	$this->setAction('/exams/quick-search');
		//$this->_query = new Zend_Form_Element_Text('_query');
    	$this->_query = new ZendX_JQuery_Form_Element_AutoComplete('_query');
		$this->_query->setLabel('Keyword Search');
		$this->_query->setRequired(true);
		$this->_query->addValidator('NotEmpty', true);
		$this->_query->addValidator( new Zend_Validate_StringLength(array('min'=>3)), true);
		$this->_query->addValidator($validator);
		
		$this->_query->setJQueryParam('data', array('Daniel', 'Willi'));
		
		//$this->_query->setDecorators($this->_decoratorDiv);
		//$this->_query->setDecorators($this->_decoratorformJQueryElements);
		$this->_query->setJQueryParam(
				'source', '/exams/quick-search-query');
		
		$this->addElement($this->_query);
		
		
		
		// Add the submit button
		$this->addElement('submit', 'submit', array(
				'ignore'   => true,
				'label'    => 'Search',
				'decorators' =>$this->_decoratorDivButton,
		));
    }

}

