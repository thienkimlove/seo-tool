<!DOCTYPE html>
<html>
    <head>   
        <?php echo $this->Html->charset(); ?>    
        <title>
            <?php echo $this->fetch('title', __(Configure::read('site_title'), true));  ?>            
        </title>
        <link href="<?php echo $this->Html->url('/img/favicon.png');?>" type="image/x-icon" rel="icon" />
        <link href="<?php echo $this->Html->url('/img/favicon.png');?>" type="image/x-icon" rel="shortcut icon"/>

        <link rel="apple-touch-icon" href="<?php echo $this->Html->url('/apple-touch-icon-precomposed.png');?>"/> 
        <link rel="apple-touch-icon-precomposed" href="<?php echo $this->Html->url('/apple-touch-icon-precomposed.png');?>"/>  
        <?php            			
        echo $this->element('css_and_js');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->element('google_analytics');
        echo $this->element('browser_update');
        echo $this->element('alexa'); 			
        echo $this->Html->script('/lib/jquery.cookie/jquery.cookie');
        
        ?>
        <script type="text/javascript">
            /* Here is the right place for global JS translations */
            __('Confirm crop', '<?php echo __('Confirm crop'); ?>');
        </script>
    </head>
    <body>
        <div id="page"  <?php echo $this->Ng->ngAppOut() ?> <?php echo $this->Ng->ngInitOut() ?>>
            <?php echo $this->element('header') ?>
            
            <?php echo $this->element('facebook'); ?>
           
            <div>
                <div <?php echo $this->Ng->ngControllerOut() ?>>
                    <?php echo $this->fetch('content'); ?>
                </div><!-- .inner -->
            </div><!-- end #content -->


            <?php echo $this->element('footer') ?>
        </div>    
    </body>
</html>
