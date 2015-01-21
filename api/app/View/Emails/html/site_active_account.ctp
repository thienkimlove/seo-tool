<?php echo __('Hello'); ?><br /><br />
<?php echo __('To activate your new email address please click on the following link:'); ?> <br/>
<?php echo Configure::read('Url') .'activation/'.$user['token']; ?>
<br/><br/>
<?php echo __('We wish you a lot of success with your VietLancer!'); ?>
<br/><br/>
<?php echo __('Your VietLancer Team'); ?>