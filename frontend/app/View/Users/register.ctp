<?php $this->Ng->ngController('registerCtrl') ?>
<?php $this->start('script') ?>
<script type="text/javascript">
    
    Config.message = [
        {'field': 'email', 'condition': 'required', 'content': '<?php echo __('Email is required') ?>'},
        {'field': 'email', 'condition': 'email', 'content': '<?php echo __('Email is invalid') ?>'},
        {'field': 'password', 'condition': 'required', 'content': '<?php echo __('Password is required') ?>'},
        {'field': 'password', 'condition': 'minlength', 'content': '<?php echo __('Minimum length of password is 6 characters') ?>'},
        {'field': 'password', 'condition': 'maxlength', 'content': '<?php echo __('Maximum length of password length is 30 characters') ?>'},
        {'field': 'password', 'condition': 'pattern', 'content': '<?php echo __('Password musts contain number and letter') ?>'},            
    ];

</script>
<?php echo $this->end(); ?>
<div class="col-md-4 col-md-offset-4">
    
   
    <div class="login-panel panel panel-default">
        
        <div class="panel-heading">
            <h3 class="panel-title">Please Fill All Fields Below</h3>
        </div>
        
        <div class="alert-info" id="notification" style="display: none;"></div>
        
        <div class="panel-body">
            <form role="form" name="registerForm" novalidate method="post" action="<?php echo $this->here ?>">
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" placeholder="E-mail" name="email"  autofocus ng-model="user.email"  type="email" maxlength="50" required>
                    </div>                       
                   
                    
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="password" type="password"  ng-model="user.password" required 
                                       ng-minlength=6 ng-maxlength=30 ng-pattern="/(?=.*[a-z])(?=.*[^a-zA-Z])/">
                    </div>
                   
                    <!-- Change this to a button or input when using this as a form -->
                     <button type="submit" ng-click="doRegister($event, user)" class="btn btn-lg btn-success btn-block">
                            <span ng-show="!atSubmitting"><?php echo __('Sign up') ?></span>
                            <span ng-show="atSubmitting"><?php echo __('Submitting...') ?></span>
                        </button>
                </fieldset>
            </form>
        </div>
    </div>
</div>