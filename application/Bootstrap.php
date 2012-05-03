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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    protected function _initTimezone()
    {
    	date_default_timezone_set('UTC');
    }
    
    public function _initExams()
    {
        $examsConfig = $this->getOption('exams');
        Zend_Registry::set('examDBConfig', $examsConfig);
		
		$authenticateConfig = $this->getOption('authenticate');
        Zend_Registry::set('authenticate', $authenticateConfig);
    }
    
    public function _initPlaintextHelper()
    {
        // Initialise Zend_Layout's MVC helpers
        Zend_Layout::startMvc();
        Zend_Layout::getMvcInstance()->getView()->addHelperPath('Custom/View/Helper/', 'Custom_View_Helper');
    }
    
 	 public function _initLog() {
		if ($this->hasPluginResource ( "log" )) {
			$r = $this->getPluginResource ( "log" );
			$log = $r->getLog ();
			Zend_Registry::set ( "log", $log );
			return $log;
		}	
	}  
}

