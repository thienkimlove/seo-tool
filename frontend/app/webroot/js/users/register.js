angular.module('fdb.directives', ['site.directives']);
angular.module('fdb.services', ['site.services']).
    factory('User', function(fdbModel) {
        return fdbModel('User');
    });
angular.module('fdb.filters', []);
angular.module('fdb', ['fdb.services', 'fdb.directives', 'fdb.filters']).
    controller('registerCtrl', function($scope, User, $http, $filter, showFormMessage, $compile) {
                 
        $scope.user = (Config.user) ? Config.user : {};

        $scope.user.password = '';

        //State variables
        $scope.atSubmitting = false;
     
        $scope.doRegister = function(event, user) {
           
            if ($scope.atSubmitting)
                return; //Avoid re-submitting
           

            if ($scope.registerForm.$invalid) {
                event.preventDefault();                 
                showFormMessage.error($scope.registerForm, Config.message, '#notification');
                $('html, body').animate({scrollTop: 0}, 'slow');
                $scope.atSubmitting = false;
                return;
            }

            $scope.atSubmitting = true;  
            $('#registerForm').submit();

        };     
     
    });