<?php

App::uses('CakeTime', 'Utility');

class PermissionComponent extends Component {

    public $components = array('Session', 'Api');
    
    public $controller = null;
    
    public $base = 'Permission';
    
    function startup(&$controller){
        $this->controller = $controller;
    }
    
    private function _refresh() {
        if($this->Session->check('Auth')) {
            $timestamp = $this->Session->read($this->base . '._timestamp');
            if(!$timestamp || !CakeTime::isToday($timestamp, 'UTC')) {
                $user = $this->Api->resource('User')->get($this->Session->read('Auth.User.Id'));
                $this->Session->write($this->base, array(
                    'User' => $user['user']['User'],
                    '_timestamp' => time()
                ));                               
            }
        } else {
            $this->reset();
        }
    }
    
    public function resetUser(){
         $this->Session->write($this->base . '._timestamp', null);
    }
    
    
    public function reset() {
        $this->Session->delete($this->base);
    }
    
    public function user() {
        $this->_refresh();
        return $this->Session->read($this->base . '.User');
    }
}