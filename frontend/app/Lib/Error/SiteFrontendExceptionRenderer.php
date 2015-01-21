<?php 
/**
 * This renderer is used to display custom Scout2GoFrontendExceptions also if debug == 0
 */
class SiteFrontendExceptionRenderer extends ExceptionRenderer {
    
    public function __construct(Exception $exception) {
           
        parent::__construct($exception);
        
        if ($exception instanceof SiteFrontendException) {
            if (isset($exception->viewVars)) {
                foreach ($exception->viewVars as $var => $val) {
                    $this->controller->set($var, $val);
                } 
            }
            $this->controller->layout = 'default';
            $this->method = '_cakeError';
        }
    }
}
?>