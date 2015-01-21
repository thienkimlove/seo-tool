<?php
App::uses('AppController', 'Controller');
class HomeController extends AppController {
    public function index()	{
        // the langing page should be handled by the CMS on live and stage
        // for local development we redirect to the list jobs page
       $this->autoRender = false;
       
       echo "HOME";
    }
    
}