'use strict';

/**
* Directives
*/
angular.module('site.directives', ['placeholderShim']).
directive('rtEditor', function(){
    var generatedIds = 0;
    return {
        require: 'ngModel',
        link:  function (scope, elm, attrs, ngModel) {
            elm.hide();
            // editor instance
            var editor;

            // generate an ID if not present
            if (!attrs.id) {
                attrs.$set('id', 'wysihtml5-' + generatedIds++);
            }
            
            var adjustHeight = function() {
                if(editor) {
                    $(editor.composer.iframe).css('min-height', editor.iframeBody.height());
                }
            };
            
            var updateView = function () {
                ngModel.$setViewValue(editor.getValue());
                if (!scope.$root.$$phase) {
                    scope.$apply();
                }
            };
            
            ngModel.$render = function() {
                elm.val(ngModel.$viewValue);
                if(editor) {
                    editor.setValue(ngModel.$viewValue);
                    adjustHeight();
                    if(!editor.iframeBody.is(':focus')) {
                        if(ngModel.$isEmpty(ngModel.$viewValue)) {
                            editor.fire('set_placeholder');
                        } else {
                            editor.fire('unset_placeholder');
                        }
                    }
                }
            };
            
            var build = function() {
                editor = new wysihtml5.Editor(attrs.id, { // id of textarea element
                    toolbar:      attrs.wysihtml5Toolbar, // id of toolbar element
                    parserRules:  {tags: {b: {}, u: {}, i: {}, br: {}, p: {}, a: {
                                set_attributes: {
                                    target: "_blank",
                                    rel:    "nofollow"
                                },
                                check_attributes: {
                                    href:   "url" // important to avoid XSS
                                }
                            }
                        }
                    },
                    // defined in parser rules set 
                    stylesheets: Config.baseUrl + '/css/wysihtml5-iframe.css'
                });
                editor.on('load', function() {
                    elm.parent().find('iframe.wysihtml5-sandbox').show();
                    editor.iframeBody = $(editor.composer.iframe).contents().find('body');
                    editor.iframeBody.css('overflow', 'hidden');
                    editor.iframeBody.css('min-height', '0');
                    adjustHeight();
                    editor.iframeBody.on('keyup keydown paste change focus', function() {
                        setTimeout(function(){
                            adjustHeight();
                            updateView();
                        }, 0);
                    });
                });
                // propagate some events
                elm.on('focus-on-editor', function() {
                    setTimeout(function() {
                        editor.focus();
                    }, 0);
                });
                editor.on('focus:composer', function() {
                    // we trigger a click to activate the controls in the design page
                    elm.trigger('click');
                });
                editor.on('aftercommand:composer', function(){
                    updateView();
                });
                // set initial value
                if(attrs.wysihtml5Focus) {
                    editor.on('focus', function(){
                        scope.$apply(attrs.wysihtml5Focus);
                    });
                    editor.on('show:dialog', function() {
                        // postpone this so it get called after the blur event
                        setTimeout(function(){
                            scope.$apply(attrs.wysihtml5Focus);
                        }, 0);
                    });
                }
                if(attrs.wysihtml5Blur) {
                    editor.on('blur', function(e, e2){
                        scope.$apply(attrs.wysihtml5Blur);
                    });
                }
            }
            
            setTimeout(function () {
                build();
                elm.on('reset-editor', function(){
                    elm.parent().find('iframe.wysihtml5-sandbox').remove();
                    build();
                });
            });
            
        }
    }
}).
directive('starRating', function($window, $parse, $compile){
    var generatedIds = 0;
    var bv = navigator.userAgent.match(/(msie) (\d+)/i);
    return {
        link: function(scope, iElement, iAttrs) { 
            if(bv && bv[0] === "MSIE 8" && bv[1] === "MSIE" &&bv[2] === "8"){
                return;
            }
            var id = generatedIds++;
            var val = Math.round(scope.$eval(iAttrs.starRating));
            var attDisabled = false;
            if(iAttrs.hasOwnProperty('disabled')) {
                attDisabled = true;
            };
            var html = "";
            for(var i = 1; i <= 5; i++) {
                html += '<input name="star-rating-' + id + '" type="radio" value="' + i + '" ';
                if(attDisabled) {
                    html += 'disabled="disabled" ';
                    if(val == i) {
                        html += 'checked="checked" ';
                    }
                }
                html += '/>';
            }
            var isInput = true;
            iElement.html(html);
            iElement.find('input').rating({
                callback: function(value) {
                    if (isInput) {
                        $parse(iAttrs.starRating).assign(scope, value); 
                        if(!scope.$$phase) {
                            scope.$digest();
                        }
                    }

                }
            }).rating('readOnly', attDisabled);
            scope.$watch(iAttrs.starRating, function(value) {
                setTimeout(function() {
                    isInput = false;
                    if(value != undefined) {
                        iElement.find('input').rating('select', Math.round(value) - 1);
                    } else {
                        iElement.find('.rating-cancel > a').triggerHandler('click');
                    }
                    isInput = true;
                    }, 0);
                }, true);   
            }
      };
}).
directive('imageUpload', function($window, $parse,$timeout) {
    return {
        compile: function compile(tElement, tAttrs, transclude){
            if($window.File && $window.FileReader && $window.FileList) {
                var crop = tAttrs.hasOwnProperty('crop');
                
                var maxImageSize = 15000000; //for testing. 
                if(tAttrs.hasOwnProperty('maxImageSize')) {
                    maxImageSize = tAttrs.maxImageSize;
                }
                
                var rawImage = (tAttrs.imageUpload + '_raw').replace(/[^a-zA-Z0-9]/g, '_');
                var showImage = (tAttrs.imageUpload + '_show').replace(/[^a-zA-Z0-9]/g, '_');
               
                var html = '<input type="file" style="display:none" />';
                html += '<div class="image-preview" >';
                if (crop) {
                    html += '<div class="progress"></div>';
                }                 
                html +=     '<img ng-show="' + showImage + '" ng-src="{{' + showImage + ' || null }}" />';                                 
                html += '</div>';

                if (crop) {
                    html += '<div class="slider-container"></div>';  
                    html += '<div class="save-container"></div>';
                }
                html += '<div class="image-message">{{' + tAttrs.defaultMessage + '}}</div>'; 
                html += '<div class="image-remove">x</div>';

                tElement.html(html);

                return function(scope, iElement, iAttrs) {
                    var origin = {
                        width : 0,
                        height : 0,                                          
                        snapToContainer : false,
                        image : {                          
                            imgW : 0,
                            imgH : 0,
                            w : 0,
                            h : 0,
                            posX : 0,
                            posY : 0,
                            scaleX : 0,
                            scaleY : 0
                        }                        
                    };
                    
                    var fixed = 'w';
                    if (tAttrs.hasOwnProperty('fixed')) {
                        fixed = 'h';
                    }
                    
                    var store = angular.copy(origin);
                    var ratio = iElement.width() / iElement.height();
                    if(tAttrs.hasOwnProperty('imageRatio')) {
                        ratio = tAttrs.imageRatio;
                    }
                    var input = iElement.find('input[type=file]');
                    var preview = iElement.find('div.image-preview');
                    var previewImage = iElement.find('div.image-preview > img:first');

                    var slider = iElement.find('div.slider-container');
                    var progressBar = preview.find('div.progress');
                    var saveButton =  iElement.find('div.save-container');
                                  
                    
                    var setHeight = function() {
                        store.width = iElement.width();
                        if(scope.$eval(rawImage) || scope.$eval(showImage)) {
                            store.height = iElement.width() / ratio;
                            iElement.height(store.height);                            
                        } else {
                            iElement.css('height', '');
                            store.height = iElement.height();
                        }                     
                    };
                    
                    var ApplyCssToImageBaseOnData = function(setSize, setPosition, forceStretch) { 
                      if (setSize) {
                         if ((fixed == 'w') && (store.image.w >= store.width || forceStretch)) { 
                            store.image.h = Math.round(store.image.h * (store.width / store.image.w));
                            store.image.w = store.width;
                        }
                        if ((fixed == 'h') && (store.image.h >= store.height || forceStretch)){
                            store.image.w = Math.round(store.image.w * (store.height / store.image.h));
                            store.image.h = store.height;
                        } 
                      }  
                      if (setPosition) {
                          store.image.posY =(store.height - store.image.h) / 2;
                          store.image.posX = (store.width - store.image.w) / 2; 
                      }        
                        previewImage.css({       
                            'position' : 'absolute',                                             
                            'top': store.image.posY,
                            'left': store.image.posX, 
                            'width': store.image.w,
                            'height':store.image.h                           
                        });                       
                    }
                    var calculateFactor = function() {
                        store.image.scaleX = (store.width / store.image.w);
                        store.image.scaleY = (store.height / store.image.h);
                    }

                    var getPercentOfZoom = function(image) {
                        var percent = 0;
                        if (image.w > image.h) {
                            percent = 150
                            - ((image.w * 100) / store.image.imgW);
                        } else {
                            percent = 150
                            - ((image.h * 100) / store.image.imgH);
                        }
                        return percent;
                    }

                    var createZoomSlider = function(){

                        var zoomContainerSlider = $("<div />").attr('class',
                            'zoomContainer').mouseover(function () {
                            $(this).css('opacity', 1);
                        }).mouseout(function () {
                            $(this).css('opacity', 0.6);
                        });

                        var zoomMin = $('<div />').attr('class', 'zoomMin').html(
                            "<b>-</b>");
                        var zoomMax = $('<div />').attr('class', 'zoomMax').html(
                            "<b>+</b>");

                        var $slider = $("<div />").attr('class', 'zoomSlider');

                        // Apply Slider
                        $slider
                        .slider({
                            orientation: 'vertical',
                            value: getPercentOfZoom(store.image),
                            min: 10,
                            max: 150,
                            step: 10,
                            slide: function (event, ui) {
                                var value = 150 - ui.value;
                                var zoomInPx_width = (store.image.imgW * Math.abs(value) / 100);
                                var zoomInPx_height = (store.image.imgH * Math.abs(value) / 100);

                                var difX = (store.image.w / 2) - (zoomInPx_width / 2);
                                var difY = (store.image.h / 2) - (zoomInPx_height / 2);

                                var newX = (difX > 0 ? store.image.posX
                                    + Math.abs(difX)
                                    : store.image.posX
                                    - Math.abs(difX));
                                var newY = (difY > 0 ?store.image.posY
                                    + Math.abs(difY)
                                    : store.image.posY
                                    - Math.abs(difY));
                                store.image.posX = newX;
                                store.image.posY = newY;
                                store.image.w = zoomInPx_width;
                                store.image.h = zoomInPx_height;
                                calculateFactor();
                                ApplyCssToImageBaseOnData(false, false, false);                                
                            }
                        });

                        zoomContainerSlider.append(zoomMin);
                        zoomContainerSlider.append($slider);
                        zoomContainerSlider.append(zoomMax);

                        zoomMin.addClass('vertical');
                        zoomMax.addClass('vertical');
                        $slider.addClass('vertical');
                        zoomContainerSlider.addClass('vertical');
                        zoomContainerSlider.css({
                            'position': 'absolute',
                            'top': 5,
                            'right': 5,
                            'opacity': 0.6
                        });
                        slider.html(zoomContainerSlider).show().on('click', function(event){
                            event.stopPropagation();
                        });
                    };
                    

                    //function to render image at first time after upload.
                    var renderFirstTime = function() {  
                        var image = new Image();
                        image.src = scope.$eval(rawImage);
                        image.onload = function() {
                        setHeight();       
                        store.image.w = store.image.imgW = image.width;
                        store.image.h = store.image.imgH = image.height;
                        //resize if fixed is setted.                                       
                        calculateFactor();                             
                        
                        if (crop) {
                                ApplyCssToImageBaseOnData(true, true, false);
                                image = null;                                
                                createZoomSlider();
                                saveButton.html('<button class="save save-crop">' + __('Confirm crop') + '</button>').show();
                                // adding draggable to the image
                                previewImage.draggable({
                                    refreshPositions: true,
                                    disabled : false,
                                    drag: function (event, ui) {
                                        store.image.posY = ui.position.top;
                                        store.image.posX = ui.position.left;
                                        ApplyCssToImageBaseOnData(false, false, false);
                                    },
                                    stop: function (event, ui) {
                                        ApplyCssToImageBaseOnData(false, false, false);
                                    }
                                });  

                            } else {
                                ApplyCssToImageBaseOnData(true, true, true);
                                //create canvans and save.
                                var canvas = document.createElement('canvas');
                                canvas.width = store.image.w;
                                canvas.height = store.image.h; 
                                canvas.getContext("2d").drawImage(image,0, 0, store.image.w, store.image.h);
                                var canvasUrl = canvas.toDataURL("image/png");
                                canvas = null;
                                image = null; 
                                $.ajax({                                 
                                    type: 'POST',
                                    url: Config.baseUrl + "/upload",
                                    data: { rawImage : canvasUrl },
                                    success: function(data){
                                        scope.$apply(function(){                               
                                            $parse(iAttrs.imageUpload).assign(scope, data);
                                            $parse(showImage).assign(scope, data);
                                            $parse(rawImage).assign(scope, undefined); 
                                            ApplyCssToImageBaseOnData(true, true, true);
                                        });
                                    }
                                });
                            }
                            
                        };

                    };


                  
                    var saveImage = function() {        
                        slider.empty().hide();  
                        saveButton.empty().hide();            
                        previewImage.draggable({ disabled: true });
                        //overlay preview pane .
                        preview.css({
                            'opacity': 0.6
                        });
                        //show upload progress bar                         
                        progressBar.html('<div class="bar"></div><div class="percent">0%</div >').show();
                        var image = new Image();
                        image.src = scope.$eval(rawImage);
                        image.onload = function() {
                            var canvas = document.createElement('canvas');
                            canvas.width = store.width;
                            canvas.height = store.height;                            
                            canvas.getContext("2d").drawImage(image,store.image.posX, store.image.posY,store.image.w,store.image.h);
                            var canvasUrl = canvas.toDataURL("image/png");
                            canvas = null;
                            image = null;
                            $.ajax({ 
                                xhr: function()
                                {
                                    var xhr = new window.XMLHttpRequest();
                                    //Upload progress
                                    xhr.upload.addEventListener("progress", function(evt){
                                        if (evt.lengthComputable) {

                                            var percentVal = Math.round(evt.loaded / evt.total * 100) + '%';
                                            progressBar.find('div.bar').width(percentVal)
                                            progressBar.find('div.percent').html(percentVal);
                                        }
                                        }, false);                                    
                                    return xhr;
                                },                                
                                type: 'POST',
                                url: Config.baseUrl + "/upload",
                                data: { rawImage : canvasUrl },
                                success: function(data){
                                    scope.$apply(function(){                               
                                        $parse(iAttrs.imageUpload).assign(scope, data);
                                        $parse(showImage).assign(scope, data);
                                        $parse(rawImage).assign(scope, undefined);                                  
                                        
                                        preview.css({
                                            'opacity': 1
                                        });     
                                        var percentVal = '100%';
                                        progressBar.find('div.bar').width(percentVal)
                                        progressBar.find('div.percent').html(percentVal);                                     
                                        previewImage.removeClass('ui-draggable-disabled').removeClass('ui-state-disabled').css({        
                                            'top': 0,
                                            'left': 0,  
                                            'width' : store.width,
                                            'height': store.height,
                                            'opacity' : 1
                                        });
                                        store = angular.copy(origin);
                                        progressBar.empty().hide();                           
                                    });

                                }
                            });
                        };
                    }  
                    
                    var handleFileSelect = function(fileList) {

                        if(fileList.length < 1) {
                            return; // no image selected
                        }
                        var file = fileList[0];
                        if(file.size > maxImageSize) {
                            alert('Image size too big');
                            return;
                        }
                        if(!file.type.match('image.*')) {
                            alert("File is not an image");
                            return;
                        }
                        var reader = new FileReader();

                        reader.onloadend = (function(theFile) {
                            return function(e) {                                
                                scope.$apply(function(){
                                    $parse(rawImage).assign(scope,  e.target.result);
                                    $parse(showImage).assign(scope,  e.target.result);
                                    renderFirstTime();
                                });                                
                            };
                        })(file);                            
                        reader.readAsDataURL(file);
                    };  
                    
                    var Init = function(){
                         // reset the raw && show image  
                    if (scope.$eval(tAttrs.imageUpload)) {
                        $parse(showImage).assign(scope, scope.$eval(tAttrs.imageUpload));                       
                    } else {
                       if (scope.$eval(tAttrs.defaultImage)) {
                          $parse(showImage).assign(scope, scope.$eval(tAttrs.defaultImage)); 
                       } else {
                           $parse(showImage).assign(scope, '');
                       }
                       
                    }
                    setHeight();
                    if (scope.$eval(showImage)) {
                        var image = new Image();
                        image.src = scope.$eval(showImage);
                        image.onload = function(){
                            store.image.w = store.image.imageW = image.width;
                            store.image.h = store.image.imageH = image.height;                            
                            image = null;
                            ApplyCssToImageBaseOnData(true, true, true);
                        }                        
                    }
                    }
                    
                    
                    scope.$watch('incentiveAdd',function(newValue) {
                        if(newValue == 1) {  
                            Init();
                         }
                        }, true);
                    
                    Init();
                   
                    iElement.find('div.image-remove').click(function(event) { 
                        event.stopPropagation();     
                        scope.$apply(function(){
                            if (scope.$eval(tAttrs.defaultImage)) {                                
                                $parse(iAttrs.imageUpload).assign(scope, scope.$eval(tAttrs.defaultImage));
                                $parse(showImage).assign(scope, scope.$eval(tAttrs.defaultImage));
                            } else {
                                $parse(iAttrs.imageUpload).assign(scope, '');
                                $parse(showImage).assign(scope, '');
                            }
                            
                            $parse(rawImage).assign(scope, undefined); 
                            slider.empty().hide();
                            saveButton.empty().hide();
                            progressBar.empty().hide();
                            setHeight(); 
                            if (scope.$eval(tAttrs.defaultImage)) {
                                var image = new Image();
                                image.src = scope.$eval(tAttrs.defaultImage);
                                image.onload = function(){
                                    store.image.w = store.image.imageW = image.width;
                                    store.image.h = store.image.imageH = image.height;
                                    image = null;
                                    ApplyCssToImageBaseOnData(true, true, true);
                                }                        
                            }
                        });
                    });
                     
                    iElement.on('click', '.save', function(event) {
                        event.stopPropagation();
                        saveImage();
                    });
                    
                    input.change(function(event) {
                        handleFileSelect(event.target.files);
                    });

                    preview.on('dragover', function(event){
                        event.stopPropagation();
                        event.preventDefault();
                        event.originalEvent.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.                            
                    });

                    preview.on('dragenter', function(event){
                        event.stopPropagation();
                        event.preventDefault();
                        preview.addClass('dragover');
                    });

                    preview.on('dragleave', function(event){
                        event.stopPropagation();
                        event.preventDefault();
                        preview.removeClass('dragover');
                    });

                    preview.on('drop', function(event){
                        preview.removeClass('dragover');
                        event.stopPropagation();
                        event.preventDefault();
                        handleFileSelect(event.originalEvent.dataTransfer.files);
                    });

                    preview.click(function(event) {
                        input.click();
                    });

                };   
            } else {
                tElement.html('Browser not supportet');
            }
        }
    };
}).
directive('validUrl', function(){
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            function validator(value) {
                if (value) {
                    // remove http:// or https:// before display it
                    if (value.indexOf('http://') == 0) {
                        value = value.trim().substring(7);
                    } else if (value.indexOf('https://') == 0) {
                        value = value.trim().substring(8);
                    }
                }                
                var validUrl = true;
                if (value) {
                    var valueCheck = value;
                    if (valueCheck.indexOf('www.') == 0) {
                        valueCheck = valueCheck.substring(4);
                    }
                    if(valueCheck) {
                        validUrl = valueCheck.match("^[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
                    } else {
                        validUrl = false;
                    }
                }             
                ctrl.$setValidity('url', validUrl);                
                return value;                
            };

            ctrl.$parsers.unshift(function(viewValue){
                return validator(viewValue);
            });
            ctrl.$formatters.unshift(function(modelValue){                
                return validator(modelValue);
            });
            ctrl.$parsers.push(function(valueFromInput) {
                if (valueFromInput && (valueFromInput.indexOf('http://') < 0) && (valueFromInput.indexOf('https://') < 0)) {
                    return 'http://' + valueFromInput;
                } else {
                    return valueFromInput;
                }
            });
        }
    };
});

