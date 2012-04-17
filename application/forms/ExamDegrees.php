<?php

class Application_Form_ExamDegrees extends Zend_Form
{
    protected $_elementSelect = null;
    
    public $_decoratorHidden = array(
    		 'ViewHelper',
    		array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
    );

    public function init()
    {
        $this->setMethod('post');
         $this->setAction('/exams/degrees');
 
        //
        $this->_elementSelect = new Zend_Form_Element_Select('degree');
        $this->_elementSelect->setAttrib('size', '4')
                             ->setRequired(true);
        $this->addElement($this->_elementSelect);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
    public function setMultiOptions($group)
    {
        $degrees = new Application_Model_DegreeMapper();
        $entries = $degrees->fetchByGroup($group);  
        $options = array(); 
        
        foreach($entries as $gr)
        {
            $options[$gr->getId()] = $gr->getName();
        }

        $this->_elementSelect->setMultiOptions($options);
    }
    
    public function setGroup($id)
    {
        $this->addElement('hidden', 'group', array(
            'value' => $id,
        	'decorators' => $this->_decoratorHidden,
        ));
    }

}

