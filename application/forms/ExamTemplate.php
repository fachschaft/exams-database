<?php

class Application_Form_ExamTemplate extends Zend_Form
{
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);
	
	public $_decoratorDiv = array(
			'ViewHelper',
			//'Label',
			array('Label', array('tag' => 'div', 'class' => 'element_lable')),
			array(array('data' => 'HtmlTag'), array('class' => 'element_form')),
	);
	
	public $_decoratorDivWithoutLabel = array(
			'ViewHelper',
			//'Label',
			//array('Label', array('tag' => 'div', 'class' => 'element_lable')),
			array(array('data' => 'HtmlTag'), array('class' => 'element_form')),
	);
	
	public $_decoratorDivButton = array(
			'ViewHelper',
			//'Label',
			//array('Label', array('tag' => 'div', 'class' => 'element_lable')),
			array(array('data' => 'HtmlTag'), array('class' => 'element_button')),
	);

    public function init()
    {
        
    }


}

