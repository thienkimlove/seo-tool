<?php
App::uses('HttpSocket', 'Network/Http');

class Resource {

    protected $_resource;

    protected $_socket;
    
    private $_controller;        
    
    public function __construct($controller, $name, $user = null, $password = null) {
        $this->_resource = $name;
        $this->_socket = new HttpSocket(array('ssl_verify_peer' => false, 'timeout' => 100));
        if(isset($user) && isset($password)) {
            $this->_socket->configAuth('Basic', $user, $password);
        }
        $this->_controller = $controller;      
    }
   
    public function request($uri, $options = array()) {
        if(!isset($options['method'])) {
            $options['method'] = 'GET';
        }
        if(!isset($options['data'])) {
            $options['data'] = array();
        }
        $request = array(
            'method' => $options['method'],             
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $this->_resource . $uri
            ),
        );
        if($options['method'] == 'POST') {
            $request['body'] = $options['data'];
        } else {
            $request['uri']['query'] = $options['data'];
        }
        
               
        $response = $this->_socket->request($request);
        return ApiComponent::decodeResponse($this->_controller, $response->body);
    }

    public function query(array $params = array()) {
        $request = array(
            'method' => 'GET',            
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $this->_resource,
                'query' => $params
            )
        );
        
        $response = $this->_socket->request($request);
        return ApiComponent::decodeResponse($this->_controller, $response->body);
    }

    public function get($id) {
        $request = array(
            'method' => 'GET',             
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $this->_resource . '/' . $id
            )
        );
       
        
        $response = $this->_socket->request($request);
        return ApiComponent::decodeResponse($this->_controller, $response->body);
    }

    public function delete($id) {
        $request = array(
            'method' => 'DELETE',             
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $this->_resource . '/' . $id
            )
        );
       
        $response = $this->_socket->request($request);
        return ApiComponent::decodeResponse($this->_controller, $response->body);
    }

    public function add(array $data = array()) {
        $request = array(
            'method' => 'POST',           
            'body' => $data,
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $this->_resource
            )
        );   
        
        
        $response = $this->_socket->request($request);   
        return ApiComponent::decodeResponse($this->_controller, $response->body);
    }

    public function edit($id, array $data = array()) {
        $request = array(
            'method' => 'POST',             
            'body' => $data,
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $this->_resource . '/' . $id
            )
        );
       
        
        $response = $this->_socket->request($request);
        return ApiComponent::decodeResponse($this->_controller, $response->body);
    }
}

class ApiComponent extends Component {         
    
    public $components = array('Session');
    private $_controller;
    
    public function startup(Controller $controller) {
        $this->_controller = $controller; 
    }
    
    public function resource($name) {        
        // normal user
            $resource = new Resource(
                $this->_controller,
                Inflector::underscore(Inflector::pluralize($name)),
                $this->Session->read('Auth.User.Email'),
                $this->Session->read('Auth.User.Password')
            );
        return $resource;
    }
    
    public function request($uri, $options = array()) {        
        $socket = new HttpSocket(array('ssl_verify_peer' => false));
        if(!isset($options['method'])) {
            $options['method'] = 'POST';
        }
        if(!isset($options['data'])) {
            $options['data'] = array();
        }
        $request = array(
            'method' => $options['method'],             
            'uri' => array(
                'scheme' => Configure::read('Api.scheme'),
                'host' => Configure::read('Api.host'),
                'path' => Configure::read('Api.path') . $uri
            )
        );
        if($options['method'] == 'POST') {
            $request['body'] = $options['data'];
        } else {
            $request['uri']['query'] = $options['data'];
        }
        
         
        $response = $socket->request($request);
        return self::decodeResponse($this->_controller, $response->body);
    }
    
    public static function decodeResponse($controller, $response) {
        if($response == null || trim($response) == '') {
            return null;
        }
        $result = json_decode($response, true);
        if($result == null) {
            throw new FatalErrorException('Could not parse api server response: ' . $response);
        }
        if(isset($result['error'])) {
            if ($result['error'] === "IncorrectCredentialsException") {
                $controller->Session->destroy();                
                $controller->Session->setFlash(__('Your session has ended. Please log in again.', true), 'default', array(), 'error');                                
                $controller->redirect(array(        	        
        	        'controller' => 'users', 
        	        'action' => 'login'
        	    )); 
                
            }
            else { throw new ApiServerException($result); }
        }
        return $result;
    }
}
