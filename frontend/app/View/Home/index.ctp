<?php $this->start('script') ?>
<script type="text/javascript"> 
    Config.ads = <?php echo (!empty($ads)) ? json_encode($ads, true) : '[]' ?>;  
    Config.message = [
        {'field': 'email', 'condition': 'required', 'content': '<?php echo __('Email is required') ?>'},
        {'field': 'email', 'condition': 'email', 'content': '<?php echo __('Email is invalid') ?>'},
        {'field': 'password', 'condition': 'required', 'content': '<?php echo __('Password is required') ?>'}
    ];
</script>
<?php $this->end() ?>
<?php $this->Ng->ngController('HomeIndexCtrl') ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List Ads</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>            
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">              
            <div class="panel-body">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Landing Page</th>
                                <th>Expected Views</th>
                                <th>Expected Hours</th>
                                <th>Start Date</th>
                                <th>End Date</th>                                  
                            </tr>
                        </thead>
                        <tbody>
                          <tr ng-repeat="ad in ads">
                                <td ng-bind="ad.Content.id"></td>
                                <td ng-bind="ad.Content.name"></td>
                                <td ng-bind="ad.Content.url"></td>
                                <td ng-bind="ad.Content.landing_page"></td>
                                <td ng-bind="ad.Content.expect_view_number"></td>
                                <td ng-bind="ad.Content.expect_hours"></td>
                                <td ng-bind="ad.Content.start_date"></td>
                                <td ng-bind="ad.Content.end_date"></td>
                          </tr>
                          
                        </tbody>
                    </table>
                </div>
                
            </div>                    
          
        </div>
      
    </div>
    
</div>