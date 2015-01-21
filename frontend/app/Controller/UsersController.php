<?php
class UsersController extends AppController {

    public function logout() {
        $this->Session->destroy();
        $this->redirect(array(
            'controller' => 'users', 'action' => 'login'
        ));
    }    
    public function login() {
        $this->layout = 'login';

        if ($this->Session->check('Auth.User.Id')) {
            $this->redirect(array(                
                'controller' => 'home'
            ));
            return;
        }

        if ($this->request->is('post')) {
            $user = $this->request->data;   

            if (!empty($user['js_disabled'])) {
                $this->Session->setFlash(__('Your can not login without javascript', true), 'default', array(), 'error');
                return;
            }
            $this->Session->write('Auth.User.Email', $user['email']);
            $this->Session->write('Auth.User.Password', $user['password']);
            
            try {
                $result = $this->Api->resource('User')->request('/auth');              

            } catch (ApiServerException $e) {
                $this->Session->delete('Auth');
                $this->Session->setFlash(__('Incorrect email or password', true), 'default', array(), 'error');
                return;
            }
            if (empty($result['user'])) {
                $this->Session->delete('Auth');
                $this->Session->setFlash(__('Incorrect email or password', true), 'default', array(), 'error');
                return;
            }
            if ($result['user']['User']['status'] != 'active') {
                $this->Session->delete('Auth');
                $message1 = __("Your account has not yet been activated. Please check your email to activate. If you didn't receive any activation email,", true);
                $message2 = __("click here", true);
                $message3 = __("to request a new one.", true);
                $message = $message1 . " <a href='" . $this->base . '/users/forgotactivelink' . "'>" . $message2 . "</a> " . $message3;
                $this->Session->setFlash($message, 'default', array(), 'error');
                return;
            }
            // login
            $this->Session->write('Auth.User.Id', $result['user']['User']['id']);


            $this->redirect(array( 
                'controller' => 'home'
            ));
        } 

    }
    public function register($token = null) {
        $this->layout = 'login';
        if ($token) {
            $tokenUser = $this->Api->resource('User')->request('/viewByToken', array(
                'data' => array(
                    'token' => $token
                )
            ));
            if (isset($tokenUser['user']['User'])) {
                $tokenUser = $tokenUser['user']['User'];
                $this->set('tokenUser', $tokenUser);
            }
        }
        if (!empty($this->request->data)) {
            $userData = $this->request->data;

            try {
                if (isset($tokenUser) && !empty($tokenUser['id'])) {
                    $userData['id'] = $tokenUser['id'];
                    $result = $this->Api->resource('User')->edit($tokenUser['id'], $userData);
                } else {
                    $result = $this->Api->resource('User')->add($userData);
                }
                // set tmp authentication
                $this->Session->write('Auth.User.Email', $userData['email']);
                $this->Session->write('Auth.User.Password', $userData['password']);
                $userId = $result['user']['User']['id'];

                //clean up authentication
                $this->Session->delete('Auth');

                if ($token) {
                    $this->Session->setFlash(__('Thank You for complete the registration progress. Now you can login with your design login and password', true), 'default', array(), 'success');
                } else {
                    $this->Session->setFlash(__('Thank You for registering<br />We have sent a confirmation email to you to confirm your registration. Please click on the link in the email to confirm the registration and activate your account.<br />It is possible that the email could end up in your spam folder, so please check there just in case. If you do find an email in your spam folder, do not forget to mark it as safe to ensure that you receive future messages from us.', true), 'default', array(), 'success');
                }
                $this->redirect(array( 
                    'action' => 'login'));

            } catch (ApiServerException $e) {
                // clean up authentication
                $this->Session->delete('Auth');
                if ($e->error == 'InvalidDataException') {
                    $this->Session->setFlash($e->message, array(), 'error');
                } elseif ($e->error == 'DuplicateEmailException') {
                    $this->Session->setFlash(__('Look like you already have account at Seo Ranking Tool. Please login'), array(), 'error');
                    $this->redirect(array( 
                        'action' => 'login'));

                } else {
                    throw $e;
                }
                return;
            }

        }
    }
    public function activation($token) {
        $this->Session->delete('Auth');
        if (empty($token)) {
            $this->Session->setFlash(__('This link is invalid', true), 'default', array(), 'error');
            $this->redirect(array(                 
                'controller' => 'users', 'action' => 'login'));
        }
        try {
            $result = $this->Api->resource('User')->request('/activate/', array(
                'data' => array(
                    'token' => $token
                )
            ));
            if (!empty($result['message'])) {
                $this->Session->setFlash(__($result['message']), 'default', array(), 'success');
            }
        } catch (ApiServerException $e) {
            $this->Session->setFlash($e->message, 'default', array(), 'error');
        }
        $this->redirect(array('controller' => 'users', 'action' => 'login'));
    }
    //end of basic function.

    public function profile() {

    }

}