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

class Application_Model_Notification
{
	
	private $_mailConfig;
	private $_notiConfig;
	private $_notifactionType = "mail";
	
	public function __construct()
	{
		$this->_mailConfig = Zend_Registry::get('mail');
		$this->_notiConfig = Zend_Registry::get('notification');
		if(strtolower($this->_notiConfig['type']) == 'mail') {
			$this->_notifactionType = "mail"; 
		}
		if(strtolower($this->_notiConfig['type']) == 'none') {
			$this->_notifactionType = "none";
		}
		
			
		
	}
	
	public function sendNotification($subject, $text) {
		
		if($this->_notifactionType == 'mail') {
		
			try {
				$config = array('ssl' => $this->_mailConfig['ssl'],
						 'port' => $this->_mailConfig['port'],
						 'auth' => $this->_mailConfig['auth'],
						 'username' => $this->_mailConfig['user'],
						 'password' => $this->_mailConfig['password']);
				$transport = new Zend_Mail_Transport_Smtp($this->_mailConfig['host'], $config);
				
					
				$mail = new Zend_Mail();
				$mail->addTo($this->_notiConfig['toMail']);
				$mail->setFrom($this->_mailConfig['user']);
				$mail->setSubject($subject);
				$mail->setBodyText($text . "\n\n" . $this->_notiConfig['mailLink']);
				$mail->send($transport);
			
			} catch (Exception $e) {
				//die("Mail send Error!!!");
				$e->getTraceAsString();
				
				// handle this in a good mood?
			}
		
		}

		//else do noting!
		
		//die('mail out!?');
		
	}
	
}

