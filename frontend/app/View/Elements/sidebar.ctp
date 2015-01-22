<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">               
                <a class="navbar-brand" href="index.html">SEO Ranking Tool</a>
            </div>
            

            <ul class="nav navbar-top-links navbar-right">             
                
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'logout')) ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    
                </li>
                
            </ul>
            

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            
                        </li>
                        <li <?php echo ($this->request->controller == 'home' && $this->request->action == 'index') ? 'class="active"' : '' ?>>
                            <a href="<?php echo $this->Html->url('/') ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i> Promotion</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i> Campaign</a>
                        </li>
                        <li <?php echo ($this->request->controller == 'ads' && $this->request->action == 'index') ? 'class="active"' : '' ?>>
                            <a href="<?php echo $this->Html->url(array('controller' => 'ads', 'action' => 'index')) ?>"><i class="fa fa-dashboard fa-fw"></i> Ads</a>
                        </li>
                       
                    </ul>
                </div>
                
            </div>
            
        </nav>