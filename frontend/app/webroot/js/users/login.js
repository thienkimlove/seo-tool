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
   
});