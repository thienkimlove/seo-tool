<?php $this->Ng->ngController('registerCtrl') ?>
<?php $this->start('script') ?>
<script type="text/javascript">
    Config.user = <?php echo isset($tokenUser) ? json_encode($tokenUser, true) : '{}' ?>;
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
<div id="content">
    <div class="section-register">

        <h2><?php echo __('Join '. Configure::read('site_name')) ?></h2>
       
        <div class="box">
            <h3><?php echo __('Already a member?') ?> <a href="<?php echo $this->Html->url(['controller' => 'users', 'action' => 'login']) ?>"><?php echo __('Sign In') ?></a> <span>»</span></h3>
            <div class="content">

                <form name="registerForm" novalidate method="post" action="<?php echo $this->here ?>">
                    <div class="column-one">
                        <ul class="input">
                            <li id="registerValidationMessage">
                            </li>

                            <li>
                                <label for="user-email"><?php echo __('Your email') ?></label>
                                <input name="email" ng-model="user.email"  type="email" maxlength="50" required/>
                            </li>
                            
                            <li>
                                <label for="userpass"><?php echo __('Choose a password') ?></label>
                                <input type="password" name="password"  ng-model="user.password" required 
                                       ng-minlength=6 ng-maxlength=30 ng-pattern="/(?=.*[a-z])(?=.*[^a-zA-Z])/" />
                            </li>

                        </ul>
                        <p class="term"><?php echo __('By signing up, I agree to '. Configure::read('site_name')) ?> <a href=""><?php echo __('terms of service') ?></a>. </p>
                        <button type="submit" ng-click="doRegister($event, user)" class="buttons button-blue done">
                            <span ng-show="!atSubmitting"><?php echo __('Sign up') ?></span>
                            <span ng-show="atSubmitting"><?php echo __('Submitting...') ?></span>
                        </button>
                    </div>
                </form>
                <div class="column-two">

                    <div class="or">or</div>

                    <ul class="social-network">
                        <li>
                            <a ng-click="facebookRegister($event, user)" class="button-social facebook">
                                <i class="icon i-facebook" ></i>
                                <span><?php echo __('Join with facebook'); ?></span>
                            </a>
                            <a class="create-new-user" ng-hide="true" action="createNewAccount()" ></a>
                        </li>
                    </ul>

                </div>

            </div>
        </div>

    </div>
</div>