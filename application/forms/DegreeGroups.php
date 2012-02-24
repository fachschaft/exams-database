<?php

class Application_Form_DegreeGroups extends Zend_Form
{
    protected $_elementSelect = null;

    public function init()
    {
        $this->setMethod('post');
        $this->setAction('/exams/degrees');
 
        $this->_elementSelect = new Zend_Form_Element_Select('group');
        
        $this->_elementSelect->setAttrib('size', '3');
        $this->_elementSelect->setRequired(true);
        
        $this->setMultiOptions();
        
        $this->addElement($this->_elementSelect);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
    public function setMultiOptions()
    {
        $groups = new Application_Model_DegreeGroupMapper();
        $entries = $groups->fetchAll();
         
        $options = array();
         
        foreach($entries as $group)
        {
            $options[$group->getId()] = $group->getName();
        }

         $this->_elementSelect->setMultiOptions($options);
    }

}

