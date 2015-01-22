<?php  

App::uses('AppController', 'Controller');

class ContentsController extends AppController {
    public function index() {
        $options = $this->Content->buildOptions($this->params->query);               
        $options['order'] = 'Content.created DESC';

        $this->set(array(
            'contents' => $this->Content->find('all', $options), 
            '_serialize' => array('contents')
        ));
    }  
    public function add() {
        $data = $this->request->data;
        $data['status'] = 'active';
        $response = $this->Content->save($data);
        $this->set(array(
            'content' => $response, 
            '_serialize' => array('content')
        )); 
    }
}