angular.module('fdb.directives', ['site.directives']);
angular.module('fdb.services', ['site.services']).
    factory('User', function(fdbModel) {
        return fdbModel('User');
    });
angular.module('fdb.filters', []);
angular.module('fdb', ['fdb.services', 'fdb.directives', 'fdb.filters']).
    controller('registerCtrl', function($scope, User, $http, $filter, showFormMessage, $compile) {
        
        $scope.facebookData = null;
        $scope.user = Config.user;

        $scope.user.password = '';

        //State variables
        $scope.atSubmitting = false;
        $scope.registerType = null;

        $scope.doRegister = function(event, user) {
            if ($scope.atSubmitting)
                return; //Avoid re-submitting

            if ($scope.registerType == 'facebook') {
                return;
            }

            if ($scope.registerForm.$invalid) {
                event.preventDefault();
                showFormMessage.error($scope.registerForm, Config.message, '#notification');
                $('html, body').animate({scrollTop: 0}, 'slow');
                $scope.atSubmitting = false;
                return;
            }

            $scope.atSubmitting = true;
            $scope.registerType = 'normal';
            $('#registerForm').submit();

        };


        $scope.checkingFacebook = function(fbId) {
            // check if a specific permission was granted
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
                            $scope.facebookData = {email: fbdata.email, facebook_id: fbdata.id};
                            $http.post(Config.baseUrl +  "/users/facebook/", fbdata).success(function(response) {
                                if (response.status == 'success') {
                                    window.location = Config.baseUrl;
                                } else if (response.status == 'nonExistFacebookId') {

                                    $('.create-new-user').attr('confirm', response.message);
                                    $compile($('.create-new-user'))($scope);
                                    $('.create-new-user').trigger('click');

                                } else {
                                    $("#formMessage").removeClass('success').removeClass('error').addClass('error').show().html('').html('<ul><li>' + response.message + '</li></ul>').focus();
                                    $('html, body').animate({scrollTop: 0}, 'slow');
                                    $scope.registerType = null;
                                }
                            });
                        }
                    });
                } else {
                    $("#formMessage").removeClass('success').removeClass('error').addClass('error').show().html('').html('<ul><li>' + __('You need to allow email permission in order to using this feature') + '</li></ul>').focus();
                    $('html, body').animate({scrollTop: 0}, 'slow');
                    $scope.registerType = null;
                }
            });
        }
        $scope.facebookRegister = function(event) {
            event.preventDefault();
            if ($scope.registerType == 'normal') {
                return;
            }
            $scope.registerType = 'facebook';
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    $scope.checkingFacebook(response.authResponse.userID);
                } else if (response.status === 'not_authorized') {
                    FB.login(function(fbres) {
                        if (fbres.authResponse) {
                            $scope.checkingFacebook(fbres.authResponse.userID);
                        }
                    }, {scope: 'email, publish_stream'});
                } else {
                    // the user isn't logged in to Facebook.
                    FB.login(function(fbres) {
                        if (fbres.authResponse) {
                            $scope.checkingFacebook(fbres.authResponse.userID);
                        }
                    }, {scope: 'email, publish_stream'});
                }
            }, true);
        };
    });