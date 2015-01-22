angular.module('fdb.directives', ['site.directives']);
angular.module('fdb.services', ['site.services']).
factory('User', function(fdbModel) {
    return fdbModel('User');
});
angular.module('fdb.filters', []);
angular.module('fdb', ['fdb.services','fdb.directives', 'fdb.filters']).
controller('HomeIndexCtrl',  function($scope, User, showFormMessage, $http, $compile, $timeout) {
    $scope.ads = Config.ads;
});