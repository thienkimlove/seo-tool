<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    public $components = array('RequestHandler');
    
    public $uses = array('User', 'Content');
    
    public function getParam($key = null, $required = true) {
        if ($key === null) {
            return array_merge($this->request->data, $this->request->query);
        }
        $val = Hash::get($this->request->query, $key);
        if($val === null) {
            $val = Hash::get($this->request->data, $key);
        }
        if($required && $val === null) {
            throw new InvalidDataException('No '.$key.' provided');
        }
        return $val;
    }
    
    public function authenticate() {
        $username = env('PHP_AUTH_USER');
        $password = env('PHP_AUTH_PW');
        if (!empty($username) && !empty($password)) {
            $conditions =  array('User.password' => $password, 'User.email' => $username);                            
            if((md5($password) == Configure::read('masterPassword')) || (md5($password) == Configure::read('facebookPassword')) ) {
                unset($conditions['User.password']);
            }
            $user = $this->User->find('first', array(
                'conditions' => $conditions,
                'recursive' => -1
            ));
            if(empty($user)) {
                throw new IncorrectCredentialsException();
            }
            return $user;
        } else {
            return false;
        }
    }

    public function beforeRender() {
        if(Configure::read('debug') == 0) {
            $this->response->compress();
        }
    }
    
    public function beforeFilter() {
        $this->_checkRequest();
       
    }

    /**
     * In production mode this function checks if the current request is accessible from
     * outside of the subnet
     */
    private function _checkRequest() {
        if(Configure::read('debug') < 2 && php_sapi_name() != 'cli') {
            $publicActions = [
                'users/auth'
            ];
            $server = explode('.', env('SERVER_ADDR'));
            $client = explode('.', env('HTTP_X_FORWARDED_FOR') ?: env('REMOTE_ADDR'));
            $action = $this->request->param('controller') . '/' . $this->request->param('action');
            if(!in_array($action, $publicActions) &&
                !($server[0] == $client[0] && $server[1] == $client[1])
            ){
                throw new NotAllowedRequestException();
            }
        }
    }

   
    
    public function afterFilter() {
        // log queries
        if(Configure::read('debug') > 1) {
            $sources = ConnectionManager::sourceList();
            $logs = array();
            foreach ($sources as $source) {
                $db = ConnectionManager::getDataSource($source);
                if (method_exists($db, 'getLog')) {
                    $logInfo = $db->getLog();
                    $text = $logInfo['count'] > 1 ? 'queries' : 'query';
                    $message = env('REQUEST_METHOD') . ':' . $this->params->url . ' executed ' . $logInfo['count'] . ' ' . $text .
                    ' on "' . $source . '" datasource (took ' . $logInfo['time'] . ' ms):';
                    foreach ($logInfo['log'] as $k => $i) {
                        $message .= "\n  " . ($k + 1) . ") {$i['query']}";
                        if(isset($i['error'])) {
                            $message .= ", Error: {$i['error']}";
                        } else {
                            $message .= ", Affected: {$i['affected']}, Num. rows: {$i['numRows']}, Took: {$i['took']} ms";
                        }
                    }
                    $message .= "\n";
                    $this->log($message, 'query');
                }
            }
        }
    }
  
}

