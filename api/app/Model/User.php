<?php

class User extends AppModel {
   
    public $order = 'User.created DESC';
    
    public $validate = array(
        'email' => array(
            'rule1' => array(
                'rule' => array('email', true),
                'message' => 'Please supply a valid email address',
                'required' => 'create'
            ),
            'rule2' => array(
                'rule' => 'isUnique',
                'message' => 'This email already exists'
            ),
        )
    );

    protected static function _generateSalt($length = 22) {
        $salt = str_replace(
            array('+', '='), '.', base64_encode(sha1(uniqid(Configure::read('Security.salt'), true), true))
        );
        return substr($salt, 0, $length);
    }

    protected function _encrypt($password, $salt) {
        return sha1(sha1(sha1($password)) . $salt);
    }
    
    public function beforeFind($queryData) {
        if (isset($queryData['conditions']['User.password'])) {
            $queryData['conditions']['User.password = SHA1(CONCAT(SHA1(SHA1(?)),User.salt))'] = $queryData['conditions']['User.password'];
            unset($queryData['conditions']['User.password']);
        }
        return $queryData;
    }

    public function beforeSave($options = array()) {
        $resutl = parent::beforeSave($options);
        // encrypt
        if (!empty($this->data[$this->alias]['password'])) {
            if (!isset($this->data[$this->alias]['salt'])) {
                $this->data[$this->alias]['salt'] = $this->_generateSalt();
            }
            $this->data[$this->alias]['password'] = $this->_encrypt($this->data[$this->alias]['password'], $this->data[$this->alias]['salt']);
        }
        return $resutl;
    }

}
