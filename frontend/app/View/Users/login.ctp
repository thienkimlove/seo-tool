<?php $this->start('script') ?>
<script type="text/javascript">   
    Config.message = [
        {'field': 'email', 'condition': 'required', 'content': '<?php echo __('Email is required') ?>'},
        {'field': 'email', 'condition': 'email', 'content': '<?php echo __('Email is invalid') ?>'},
        {'field': 'password', 'condition': 'required', 'content': '<?php echo __('Password is required') ?>'}
    ];
</script>
<?php $this->end() ?>
<?php $this->Ng->ngController('userLoginCtrl') ?>
<div class="col-md-4 col-md-offset-4">
    <div class="login-panel panel panel-default">
       
        <div class="panel-heading">
            <h3 class="panel-title">Please Sign In</h3>             
        </div>
        <div class="alert-info" id="notification" style="display: none;"></div>
        <div class="panel-body">
            <form role="form" name="userLoginForm" method="post" action="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'login')) ?>" novalidate>
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus ng-model="user.email" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="password" type="password" ng-model="user.password"  required>
                    </div>
                   
                    <!-- Change this to a button or input when using this as a form -->
                    <button ng-click="doLogin($event)" class="btn btn-lg btn-success btn-block">Login</button>
                </fieldset>
            </form>
            
            <div class="well-sm"><a class="button" href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'register')) ?>">Register</a></div>
        </div>
    </div>
</div>