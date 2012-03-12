<?php

class Application_Form_UploadDegrees extends Zend_Form
{

    protected $_elementSelect = null;

    public function init()
    {
        $this->setMethod('post');
         $this->setAction('/exams/upload');
 
        //
        $this->_elementSelect = new Zend_Form_Element_Select('degree');
        $this->_elementSelect->setAttrib('size', '4')
                             ->setRequired(true);
        $this->addElement($this->_elementSelect);
       
        $this->addElement('hidden', 'step', array(
            'value' => '1',
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
        
        
        $this->setMultiOptions();
    }
    
    public function setMultiOptions()
    {
        $degrees = new Application_Model_DegreeMapper();
        $entries = $degrees->fetchAll();  
        $options = array(); 
        
        foreach($entries as $gr)
        {
            $options[$gr->getId()] = $gr->getName();
        }

        $this->_elementSelect->setMultiOptions($options);
        $this->_elementSelect->setAttrib('size', count($options));
    }

}

