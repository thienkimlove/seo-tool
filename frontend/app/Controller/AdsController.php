<?php
App::uses('AppController', 'Controller');
class AdsController extends AppController {
    public $layout = 'sb-admin';
    public function index()    {
        if (!$this->Session->check('Auth.User.Id')) {
            $this->redirect(array(                
                'controller' => 'users', 
                'action' => 'login'
            ));
            return;
        }
        if (!empty($this->request->data)) {
          //$this->log($this->request->data);
          $result = $this->Api->resource('Content')->add($this->request->data);
          if (!empty($result['content'])) {
              $this->Session->setFlash(__('Created new add successfully!', true), 'default', array(), 'success');
              $this->redirect('/');
          } else {
             $this->Session->setFlash(__('Can not create new add. Please try again later!', true), 'default', array(), 'error'); 
          }
        }
    }

}