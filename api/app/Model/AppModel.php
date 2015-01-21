<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    public $actsAs = array('Containable');

    public $autogeneratedFields = array('modified', 'created', 'updated');

       public $defaultOptions = array(
           'limit' => 3000,
           'offset' => 0
       );

    public $jsonFields = array();
       
       public function beforeValidate($options = array()) {
        // unset date fields
           foreach($this->autogeneratedFields as $field) {
            unset($this->data[$this->name][$field]);
           }
        // handle json fields
        foreach ($this->jsonFields as $field) {
            if(!empty($this->data[$this->alias][$field]) && is_array($this->data[$this->alias][$field])) {
                $this->data[$this->alias][$field] = json_encode($this->data[$this->alias][$field]);
            }
        }
        return parent::beforeValidate($options);;
       }

    public function afterFind($results = array(), $primary = false) {
        $results = parent::afterFind($results, $primary);
        foreach ($results as $key => &$val) {
            foreach ($this->jsonFields as $field) {
                if(!empty($val[$this->alias][$field])) {
                    $decoded = json_decode($val[$this->alias][$field], true);
                    if (json_last_error() == JSON_ERROR_NONE && is_array($decoded)) {
                        $val[$this->alias][$field] = $decoded;
                    }
                }
            }
        }
        return $results;
    }

    /**
     * @deprecated use http://book.cakephp.org/2.0/en/core-libraries/behaviors/containable.html instead
     */
    public function unbindAll($reset = true) {
           $this->unbindAllExcept('', $reset);
       }

    /**
     * @deprecated use http://book.cakephp.org/2.0/en/core-libraries/behaviors/containable.html instead
     */
       public function unbindAllExcept($relations, $reset = true) {
           if(!is_array($relations)) {
               $relations = array($relations);
           }
        $toUnbind = array();
        foreach(array('hasMany', 'belongsTo', 'hasOne', 'hasAndBelongsToMany') as $assoc) {
            if(property_exists($this, $assoc)) {
                $toUnbind[$assoc] = array();
                foreach ($this->{$assoc} as $key => $val) {
                    // dont unbind $relations, translations and properties
                    if(!in_array($key, $relations) && !in_array($val['className'], array('Property'))) {
                        $toUnbind[$assoc] []= $key;
                    }
                }
            }
        }
           $this->unbindModel($toUnbind, $reset);
       }
       
       public function buildOptions($data) {
           $options = array('conditions' => $this->buildConditions($data));
           if(isset($data['limit']) && $data['limit'] > 0) {
               $options['limit'] = (int) $data['limit'];
           } else {
               $options['limit'] = $this->defaultOptions['limit'];
           }
           if(isset($data['offset']) && $data['limit'] > -1) {
               $options['offset'] = (int) $data['offset'];
           } else {
               $options['offset'] = $this->defaultOptions['offset'];
           }
           return $options;
       }
       
       public function buildConditions($data) {
           $conditions = array();
           foreach ($data as $param => $value) {
               if($this->hasField($param, true)) {
                if($this->getColumnType($param) == 'text') {
                    $conditions[$this->name . '.' . $param. ' LIKE'] = $value;
                } else {
                    if( is_string($value) && 'null' == strtolower($value)) {
                        $value = null;
                    }
                    $conditions[$this->name . '.' . $param] = $value;
                }
               }
           }
           return $conditions;
       }
       
       protected function _removeUnallowedHtml($data) {
           // remove all html attributes
           $regex1 = '/\s*(?![href\s*=])[a-z]*\s*=\s*"[^"]*"\s*/';
           // remove all unallowed html tags
           $regex2 = '/<(?!\/?(a|b|u|i|div|br|p|ul|li|ol)\/?).*?>/';
           if(is_array($data)) {
               foreach($data as &$value) {
                   $value = preg_replace($regex1, '', $value);
                   $value = preg_replace($regex2, '', $value);
               }
           } elseif (is_string($data)) {
               $data = preg_replace($regex1, '', $data);
               $data = preg_replace($regex2, '', $data);
           }
           return $data;
       }
}