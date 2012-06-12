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

class Application_Form_ExamTemplate extends Zend_Form
{
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);
	
	public $_decoratorDiv = array(
			'ViewHelper',
			//'Errors',
			array('Errors', array('class' => 'element_error')),
			//'Label',
			array('Label', array('tag' => 'div', 'class' => 'element_lable')),
			array(array('data' => 'HtmlTag'), array('class' => 'element_form')),
	);
	
	public $_decoratorDivWithoutLabel = array(
			'ViewHelper',
			//'Errors',
			array('Errors', array('class' => 'element_error')),
			//'Label',
			//array('Label', array('tag' => 'div', 'class' => 'element_lable')),
			array(array('data' => 'HtmlTag'), array('class' => 'element_form')),
	);
	
	public $_decoratorDivButton = array(
			'ViewHelper',
			array('Errors', array('class' => 'element_error')),
			//'Label',
			//array('Label', array('tag' => 'div', 'class' => 'element_lable')),
			array(array('data' => 'HtmlTag'), array('class' => 'element_button')),
	);

    public function init()
    {
        
    }


}

