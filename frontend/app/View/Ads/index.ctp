<?php $this->start('script') ?>
<script type="text/javascript">   
    Config.message = [
        {'field': 'name', 'condition': 'required', 'content': '<?php echo __('Ad Name is required') ?>'},
        {'field': 'url', 'condition': 'required', 'content': '<?php echo __('Website Url is required') ?>'},
        {'field': 'landing_page', 'condition': 'required', 'content': '<?php echo __('Landing Page is required') ?>'},
        {'field': 'expect_view_number', 'condition': 'required', 'content': '<?php echo __('Views Expected Number is required') ?>'},
        {'field': 'expect_hours', 'condition': 'required', 'content': '<?php echo __('Expected Time is required') ?>'},
        {'field': 'start_date', 'condition': 'required', 'content': '<?php echo __('Start Date is required') ?>'},
        {'field': 'end_date', 'condition': 'required', 'content': '<?php echo __('End Date is required') ?>'},       
    ];
</script>
<?php $this->end() ?>
<?php $this->Ng->ngController('AdsIndexCtrl') ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Create Ad</h1>
    </div>      
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">

            <div class="panel-body">
                <div class="alert alert-info" id="notification" style="display: none;"></div>
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form" name="createAdsForm" method="post" action="<?php echo $this->Html->url(array('controller' => 'ads', 'action' => 'index')) ?>" novalidate>                       
                            <div class="form-group">
                                <label>Ad Name</label>
                                <input class="form-control" placeholder="Ad Name" type="text" name="name" ng-model="ads.name" required>
                            </div>

                            <div class="form-group">
                                <label>Website Url</label>
                                <input class="form-control" placeholder="Website Url" type="text" name="url" ng-model="ads.url" required>
                            </div>
                            
                             <div class="form-group">
                                <label>Start Date</label>                               
                                <input class="form-control" placeholder="Start Date" type="text" ps-datetime-picker name="start_date" ng-model="ads.start_date" >
                            </div>

                            <div class="form-group"> 
                                <label>End Date</label>                              
                                <input class="form-control" placeholder="End Date" type="text" ps-datetime-picker name="end_date" ng-model="ads.end_date" >
                            </div>

                            <div class="form-group">
                                <label>Choose Landing Page</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="landing_page" value="google" ng-model="ads.landing_page">Google
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="landing_page" value="facebook" ng-model="ads.landing_page">Facebook
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="landing_page" value="yahoo" ng-model="ads.landing_page">Yahoo
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="landing_page" value="youtube" ng-model="ads.landing_page">YouTube
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Input Views Expected Number</label>
                                <input class="form-control" placeholder="Views Expected Number" type="text" name="expect_view_number" ng-model="ads.expect_view_number" required>
                            </div>

                            <div class="form-group">
                                <label>Input Expected Time (Hours)</label>
                                <input class="form-control" placeholder="Expected Time" type="text" name="expect_hours" ng-model="ads.expect_hours" data-validate-range="1, 100000" ng-pattern="/^[0-9]+$/" required>
                            </div>

                           
                            
                            <button type="submit" ng-click="doRegister($event, user)" class="btn btn-default">
                                <span ng-show="!atSubmitting"><?php echo __('Create') ?></span>
                                <span ng-show="atSubmitting"><?php echo __('Submitting...') ?></span>
                            </button>

                        </form>
                    </div>                
                </div>

            </div>

        </div>

    </div>

</div>