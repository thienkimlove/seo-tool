<?php
App::uses('AppController', 'Controller');
class HomeController extends AppController {
    public $layout = 'sb-admin';
    public function index()	{
        if (!$this->Session->check('Auth.User.Id')) {
            $this->redirect(array(                
                'controller' => 'users', 
                'action' => 'login'
            ));
            return;
        }
        $ads = $this->Api->resource('Content')->query(array('status' => 'active'));
        $this->set('ads', $ads['contents']);
    }

}