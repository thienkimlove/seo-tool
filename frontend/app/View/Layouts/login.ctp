<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php echo $this->fetch('title', __(Configure::read('site_title'), true));  ?></title>
        <?php 
        echo $this->element('css_and_js');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>  

    </head>

    <body>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php
                            echo $this->Session->flash();
                            echo $this->Session->flash('error', array('params' => array('class' => 'alert alert-danger')));
                            echo $this->Session->flash('success', array('params' => array('class' => 'alert alert-success')));
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" <?php echo $this->Ng->ngAppOut() ?> <?php echo $this->Ng->ngInitOut() ?>>
            <div class="row" <?php echo $this->Ng->ngControllerOut() ?>>
                <?php echo $this->fetch('content'); ?>
            </div> 
        </div>

    </body>

</html>
