<?php

App::uses('AppHelper', 'View/Helper');

class NgHelper extends AppHelper {

    protected $_ngInit = [];
    protected $_ngController = '';

    public function ngInit($data) {
        $this->_ngInit = array_merge($this->_ngInit, $data);
    }

    public function ngInitOut() {
        if (!empty($this->_ngInit)) {
            $out = 'ng-init=\'';
            foreach ($this->_ngInit as $key => $value) {
                $out .= $key . '=' . json_encode($value, JSON_HEX_APOS ) . '; ';
            }
            return $out . '\'';
        }
        return '';
    }

    public function ngController($ctrl) {
        $this->_ngController = $ctrl;
    }

    public function ngControllerOut() {
        if (!empty($this->_ngController)) {
            return 'ng-controller="' . $this->_ngController . '"';
        } else {
            return '';
        }
    }

    public function ngAppOut() {
        return 'ng-app="fdb"';
    }

}

?>