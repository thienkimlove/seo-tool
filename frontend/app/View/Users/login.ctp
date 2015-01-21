
<?php $this->start('script') ?>
<script type="text/javascript">

    Config.successRegister = <?php echo json_encode(__('Thank You for registering<br />We have sent a confirmation email to you to confirm your registration. Please click on the link in the email to confirm the registration and activate your account.<br />It is possible that the email could end up in your spam folder, so please check there just in case. If you do find an email in your spam folder, do not forget to mark it as safe to ensure that you receive future messages from us.', true)) ?>;

    __('You need to allow email permission in order to using this feature', <?php echo json_encode(__('You need to allow email permission in order to using this feature', true)) ?>);
    __('Can not register with your Facebook information.', <?php echo json_encode(__('Can not register with your Facebook information.', true)) ?>);

    Config.message = [
        {'field': 'email', 'condition': 'required', 'content': '<?php echo __('Email is required') ?>'},
        {'field': 'email', 'condition': 'email', 'content': '<?php echo __('Email is invalid') ?>'},
        {'field': 'password', 'condition': 'required', 'content': '<?php echo __('Password is required') ?>'}
    ];
</script>
<?php $this->end() ?>
<?php $this->Ng->ngController('userLoginCtrl') ?>
<div id="content">
    <div class="section-login">
        <h2><?php echo __('Join '. Configure::read('site_name')); ?></h2>
        <h3><?php echo __('Become a seller or start buying stuff from $5 now!'); ?></h3>
        <div class="box">
            <div class="content">
                <form name="userLoginForm" method="post" action="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'login')) ?>" novalidate>
                    <ul class="input">
                        <li id="loginValidationMessage">
                        </li>
                        <li>
                            <label for="email-username"><?php echo __('Email/Username'); ?></label>
                            <input id="email-username" type="text"  name="email" ng-model="user.email" required/>
                        </li>
                        <li>
                            <label for="password"><?php echo __('Password'); ?></label>
                            <input id="password" type="password" name="password" ng-model="user.password"  required/>
                        </li>
                        <li>
                            <button class="buttons button-blue done" type="submit" ng-click="doLogin($event)"><?php echo __('Login'); ?></button>
                        </li>
                    </ul>
                </form>
            </div>
            <div class="footer">
                <ul class="social-network">
                    <li>
                        <a ng-show="showFacebookLogin" ng-click="facebookLogin($event)" class="button-social facebook">
                            <i class="icon i-facebook" ></i>
                            <span><?php echo __('Join with facebook'); ?></span>
                        </a>
                        <a class="create-new-user" ng-hide="true" action="createNewAccount()" ></a>
                    </li>
                </ul>
                <p><?php echo __('Not a member yet?'); ?> <a href="<?php echo Router::url('/', true) . 'users/register' ?>"><?php echo __('Register now'); ?></a> - <?php echo __("it's fun and easy!"); ?></p>
            </div>
        </div>
    </div>
</div>