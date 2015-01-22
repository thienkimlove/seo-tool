<?php   
$layout = Inflector::underscore($this->layout);
$controller = Inflector::underscore($this->request->controller);
$action = Inflector::underscore($this->request->action);


echo $this->Html->css('/lib/bootstrap/dist/css/bootstrap.min');
echo $this->Html->css('/lib/metisMenu/dist/metisMenu.min');
echo $this->Html->css('/lib/startbootstrap-sb-admin-2/dist/css/sb-admin-2');
echo $this->Html->css('/lib/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min');
echo $this->Html->css('/lib/font-awesome/css/font-awesome.min');


echo $this->Html->script('/lib/jquery/dist/jquery.min');
echo $this->Html->script('/lib/bootstrap/dist/js/bootstrap.min');
echo $this->Html->script('/lib/metisMenu/dist/metisMenu.min');
echo $this->Html->script('/lib/startbootstrap-sb-admin-2/dist/js/sb-admin-2');
echo $this->Html->script('/lib/moment/moment');

echo $this->Html->script('/lib/angular/angular.min');
echo $this->Html->script('/lib/autofill-event/src/autofill-event');
echo $this->Html->script('/lib/jquery-html5-placeholder-shim/jquery.html5-placeholder-shim');
echo $this->Html->script('/lib/angular-placeholder-shim/angular-placeholder-shim');

echo $this->Html->script('/lib/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min');

 
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
