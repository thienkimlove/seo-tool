angular.module('fdb.directives', ['vlancer.directives']);
angular.module('fdb.services', ['vlancer.services']).
factory('User', function(fdbModel) {
    return fdbModel('User');
});
angular.module('fdb.filters', []);
angular.module('fdb', ['fdb.services','fdb.directives', 'fdb.filters']).
controller('UserProfileCtrl',  function($scope, $http, User, showFormMessage, $timeout) {

    $scope.tab = 'public';
    $scope.loadTab = function(tab) {
        $scope.tab = tab;
    }
    
    $scope.canClick = true;  

    $scope.user = Config.user;
    $scope.userMaster = angular.copy($scope.user);
    
    //update form functions.
    $scope.updateUser = function (event) {
    
        event.preventDefault();
        
        var buttonId, form_name;
        
        if ($scope.tab == 'public') {
           form_name = 'updateUserPublicForm';
           buttonId = 'save-public';
        } else if ($scope.tab == 'details') {
           form_name = 'updateUserDetailsForm';
           buttonId = 'save-details';
        } else {
           form_name = 'updateUserSettingForm';
           buttonId = 'save-setting';
        }
        
        
        $scope.canClick = false;
        
        if (angular.equals($scope.user,$scope.userMaster)) {            
            showFormMessage.notification(__('Please change something before submit!'), '#formMessage');         
            $timeout(function(){
                $('html, body').animate({scrollTop: 0}, 'fast');
            });     
            $scope.canClick = true;    
        } else {            
            if ($scope[form_name].$invalid) {
                $timeout(function(){                    
                    $('html, body').animate({scrollTop: elm.offset().top}, 'fast');
                    $('#save').removeClass('loading').removeAttr('disabled').find('span').remove();
                });
                $scope.canClick = true;
            } else {            
                User.edit($scope.user.id, angular.toJson($scope.user)).then(function(res){
                   $('#' + buttonId).removeClass('loading').removeAttr('disabled').find('span').remove(); 
                   if (res.User != undefined) {
                      if ($scope.user.new_password) {
                        window.location =  Config.baseUrl + '/' + Config.language + '/users/logout';
                      } else {
                        showFormMessage.notification(__('Update success!'), '#formMessage');
                        $scope.userMaster = angular.copy($scope.user);
                      }                      
                   } else {
                     showFormMessage.notification(__('Error while updating user information. Please try again later!'), '#formMessage'); 
                   }
                   $scope.canClick = true;
                }); 
            }
        }
    };
    //facebook part.
    $scope.facebookLinkMessage = null;
    $scope.facebookLinkStatus = true;
    $scope.needUpdatePassword = false;
    
    $scope.facebookLink = function() {
        $scope.facebookLinkStatus = true;
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {  
                $scope.connectFB(response.authResponse.userID);
            } else if (response.status === 'not_authorized') {               
                FB.login(function(fbres){
                    if (fbres.authResponse) {                        
                        $scope.connectFB(fbres.authResponse.userID);
                    } 
                    }, {scope: 'email, publish_stream'});
            } else {
                // the user isn't logged in to Facebook.
                FB.login(function(fbres){
                    if (fbres.authResponse) {
                        $scope.connectFB(fbres.authResponse.userID);
                    }
                    }, {scope: 'email, publish_stream'});
            }}, true);
    };
    $scope.connectFB = function(fbId) {     
        $http.post(Config.baseUrl + '/' + Config.language + '/users/mapFacebookToUser/', { facebook_id :  fbId  })
          .success(function(response){ 
            $scope.facebookLinkMessage = response.message;         
            if (response.status == 'success') {
                $scope.user.facebook_id = fbId;                               
            } else {                
                $scope.facebookLinkStatus = false;
            }
        });     
    };
    $scope.facebookUnlink = function() {
        $scope.needUpdatePassword = false;
        User.get($scope.user.id).then(function(user){
            if (user.User.password == null || !user.User.password) { 
                $scope.needUpdatePassword = true;
                return false;                               
            } else {                
                User.edit($scope.user.id, { facebook_id : '' }).then(function(){
                    window.location =  Config.baseUrl + '/' + Config.language + '/users/logout';
                });
            }
        });

    };

});
