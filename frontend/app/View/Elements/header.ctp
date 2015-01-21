<div class="header">
    HEADER
    <div id="notification">
        <?php
        echo $this->Session->flash();
        echo $this->Session->flash('error', array('params' => array('class' => 'message error')));
        echo $this->Session->flash('success', array('params' => array('class' => 'message success')));
        ?>
        <div id="formMessage" style="display: none;" class="message"></div>
    </div> 

</div>
