<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php echo $this->fetch('title', __(Configure::read('site_title'), true));  ?></title>
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        <?php 
        echo $this->element('css_and_js');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>  

    </head>

    <body>     
        <div id="wrapper" <?php echo $this->Ng->ngAppOut() ?> <?php echo $this->Ng->ngInitOut() ?>>
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

            <?php echo $this->element('sidebar') ?>

            <!-- Page Content -->
            <div id="page-wrapper" <?php echo $this->Ng->ngControllerOut() ?>>
                <?php echo $this->fetch('content'); ?>

                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->

        </div>

    </body>

</html>
