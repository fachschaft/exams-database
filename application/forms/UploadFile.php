<?php

class Application_Form_UploadFile extends Zend_Form
{

    public function init()
    {
    
        $this->setMethod('post');
        $this->setAction('/exams/upload');
        
        // go on with http://framework.zend.com/manual/de/zend.form.standardElements.html
        
        $file = new Zend_Form_Element_File('exam_file');
        $config = Zend_Registry::get('examDBConfig');
        $file->setLabel('Uplaod Exam File:')
                ->addValidator('Count', false, $config['max_upload_files'])
                ->addValidator('Size', false, $config['max_file_size'])
                ->addValidator('Extension', false, $config['allowed_extentions'])
                ->setAttrib('enctype', 'multipart/form-data');
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
    
    public function setExamId($id)
    {
        $this->addElement('hidden', 'examId', array(
            'value' => $id,
        ));
    }


}

