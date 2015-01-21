angular.module('fdb.directives', ['site.directives']);
angular.module('fdb.services', ['site.services']).
factory('User', function(fdbModel) {
    return fdbModel('User');
});
angular.module('fdb.filters', []);
angular.module('fdb', ['fdb.services','fdb.directives', 'fdb.filters']).
controller('userLoginCtrl',  function($scope, User, showFormMessage, $http, $compile, $timeout) {
   
    $scope.user = {
        email : '',
        password : ''
    };    

    $scope.doLogin = function(event) {
        event.preventDefault();        
        //fix the case just user choose email then password fill in but blur event not happened.
        $('form[name=userLoginForm] input').trigger('change blur');
        $timeout(function(){
            if ($scope.userLoginForm.$invalid) {
                showFormMessage.error($scope.userLoginForm, Config.message, '#notification');
            } else {
                $('form[name=userLoginForm]').submit();
            }
          }, 0); 
    };
    
    $scope.showFacebookLogin = true;
    $scope.facebookData = null;
    $scope.createNewAccount = function() {
        User.add(angular.toJson($scope.facebookData)).then(function(user){
            if (user.User != undefined) {
                $("#formMessage").removeClass('success').removeClass('error').addClass('success').show().html('').html('<ul><li>' + Config.successRegister + '</li></ul>').focus();
                $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
            } else {
                $("#formMessage").removeClass('success').removeClass('error').addClass('error').show().html('').html('<ul><li>' + __('Can not register with your Facebook information.') + '</li></ul>').focus();
                $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
            }
        });
    };
    $scope.checkingFacebook = function(fbId) { 
        $scope.showFacebookLogin = false;
        //check if have email permission.
        FB.api('/me/permissions', function(r) {
            var grants = false; 
            if (r.data.length > 0) {
                for (var i = 0; i < r.data.length; i++) {
                    if (r.data[i].hasOwnProperty('permission') && r.data[i].permission == 'email' && r.data[i].status == 'granted') {
                        grants = true;
                    }
                }
            }                       
            if (Object.keys(r.data[0]).indexOf('email') >= 0) {
                   grants = true;
            }
            if (grants) {
                FB.api('/me', function(fbdata) {
                    if (!fbdata || fbdata.error || !fbdata.email) {
                        window.location.reload();
                    } else {  
                        $scope.facebookData = { email : fbdata.email, facebook_id : fbdata.id };  
                        $http.post(Config.baseUrl +  "/users/facebook/", fbdata).success(function (response) {
                            if (response.status == 'success') {  
                                window.location =  Config.baseUrl;            
                            } else if(response.status == 'nonExistFacebookId') {

                                $('.create-new-user').attr('confirm', response.message);
                                $compile($('.create-new-user'))($scope);
                                $('.create-new-user').trigger('click');

                            }  else {
                                $("#formMessage").removeClass('success').removeClass('error').addClass('error').show().html('').html('<ul><li>' + response.message + '</li></ul>').focus();
                                $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                            }
                        });
                    }
                });
            } else {
                console.log(r);
                $("#formMessage").removeClass('success').removeClass('error').addClass('error').show().html('').html('<ul><li>' + __('You need to allow email permission in order to using this feature') + '</li></ul>').focus(); 
                $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
            }
        });

    }
    $scope.facebookLogin = function(event) {
        event.preventDefault();
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {                                     
                $scope.checkingFacebook(response.authResponse.userID);
            } else if (response.status === 'not_authorized') {               
                FB.login(function(fbres){
                    if (fbres.authResponse) {                        
                        $scope.checkingFacebook(fbres.authResponse.userID);
                    } 
                    }, {scope: 'email, publish_stream'});
            } else {
                // the user isn't logged in to Facebook.
                FB.login(function(fbres){
                    if (fbres.authResponse) {
                        $scope.checkingFacebook(fbres.authResponse.userID);
                    }
                    }, {scope: 'email, publish_stream'});
            }}, true);
    };
});