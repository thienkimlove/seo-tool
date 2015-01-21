<?php

class LessController extends AppController {

    public $autoRender = false;

    public function beforeFilter() {
        // overwrite parent beforeFilter
    }
    
    public function index(){
        header('Content-type: text/css');
        if(sizeof($this->params['pass']) !== 1) {
            return;
        }
        $result = Cache::read(str_replace('/', '_', $this->params['pass'][0]), '_less_');
        if(!$result) {
            $params = explode('/', $this->params['pass'][0]);
            $less = new lessc;
            $lessRoot = APP.WEBROOT_DIR.DS.'less';
            $less->setImportDir($lessRoot);
            // include kube
            $lessString = '';
            // include global.less
            $lessString .= '@import "global.less";';
            // include layout specific less
            if(sizeof($params > 0) && is_file($lessRoot . DS . 'layout-' . $params[0] . '.less')) {
                $lessString .= '@import "layout-' . $params[0] . '.less'.'";';
            }
            // include controller specific less
            if(sizeof($params > 1) && is_file($lessRoot . DS . $params[1] . '.less')) {
                $lessString .= '@import "' . $params[1] . '.less' . '";';
            }
            // include action specific less
            if(sizeof($params > 2) && is_file($lessRoot . DS . $params[1] . DS . $params[2] . '.less')) {
                $lessString .= '@import "' . $params[1] . '/' . $params[2] . '.less' . '";';
            }
            if(Configure::read('debug') == 0) {
                $less->setFormatter('compressed');
            }
            $less->setVariables(array('base' => '~"' . $this->base . '"'));
            $result = $less->compile($lessString);
            Cache::write(str_replace('/', '_', $this->params['pass'][0]), $result, '_less_');
        }
        return $result;
    }
}
?>
