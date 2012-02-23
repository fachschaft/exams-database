<?php

class Application_Form_ExamDegrees extends Zend_Form
{
    protected $_elementSelect = null;

    public function init()
    {
       // Set the method for the display form to POST
        $this->setMethod('post');
 
        $this->_elementSelect = new Zend_Form_Element_Select('degree');
        
        $this->_elementSelect->setAttrib('size', '4');
        $this->_elementSelect->addValidator('NotEmpty', false);
        
        $this->addElement($this->_elementSelect);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
    public function setMultiOptions(array $options)
    {
        $this->_elementSelect->setMultiOptions($options);
    }

}

