<?php

App::uses('ErrorHandler', 'Error'); 
App::uses('CakeEmail', 'Network/Email');

class BackendErrorHandler extends ErrorHandler {

    /**
     * Overwrite exception handler to send emails in production mode for non user level exceptions
     */
	public static function handleException(Exception $exception) {
		if (Configure::read('debug') == 0 &&  !($exception instanceof SiteBackendException)) {
		    static::reportError(self::_getMessage($exception));
		    $exception = new BadRequestException();
		}		
		parent::handleException($exception);
        if($exception instanceof SiteBackendException) {
            CakeLog::write(LOG_WARNING, self::_getMessage($exception));
        }
	}

	/**
	 * Overwrite error handler to throw a fatal error exception on every error
	 */
	public static function handleError($code, $description, $file = null, $line = null, $context = null) {
	    if (error_reporting() === 0) {
			return false;
		}
		return static::handleFatalError($code, $description, $file, $line);
	} 
	
	public static function reportError($message) {
	    $message = 'Cyker app running on ' . env('HTTP_HOST') . ' in folder ' . 
	        ROOT . " recived an error.\n\nLogged in user: " . env('PHP_AUTH_USER') . "\n\n" . $message;
	    try {
    	    $email = new CakeEmail('site');
    	    $email->from(array('info@5stars.vn' => 'Sitename'))
        	    ->emailFormat('text')
        	    ->to(Configure::read('ErrorEmail'))
        	    ->subject('Site error');
    	    $result = $email->send($message);
	    } catch (Exception $e) {
			CakeLog::write(LOG_ERR, "Error while sending email error notification: ".$e->getMessage());
	    }
	}
}
