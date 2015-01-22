<?php echo __('Hello'); ?><br /><br />
<?php echo __('Confirm your post'); ?> <br/>

<?php echo __('Post Title'); ?>: <?php echo $post['Post']['name']; ?>
<?php echo __('Post status'); ?>: <?php echo $post['Post']['status']; ?>
<?php if(!empty($post['Post']['note'])) {?>
<?php echo __('Notes'); ?>: <?php echo $post['Post']['note']; ?>
<?php } ?>
<br/><br/>
<?php echo __('We wish you a lot of success with your SEO Ranking Tool!'); ?>
<br/><br/>
<?php echo __('Your SEO Ranking Tool Team'); ?>