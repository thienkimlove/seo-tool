<?php   
$layout = Inflector::underscore($this->layout);
$controller = Inflector::underscore($this->request->controller);
$action = Inflector::underscore($this->request->action);
/**
* Include compiled less
*/
echo $this->Html->css($layout . '/' . $controller . '/' . $action); 

/**
* Include global.js, jQuery and angulajs, jQuery UI
*/
echo $this->Html->script('/lib/jquery/dist/jquery.min');
echo $this->Html->script('/lib/jquery-ui/jquery-ui.min');
echo $this->Html->css('/lib/jquery-ui/themes/smoothness/jquery-ui.min');
echo $this->Html->script('/lib/angular/angular.min');

echo $this->Html->script('/lib/autofill-event/src/autofill-event');

echo $this->Html->script('/lib/jquery-html5-placeholder-shim/jquery.html5-placeholder-shim');
echo $this->Html->script('/lib/angular-placeholder-shim/angular-placeholder-shim');

echo $this->Html->script('/lib/bpopup/jquery.bpopup.min');

echo $this->Html->script('global');
echo $this->Html->script('angular-site');

/**
* Include layout specific js
*/
if(is_file(APP.WEBROOT_DIR . DS . 'js' . DS . 'layout-' . $layout . '.js')) {
    echo $this->Html->script('layout-'.$layout);
}

/**
* Incudes controller specific js
*/
if (is_file(APP . WEBROOT_DIR . DS . 'js' . DS . $controller . '.js')){
    echo $this->Html->script($controller);
}

/**
* Incudes action specific js
*/
if (is_file(APP . WEBROOT_DIR . DS . 'js' . DS . $controller . DS . $action . '.js')){
    echo $this->Html->script($controller . '/' . $action);
}
?>
<link href='http://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
<style>
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
        display: none !important;
    }
</style>

<script type="text/javascript">            
    Config = {        
        baseUrl: "<?php echo $this->base ?>",        
        GoogleAnalytics: <?php if (Configure::read('GoogleAnalytics')) { echo 'true';} else { echo 'false'; } ?>,    
        user :  <?php echo json_encode($user, true) ?>    
    };     
</script>
