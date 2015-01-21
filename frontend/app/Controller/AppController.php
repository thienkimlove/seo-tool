<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::import('Lib/Error', 'Exceptions', array('file' => 'Exceptions.php'));

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    // we have to set this to false because we are not using any datasource   
    public $uses = null;
    public $layout = "site";    
    public $components = array(
        'Session', 'Cookie', 'Api', 'RequestHandler', 'Permission'
    );
    public $helpers = array(
        'Html', 'Form', 'Session', 'Time', 'Ng'
    );

    public function beforeFilter() {
        // setup cookie
        $this->Cookie->name = Configure::read('site_name');
        $this->Cookie->key = '4beea1300a0bd99b7e8dc64b59a99aba';  
        
        $this->_setUser();      
    }
    private function _setUser() {
        $user = $this->Permission->user();
        $this->CurrentUser = $user;
        $this->set('user', $this->CurrentUser);
    }    

}
