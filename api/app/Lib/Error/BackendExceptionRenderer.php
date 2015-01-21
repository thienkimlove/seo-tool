<?php
/**
 * This file is calles error500 to overwrite cakes error page but
 * it renders all exception codes
 */
class BackendExceptionRenderer extends ExceptionRenderer {

	public function __construct(Exception $exception) {
		$this->controller = $this->_getController($exception);
		$this->error = $exception;
	}

	public function render() {
	    $code = $this->error->getCode();
	    try {
	        $this->controller->response->statusCode($code);
	    } catch (CakeException $e) {
	        $code = 500;
	        $this->controller->response->statusCode($code);
	    }
	     
	    $this->controller->set(array(
	        'code' => $code,
	        'url' => $this->controller->request->here(),
	        'message' => $this->error->getMessage(),
	        'error' => get_class($this->error),
	        'exception' => $this->error,
	        '_serialize' => array('code', 'error', 'url', 'message')
	    ));

	    $this->_outputMessage('error500');
	}
}
