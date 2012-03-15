<?php

class Application_Form_UploadFile extends Zend_Form
{

    public function init()
    {
    
        $this->setMethod('post');
        $this->setAction('/exams/upload');
        
        // go on with http://framework.zend.com/manual/de/zend.form.standardElements.html
        
        $file = new Zend_Form_Element_File('exam_file');
        $file->setLabel('Uplaod Exam File:')
                //->setDestination('/var/www/vhosts/db/data/exams')
                ->addValidator('Count', false, 1)
                ->addValidator('Size', false, 102400) // 100kb
                ->addValidator('Extension', false, 'jpg,png,gif,txt');
        $this->addElement($file, 'exam_file');
        
        //
        $this->addElement('hidden', 'step', array(
            'value' => '3',
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        ));
    }
    
    


}

