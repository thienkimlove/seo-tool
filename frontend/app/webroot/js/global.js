/**
* internationalisation helper, used to set and get translations
* getter: __('myString')
* setter: __('mySeting', 'myTranslatedString')
*/
__ = function(toTranslate, translation) {
    if(typeof(I18n) == "undefined") {
        I18n = {
            translations: {}
        };
    }
    if(!I18n.hasOwnProperty('translations')) {
        I18n.translations = {};
    }
    if(translation != undefined) {
        I18n.translations[toTranslate] = translation;
    } else {
        if(I18n.translations.hasOwnProperty(toTranslate)) {
            return I18n.translations[toTranslate];
        } else {
            return toTranslate;
        }
    }
};

/**
* some string helper
*/
String.prototype.toUnderscore = function(){
    return this.replace(/(?!^.?)([A-Z])/g, function($1){return "_" + $1;}).toLowerCase();
};
String.prototype.trim = function(){
    return this.replace(/^\s+|\s+$/g, '');
};

/**
 * some array helper
 */
Array.prototype.clean = function(deleteValue) {
    for ( var i = 0; i < this.length; i++) {
        if (this[i] == deleteValue) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};

$(function() {

    $('#wrap').height($(window).height());
    $(window).resize(function(){
        $('#wrap').height($(window).height());
    });


    /**
     * Open external links in new window
     */
    $('a[href^="http://"],a[href^="https://"]').not(
            '[href*="' + window.location.host + '/"]').click(function(e) {
        var url = this.href;
        e.preventDefault();
        e.stopPropagation();
        window.open(url);
    });
});

/**
* some global angular directives
*/
angular.module('ng').

// disables the button after click to prevent multiple clicks
directive('disableOnClick', function(){
    return function(scope, elm, attrs){
        elm.click(function() {
            elm.attr('disabled', 'disabled');
            elm.addClass(attrs.disableOnClick);
        });
    };
}).
directive('validNumberOfChar', function(){
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            function validator(value, count) {              
                var valid = true;
                if (count) {
                    if(value && (!isNaN(count)) && value.length < count) {
                            valid = false;
                   }
                }             
                ctrl.$setValidity('number', valid); 
                return value;                
            };
            ctrl.$parsers.unshift(function(viewValue){//when input change
                return validator(viewValue, scope.$eval(attrs.validNumberOfChar));
            });
            ctrl.$formatters.unshift(function(modelValue){//when load exist value
                return validator(modelValue, scope.$eval(attrs.validNumberOfChar));
            });
            scope.$watch(attrs.validNumberOfChar, function (newValue){
                return validator(scope.$eval(attrs.ngModel), newValue);
            }, true);
        }
    };
}).
// disables the button after click to prevent multiple clicks and adds the loading animation
//directive('loadingOnClick', function(){
//    return function(scope, elm, attrs){
//        elm.click(function() {
//            elm.attr('disabled', 'disabled');
//            elm.addClass('loading');
//            elm.append('<span class="loading"><span><i class="icon i-icon"></i>');
//        });
//    };
//}).
        // disables the button after click to prevent multiple clicks and adds the loading animation
