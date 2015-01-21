<?php echo $this->Html->charset(); ?> 

<?php if (!empty($isMobile)){ ?>
    <?php // set viewport for fill_out and review to support the responsive layouts ?>
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />	
    <?php }	else { ?>
    <meta name="viewport" content="width=1024" />
    <?php } ?> 

<meta name="keywords" content="<?php echo __(Configure::read('site_keyword'), true); ?>" />
<meta name="title" content="<?php echo __(Configure::read('site_title'), true); ?>" />
<meta name="description" content="<?php echo __(Configure::read('site_description'), true); ?>">
<?php if(($this->request->controller == 'users' && $this->request->action == 'login') || ($this->request->controller == 'users' && $this->request->action == 'register')) { ?>
    <meta content="noindex,nofollow" name="robots">
    <?php } else { ?>
    <meta content="index,follow" name="robots">

    <?php } ?>