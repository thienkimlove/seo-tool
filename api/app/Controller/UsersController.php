<?php  

App::uses('AppController', 'Controller');

class UsersController extends AppController {

    var $uses = array('User');

    public function index() {
        $user = $this->authenticate();
        if (!$user) {
            throw new NotAllowedRequestException();
        }
        $options = $this->User->buildOptions($this->params->query);

        if (isset($this->params->query['conditions'])) {
            $options['conditions'] = $this->params->query['conditions'];
        }
        if ($user['User']['group'] != 'admin') {
            $options['conditions']['User.id'] = $user['User']['id'];
        }                        

        $options['recursive'] = -1;

        $users = $this->User->find('all', $options);
        $this->set(array(
            'users' => $users, 
            '_serialize' => array('users')
        ));
    }

    public function add() {
        if (!empty($this->request->data)) {
            // unset a few things for securty
            $propsToUnset = array('id', 'token', 'status', 'salt', 'group');

            foreach ($propsToUnset as $field) {
                unset($this->request->data[$field]);   
                unset($this->request->data['User'][$field]);   
            }               

            // check email
            $this->User->recursive = -1;
            $checkEmail = $this->User->findByEmail($this->request->data['email']);
            if(!empty($checkEmail)) {
                throw new DuplicateEmailException();
            }
            $this->request->data['token'] = md5(microtime());
            $user = $this->User->save($this->request->data);
            if(!$user) {
                throw new InvalidDataException($this->User->validationErrors);
            }
            $to = $user['User']['email'];
            $email = new CakeEmail('site');                       
            $email->template('site_active_account')
            ->emailFormat('html')
            ->from(array('info@5stars.vn' => Configure::read('site_name')))
            ->to($to)
            ->bcc(array('thienkimlove@gmail.com'))
            ->subject(__(Configure::read('site_name'). ' - activate your account'))
            ->viewVars(array('user' => $user['User']))
            ->send();
            $this->set(array(
                'user' => $user,
                '_serialize' => array('user')
            ));
        } else {
            throw new MissingDataException('No data provided');
        }
    }

    public function edit($id) {            
        if (!empty($this->request->data)) {

            $check = $this->authenticate();
            if(!$check) {
                throw new NotAllowedRequestException();
            }

            $postData = $this->request->data;
            $user = (isset($postData['User']))? $postData['User'] : $postData;  

            $user['id'] = $id;

            // unset email to prevent updating it directly but only if e-mail confirmation needed                    
            unset($user['email']);

            //for change password.
            if (!empty($user['new_password'])) {
                $user['password'] = $user['new_password'];
            } 

            $this->User->unbindAll(); 

            if($user = $this->User->save($user)) {                                        

                $this->set(array(
                    'user' => $user,
                    '_serialize' => array('user')
                ));
            } else {
                throw new InvalidDataException($this->User->validationErrors);
            }  
        } else {
            throw new MissingDataException('No user data provided');
        }
    }

    public function view($id) {            
        $user = $this->authenticate();
        if(!$user) {
            throw new NotAllowedRequestException();
        }
        if($user['User']['id'] != $id) {
            $this->User->recursive =  -1;
            $user = $this->User->findById($id);
        }
        $this->set(array(
            'user' => $user,
            '_serialize' => array('user')
        ));
    }        

    /**
    * Special actions needed to login and activate an account
    */
    public function auth() {
        try {
            $user = $this->authenticate();
        } catch (IncorrectCredentialsException $e) {
            $user = false;
        }
        $this->set(array(
            'user' => $user,
            '_serialize' => array('user')
        ));
    }

    public function viewByToken() {
        $this->User->unbindAll();
        $this->set(array(
            'user' => $this->User->findByToken($this->getParam('token')),
            '_serialize' => array('user')
        ));
    }

    public function viewByFacebookId() {
        $this->User->unbindAll();
        $this->set(array(
            'user' => $this->User->findByFacebookId($this->getParam('facebook_id')),
            '_serialize' => array('user')
        ));
    }

    public function viewByFacebookEmail() {
        $this->User->recursive = -1;
        $this->set(array(
            'user' => $this->User->findByEmail($this->getParam('email')),  
            '_serialize' => array('user')
        ));
    }

    public function requestPassword() {
        $this->User->recursive = -1;
        $user = $this->User->findByEmail($this->getParam('email'));
        if(empty($user['User']['id'])){
            throw new InvalidDataException('User with email ' . $this->getParam('email') . ' not found');
        }
        $user['User']['password'] = substr(base64_encode(md5(microtime())),-9,-1);
        if(!$this->User->save($user)) {
            throw new InvalidDataException($this->User->validationErrors);   
        }
        $email = new CakeEmail('site');
        $email->template('site_request_password')    
        ->emailFormat('html')
        ->from(array('info@5stars.vn' => Configure::read('site_name')))
        ->to($user['User']['email'])
        ->subject(__('New '. Configure::read('site_name') .' Password', true))
        ->viewVars(array('new_pass' => $user['User']['password'], 'user_name' => $user['User']['fullname']))
        ->send();

        $this->set(array(
            'status' => true,
            '_serialize' => array('status')
        ));
    }

    public function checkEmail() {
        $user = $this->authenticate();
        $this->User->recursive = -1;
        $user = $this->User->findByEmail($this->getParam('email'));  

        $this->set(array(
            'status' => ($user)? 1 : 0,                
            '_serialize' => array('status')
        ));
    }


    public function activate() {
        $this->User->recursive = -1;
        $user = $this->User->findByToken($this->getParam('token'));
        if(empty($user)) {               
            throw new InvalidDataException(__('Could not find user with token ', true) . $this->getParam('token'));
        }
        $user['User']['token'] = null;
        unset($user['User']['password']);

        $user['User']['status'] = 'active';
        $email = new CakeEmail('site');
        $email->template('site_welcome')
        ->emailFormat('html')
        ->from(array('info@5stars.vn' => Configure::read('site_name')))
        ->to($user['User']['email'])
        ->subject(__('Welcome to '. Configure::read('site_name')))
        ->viewVars(array('user'=>$user['User']));  



        if(!$this->User->save($user)) {
            throw new InvalidDataException($this->User->validationErrors);
        }
        if (isset($email)) {
            $email->send();
        }
        $this->set(array(
            'message' => __('Your account has been activated. Please login to start using '. Configure::read('site_name') .'.', true),
            '_serialize' => array('message')
        ));     
    }

    public function requestRegistrationLink() {
        $this->User->recursive = -1;
        $user = $this->User->findByEmail($this->getParam('email'));
        if(empty($user['User']['id'])){
            throw new InvalidDataException(__('No user found with email ', true) . $this->getParam('email'));
        }

        if(empty($user['User']['token'])){
            throw new InvalidDataException(__('User with email ', true) . $this->getParam('email') . __(' is already active', true));
        }

        $to = $user['User']['email'];
        $email = new CakeEmail('site');                       
        $email->template('site_active_account')
        ->emailFormat('html')
        ->from(array('info@5stars.vn' => Configure::read('site_name')))
        ->to($to)
        ->subject(__(Configure::read('site_name'). ' - activate your account'))
        ->viewVars(array('user' => $user['User']))
        ->send();

        $this->set(array(
            'status' => true,
            '_serialize' => array('status')
        ));
    }

    public function delete($id) {
        //TODO admin group to delete.
    }


}
?>