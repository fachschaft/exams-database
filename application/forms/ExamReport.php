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

