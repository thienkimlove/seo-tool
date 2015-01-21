<?php 

/**
 * Base class for all scout2go user level exceptions
 */
class SiteBackendException extends CakeException {

    public function __construct($args, $code = 500) {
        parent::__construct($args, $code);
    }
};

class NotAllowedRequestException extends SiteBackendException {

    public function __construct($msg = 'You are not allowed to execute this request') {
        parent::__construct($msg, 401);
    }
}

class MissingDataException extends SiteBackendException {

    public function __construct($msg) {
        parent::__construct($msg);
    }
}

class InvalidDataException extends SiteBackendException {

    public function __construct($validationErrors = null) {
        $message = 'Validation error';
        if(is_string($validationErrors)) {
            $message = $validationErrors;
        } else if(is_array($validationErrors)) {
            while (is_array($validationErrors)) {
                $validationErrors = array_pop($validationErrors);
            }
            $message = $validationErrors;
        }
        parent::__construct($message, 400);
    }
}

class IncorrectCredentialsException extends SiteBackendException {

    public function __construct($message = 'Incorrect credentials supplied') {
        parent::__construct($message, 401);
    }
}

class DuplicateEmailException extends SiteBackendException {  
    public function __construct($msg = 'This email already exists') {
        parent::__construct($msg);
    }
}


?>