directive('loadingOnClick', function(){
    return function(scope, elm, attrs){
        elm.click(function() {
            var loading = true
            if (attrs.loadingOnClick != '') {
                loading = scope.$eval(attrs.loadingOnClick);
            }
            if (loading) {
                elm.attr('disabled', 'disabled');
                elm.addClass('loading');
                elm.append('<span class="loading"><span><i class="icon i-icon"></i>');
            }
            return false;
        });
    };
}).
//This directive sets a type of the input field to date if the browser supports it and appends a datepicker otherwise
directive('datePicker', function(){

    var el = document.createElement('input');
    el.setAttribute('type','date');
    var typeDateSupport = (el.type === "date");

    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            if (typeDateSupport) {
                elm.attr("type", "date");
                elm.attr("placeholder", null);                
            } else {
                elm.attr("type", "text");
                elm.attr("readonly", "readonly");
                elm.datepicker({
                    dateFormat: 'dd.mm.yy' // TODO: internationalize this
                });
            }
            ctrl.$parsers.unshift(function(value) {
                if (typeDateSupport) {
                    return value
                } else {
                    return moment(value, 'DD.MM.YYYY').format('YYYY-MM-DD');
                }
            });
            ctrl.$formatters.unshift(function(value) {
                if (typeDateSupport) {
                    return moment(value).format('YYYY-MM-DD');
                } else {
                    return moment(value).format('DD.MM.YYYY');
                }
            });
        }
    };
}).
directive('colorPicker', function($parse){

    var el = document.createElement('input');
    el.setAttribute('type','color');
    var typeColorSupport = (el.type === "color");

        // 0 = not loaded
        // 1 = loading first script
        // 2 = loading second script
        // 3 = done
    var minicolorsStatus = 0;
        callbacks = [];
    var loadMinicolor = function(callback) {
        if (minicolorsStatus == 3) {
            callback();
        } else if (minicolorsStatus < 3 && minicolorsStatus > 0) {
            callbacks.push(callback);
        } else { // load minicolors
            callbacks.push(callback);
            minicolorsStatus = 1;
            var script = document.createElement('script');
            script.setAttribute('type', 'text/javascript');
            script.setAttribute('src', Config.baseUrl + '/lib/jquery-minicolors/jquery.minicolors.min.js');
            var css = document.createElement('link');
            css.setAttribute('rel', 'stylesheet')
            css.setAttribute('type', 'text/css')
            css.setAttribute("href", Config.baseUrl + '/lib/jquery-minicolors/jquery.minicolors.css')
            document.getElementsByTagName('head')[0].appendChild(script);
            document.getElementsByTagName('head')[0].appendChild(css);
            var onLoad = function(e) {
                minicolorsStatus++;
                if (minicolorsStatus == 3) {
                    for (var i = 0; i < callbacks.length; i++) {
                        callbacks[i]();
                    }
                }
            };
            script.addEventListener('load', onLoad);
            css.addEventListener('load', onLoad);
        }
    };
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ngModel) {

            var settings = angular.extend({sharp: true}, scope.$eval(attrs.colorPicker));

            if (typeColorSupport) {
                elm.attr("type", "color");
            } else {
                elm.attr("type", "hidden");
                loadMinicolor(function() {
                    elm.minicolors();
                    ngModel.$render = function() {
                        elm.minicolors('value', ngModel.$viewValue);
                    }
                });
            }
            ngModel.$formatters.unshift(function(modelValue) {
                if (modelValue) {
                    if (modelValue.match(/^[0-9a-fA-F]{6}$/)) {
                        return '#' + modelValue;
                    }
                    if (modelValue.match(/^#[0-9a-fA-F]{6}$/)) {
                        return modelValue;
                    }
                }
                return null;
            });
            ngModel.$parsers.unshift(function(viewValue) {
                if(settings.sharp){
                    return viewValue;
                } else {
                    return (viewValue) ? viewValue.replace('#','') : viewValue;
                }
            });
        }
    };
}).
directive('confirm', function() {
    return function(scope, iElement, iAttrs) {
        iElement.click(function(){
            var html = '<div id="confirmdelete" class="alert-message" style="display:none;">';
            html += '<div class="alert-content">';
            html += '<p class="message-content">' + iAttrs.confirm + '</p>';
            html += '<p class="button-proceed">';
            html += "<button type='button' class='buttons confirm'>" + __('OK') + "</button>";
            html += '<button style="margin-left: 10px;" type="button" class="buttons cancel">' + __('Cancel') + '</button>';
            html += '</p>';
            html += '</div>';

            var popup = $(html);
            $('body').append(popup);
            popup.find('.confirm').click(function() {
                if(iAttrs.action) {
                    scope.$apply(function(){
                        scope.$eval(iAttrs.action);
                    });
                } else if (iAttrs.href) {
                    window.location = iAttrs.href;
                }
                popup.fadeOut(200, function() {
                    popup.remove();
                });
            });
            popup.find('.cancel').click(function() {
                popup.fadeOut(200, function() {
                    popup.remove();
                });
            });
            
            popup.css('top', ($(window).height() - popup.height()) / 2 + 'px')
                .css('left', ($(window).width() - popup.width())/2+'px')
                .css('opacity',1).fadeIn(200);
            
            return false;
        });
    }
}).
directive('validNumber', function(){
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            function validator(value) {              
                var numberValid = true;                
                if ((value) && (isNaN(value))) {
                        numberValid = false;
               }                             
                ctrl.$setValidity('number', numberValid); 
                return value;                
            };
            ctrl.$parsers.unshift(function(viewValue){//when input change
                return validator(viewValue);
            });
            ctrl.$formatters.unshift(function(modelValue){//when load exist value
                return validator(modelValue);
            });            
        }
    };
})
.filter('startFrom', function() {
    return function(input, start) {
        
        if(input!==undefined){
            start = +start; //parse to int
            return input.slice(start);
        }
         
    }
})
.filter('timeAgo', function(){
    return function(input) {
        if (input === false) {
			return __('not yet taken');
		}
		if(input) {            
            var localOffset = parseFloat(moment().format('Z'));
			return moment(input).add('h',localOffset).fromNow();
		}
        return input;
    };
})
.filter('formatDateTime', function(){
    return function(input) {
        if(input) {
            return moment.utc(input).tz(Config.timeZone).format('L LT');
        }
        return input;
    };
})
.filter('formatDate', function(){
    return function(input) {
        if(input) {
            return moment.utc(input).tz(Config.timeZone).format('L');
        }
        return input;
    };
})
.filter('sanitizeLink', function(){
    return function(input) {
        if(input) {
            if (input.indexOf('http')) {
                return 'http://'+input;
            }    
            else return input;         
        }
        else return input;
    };
})
.filter('trustAsHtml', function($sce){
    return function(input) {
        // TODO: sanitize input, maybe we should include ng-sanitize
        return $sce.trustAsHtml(input);
    };
})
.filter('stripHtmlTags', function() {
    return function(text) {
        return String(text).replace(/<[^>]+>/gm, '');
    };
})
.filter('truncate', function () {
    return function (text, length, end) {
        if (isNaN(length))
            length = 10;

        if (end === undefined)
            end = "...";

        if (text.length <= length || text.length - end.length <= length) {
            return text;
        }
        else {
            return String(text).substring(0, length-end.length) + end;
        }
    };
})
.factory('$exceptionHandler', function($injector, $log) {
    return function(exception, cause) {
        try {
            $log.error.apply($log, arguments);
            if(typeof window.applicationError == "undefined") {
                window.applicationError = true;
                var isOldBrowser = window.navigator.userAgent.indexOf("Mozilla/4.0") != -1;
                $('<div class="js-error" ><p>We´re sorry. An error occured while processing your request. The support team has been informed.</p>' +
                   '<a href="#" onClick="history.back(); return false;">Back</a></div>').dialog({ 
                    resizable: false,
                    modal: true
                });
                if(!isOldBrowser) {
                    var http = $injector.get('$http');
                    exception.useragend = window.navigator.userAgent;
                    http.post(Config.baseUrl + '/proxy/notification/error', { origin: location.href, error: exception });
                }
            }
        } catch (e) {
            // do nothing
        }
    };
})
// add some usefull stuff to the rootScope
.run(function($rootScope, $location){
    // routing
    $rootScope.history = {
        queue: [],
        _isBack: false,
        _isFirst: true,
        back: function() {
            $rootScope.history._isBack = true;
            $rootScope.history.queue.pop();
            var path = $rootScope.history.queue.pop();
            if (path) {
                $location.url(path);
            } else {
                $location.url('/');
            }
        }
    }
    $rootScope.$on('$locationChangeSuccess', function(){
        if ($rootScope.history._isBack) {
            $rootScope.history.direction = 'back';
            $rootScope.history._isBack = false;
        } else {
            $rootScope.history.direction = 'forward';
        }
        if ($rootScope.history._isFirst) {
            $rootScope.animationClass = null
            $rootScope.history.direction = 'first';
            $rootScope.history._isFirst = false;
        }
        $rootScope.history.queue.push($location.url());
    });

    $rootScope.translate = function(toTranslate, lang) {
        if(typeof toTranslate == 'string') {
            return __(toTranslate);
        }
        if (toTranslate) {
            if (lang && toTranslate.hasOwnProperty(lang)) {
                return toTranslate[lang];
            } 
            if (toTranslate.hasOwnProperty(Config.language)) {
                return toTranslate[Config.language];
            }
            for (var key in toTranslate) {
                if (toTranslate[key]) {
                   return toTranslate[key]; 
                }   
            }
            return '';
        }
        return;
    };
});

