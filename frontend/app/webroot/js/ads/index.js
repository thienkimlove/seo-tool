angular.module('fdb.directives', ['site.directives'])
.directive('validateRange', ['$parse', function($parse) {

    function link($scope, $element, $attrs, ngModel) {
        var attrRange, range = [];

        function validate(value) {
            var validMin = true, validMax = true;
            if (typeof range[0] === 'number') {
                ngModel.$setValidity('min', value >= range[0]);
                validMin = value >= range[0];
            }
            if (typeof range[1] === 'number') {
                ngModel.$setValidity('max', value <= range[1]);
                validMax = value <= range[1];
            }
            return validMin && validMax ? value : undefined;
        }

        attrRange = $attrs.validateRange.split(/,/);

        range[0] = $parse(attrRange[0] || '')($scope);
        range[1] = $parse(attrRange[1] || '')($scope);

        $scope.$watchCollection('[' + $attrs.validateRange + ']', function(vals) {
            range = vals;
            validate(ngModel.$viewValue);
        });

        ngModel.$parsers.unshift(validate);
        ngModel.$formatters.unshift(validate);
    }

    return {
        link: link,
        require: 'ngModel'
    };

}])
.directive('psDatetimePicker', function () {
      var format = 'YYYY-MM-DD hh:mm:ss';

      return {
          restrict: 'A',
          require: 'ngModel',
          link: function (scope, element, attributes, ctrl) {
              element.datetimepicker({
                  format: format
              });
              var picker = element.data("DateTimePicker");

              ctrl.$formatters.push(function (value) {
                  var date = value;
                  if (date.isValid()) {
                      return date.format(format);
                  }
                  return '';
              });

              /**
              * Update datetime picker's value from ng-model when opening the datetime picker's dropdown
              */
              element.on('dp.show', function() {
                  picker.setDate(ctrl.$viewValue);
              });

              /**
              * Update ng-model when  datetime picker's value changes
              */
              element.on('change', function (event) {
                  scope.$apply(function () {
                      var date = picker.getDate();
                      ctrl.$setViewValue(date);
                  });
              });
          }
      };
  });
angular.module('fdb.services', ['site.services']).
factory('User', function(fdbModel) {
    return fdbModel('User');
});
angular.module('fdb.filters', []);
angular.module('fdb', ['fdb.services','fdb.directives', 'fdb.filters']).
controller('AdsIndexCtrl',  function($scope, User, showFormMessage, $http, $compile, $timeout) {
    //State variables
    $scope.atSubmitting = false;

    $scope.ads = {};


    $scope.doRegister = function(event, ads) {

        if ($scope.atSubmitting) {
            event.preventDefault();
            return; //Avoid re-submitting
        }

        if ($scope.createAdsForm.$invalid) {
            event.preventDefault();
            showFormMessage.error($scope.createAdsForm, Config.message, '#notification');
            $('html, body').animate({scrollTop: 0}, 'slow');
            $scope.atSubmitting = false;
            return;
        }

        $scope.atSubmitting = true;

        $('#createAdsForm').submit();
    };

});