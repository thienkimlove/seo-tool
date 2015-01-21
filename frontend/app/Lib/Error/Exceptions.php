<?php 

/**
 * Excetpion base class used to identify exceptions that can be 
 * shown to the user in production mode
 */
class SiteFrontendException extends CakeException {
    
    public function __construct($args, $code = 500) {
        parent::__construct($args, $code);
        if (isset($args["viewVars"])) {
            $this->viewVars = $args["viewVars"];
        }
    }
}

class ApiServerException extends SiteFrontendException {
    
    public $code = 500;
    public $url = '';
    public $message = '';
    public $error = 'Exception';
    
    public function __construct($response) {
        if(isset($response['code'])) {
            $this->code = $response['code'];
        }
        if(isset($response['error'])) {
            $this->error = $response['error'];
        }
        if(isset($response['url'])) {
            $this->url = $response['url'];
        }
        if(isset($response['message'])) {
            $this->message = $response['message'];
        }
        parent::__construct($this->message, $this->code);
    }   
}



?>