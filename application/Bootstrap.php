<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
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
        $view->setEncoding('UTF-8');
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
        
        $ldapConfig = $this->getOption('ldap');
        Zend_Registry::set('ldap', $ldapConfig);
        
        $mailConfig = $this->getOption('mail');
        Zend_Registry::set('mail', $mailConfig);
        
        $notConfig = $this->getOption('notification');
        Zend_Registry::set('notification', $notConfig);
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

	public function _initJQuery() {
		$this->view->addHelperPath(
				'ZendX/JQuery/View/Helper'
				,'ZendX_JQuery_View_Helper');
	}
	
	protected function _initLucene()
	{
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(
		new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ());
	}
}

