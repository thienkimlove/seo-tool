<?php
App::uses ( 'HttpSocket', 'Network/Http' );

class ProxyController extends AppController {

    private function _resetCacheOnJsRequest() {
        if (strpos($this->params['pass'][0] ,'users') !== FALSE && in_array( strtolower(env('REQUEST_METHOD')), array('post', 'put'))) {            
            return true;
        }         
        return false;
    }
    
    public function index() {
        $HttpSocket = new HttpSocket ();
        
        if (sizeof ( $this->params ['pass'] [0] ) != 1) {
            throw new InvalidArgumentException ();
        }
        if (! empty ( $_SERVER ['PHP_AUTH_USER'] ) && ! empty ( $_SERVER ['PHP_AUTH_PW'] )) {
            $HttpSocket->configAuth ( 'Basic', $_SERVER ['PHP_AUTH_USER'], $_SERVER ['PHP_AUTH_PW'] );
        } elseif (isset ( $this->CurrentUser )) {
            $HttpSocket->configAuth ( 'Basic', $this->Session->read ( 'Auth.User.Email' ), $this->Session->read ( 'Auth.User.Password' ) );
        }
        
        $this->response->type ( 'json' );
        
        $request = [
            'method' => env ( 'REQUEST_METHOD' ),
            'body' => $this->request->data,
            'header' => [
                'Accept-Language' => Configure::read('Config.language')
            ],
            'uri' => [
                'scheme' => Configure::read ( 'Api.scheme' ),
                'host' => Configure::read ( 'Api.host' ),
                'port' => 80,
                'path' => Configure::read ( 'Api.path' ) . $this->params ['pass'] [0],
                'query' => $this->params->query 
            ] 
        ];
       
       $response = $HttpSocket->request($request);
        if ($error = $HttpSocket->lastError()) {
            throw new SocketException($error);
        }

        if ($this->_resetCacheOnJsRequest()) {             
            $this->Permission->resetUser();
        }
        try {
            $this->response->statusCode($response->code);
        } catch (CakeException $e) {
            $this->response->statusCode (500);
        }
        $this->response->body ( $response->body );
        
        return $this->response;
    }
}

?>