/**
* Services
*/

angular.module('site.services', []).
config(function($httpProvider) {
  $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}).
factory('Auth',function ($http) {    
    return {
        setCredentials: function (username, password) {
            var keyStr = 'ABCDEFGHIJKLMNOP' +
            'QRSTUVWXYZabcdef' +
            'ghijklmnopqrstuv' +
            'wxyz0123456789+/' +
            '=';
            var input = username + ':' + password;
            var output = "";
            var chr1, chr2, chr3 = "";
            var enc1, enc2, enc3, enc4 = "";
            var i = 0;

            do {
                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                keyStr.charAt(enc1) +
                keyStr.charAt(enc2) +
                keyStr.charAt(enc3) +
                keyStr.charAt(enc4);
                chr1 = chr2 = chr3 = "";
                enc1 = enc2 = enc3 = enc4 = "";
            } while (i < input.length);


            $http.defaults.headers.common.Authorization = 'Basic ' + output;
        },
        clearCredentials: function () {
            document.execCommand("ClearAuthenticationCache");            
            $http.defaults.headers.common.Authorization = 'Basic ';
        }
    };
}).
factory('fdbModel', function($http, $q) {
    return function(modelName) {
        //var orModelName = modelName;
        modelName = modelName.toUnderscore();
        var uri = Config.baseUrl + '/proxy/';
        
        if(modelName === 'category') {
            uri += 'categories';
        } else if(modelName === 'property') {
            uri += 'properties';
        } else if (modelName === 'invoice') {
            uri += 'invoice';
        } else {
            uri += modelName + 's';
        }
        return {
            getUri: function() {
                return uri;
            },
            query: function(params) {                          
                return $http({method: 'GET', url: uri + '.json', params: params}).then(function(response){
                    return response['data'];
                    }, function(response) {
                        return $q.reject(response);
                });
            },
            get: function(id) {
                return $http({method: 'GET', url: uri + '/' + id + '.json'}).then(function(response){
                    return response['data'][modelName];
                    }, function(response) {
                        return $q.reject(response);
                });
            },
            add: function(data) {
                return $http({method: 'POST', url: uri + '.json', data: data}).then(function(response){
                    return response['data'][modelName];
                    }, function(response) {
                        return $q.reject(response);
                });
            },
            edit: function(id, data) {
                return $http({method: 'POST', url: uri + '/' + id + '.json', data: data}).then(function(response){
                    return response['data'][modelName] ? response['data'][modelName] : response;
                    }, function(response) {
                        return $q.reject(response);
                });
            },
            remove: function(id) {
                return $http({method: 'POST', url: uri + '/delete/' + id + '.json'}).then(function(response){
                    return response['data'];
                    }, function(response) {
                        return $q.reject(response);
                });                    
            }
        }; 
    };
}).
factory('showFormMessage', function() {
    return {
        error : function (errorValidation, objMessages, messageDivId) {
            var  validationMessage = '<ul>';
            for (var i = 0 ; i < objMessages.length ; i ++) {
                if (typeof errorValidation[objMessages[i].field] != 'undefined' && errorValidation[objMessages[i].field].$error[objMessages[i].condition]) {                    
                    validationMessage += '<li>'+objMessages[i].content+ '</li>';
                }
            }
            validationMessage += '</ul>';            
            $(messageDivId).show().removeClass('error').removeClass('success').removeClass('message').addClass('error').addClass('message').html(validationMessage);
        },   
        success : function (message, messageDivId) {
            $(messageDivId).show().removeClass('error').removeClass('success').addClass('success').html(message);
        },
        notification : function (message, messageDivId) {
            $(messageDivId).show().removeClass('error').removeClass('success').html(message);
        },
        fail : function (message, messageDivId) {
            $(messageDivId).show().removeClass('error').removeClass('success').addClass('error').html(message);
        },
        clear : function (messageDivId) {
            $(messageDivId).html('').hide();
        }
    };
});