//check for existing console object and create it if not existing
//used for internet explorer which does not create this object automatically
if (!window.console) { 
    window.console = {
        log: function(obj){}
    };
}          
function shareOnFacebook(options) {
    
    options = options || {};
    options.forceRedirect = options.forceRedirect || false;
    options.link = options.link || location.href;
    options.redirect = options.redirect || (/feedbackstr\.com/i.test(location.href) ? location.href : 'http://www.feedbackstr.com');
    options.picture = options.picture || 'http://www.feedbackstr.com/img/acp/logo.png';
    options.name = options.name || 'Feedbackstr';
    options.caption = options.caption || 'Verbessern Sie die Beziehungen zur Ihren Kunden durch direktes Feedback!';
    options.description = options.description || 'Feedbackstr';

    if( options.forceRedirect || /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
        var fbUrl = 'https://www.facebook.com/dialog/feed?' + 
          'app_id=' + options.fbId + '&' +
          'link=' + encodeURI(options.link) + '&' +
          'picture=' + encodeURI(options.picture) + '&' +
          'name=' + encodeURI(options.name) + '&' +
          'caption=' + encodeURI(options.caption) + '&' +
          'description=' + encodeURI(options.description) + '&' +
          'redirect_uri=' + encodeURI(options.redirect);
        window.location.assign(fbUrl);
    }else{
        FB.ui({
            method: 'feed',
            //redirect_uri: 'http://www.feedbackstr.com/js-close.html',
            link: options.link,
            picture: options.picture,
            name: options.name,
            caption: options.caption,
            description: options.description,
          }, function(response){
              //alert("response");
          });
    }
}
