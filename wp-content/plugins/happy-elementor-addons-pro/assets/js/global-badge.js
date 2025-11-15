"use strict";

(function ($, w) {
  "use strict";

  var $window = $(w);

  // replacement of waypointjs
  // function observeTarget(target, callback) {
  // 	const observer = new IntersectionObserver((entries, observer) => {
  // 		entries.forEach(entry => {
  // 			if (entry.isIntersecting) {
  // 				callback(entry);
  // 			}
  // 		});
  // 	});

  // 	observer.observe(target);
  // }

  if ('undefined' == typeof window.paCheckSafari) {
    var checkSafariBrowser = function checkSafariBrowser() {
      var iOS = /iP(hone|ad|od)/i.test(navigator.userAgent) && !window.MSStream;
      if (iOS) {
        var allowedBrowser = /(Chrome|CriOS|OPiOS|FxiOS)/.test(navigator.userAgent);
        if (!allowedBrowser) {
          var isFireFox = '' === navigator.vendor;
          allowedBrowser = allowedBrowser || isFireFox;
        }
        var isSafari = /WebKit/i.test(navigator.userAgent) && !allowedBrowser;
      } else {
        var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
      }
      if (isSafari) {
        return true;
      }
      return false;
    };
    window.paCheckSafari = checkSafariBrowser();
  }
  $window.on("elementor/frontend/init", function () {
    var HappyGlobalBadge = function HappyGlobalBadge($scope, $) {
      if (!$scope.hasClass('ha-gb-yes')) {
        return;
      }
      var elemType = $scope.data('element_type'),
        id = $scope.data("id"),
        settings = {};
      generateSettings(elemType, id);
      if (!settings) {
        return;
      }
      haObserveTarget($scope[0], function () {
        generateGlobalbadge();
      });
      function generateSettings(type, id) {
        var editMode = elementorFrontend.isEditMode(),
          badgeSettings = {},
          tempTarget = $scope.find('#ha-gbadge-data-wrap-' + id),
          tempTarget2 = $scope.find('#ha-gbadge-data-wrap-temp-' + id);
        var tempExist = 0 !== tempTarget.length || 0 !== tempTarget2.length,
          editMode = elementorFrontend.isEditMode() && tempExist;
        if (editMode && tempExist) {
          badgeSettings = tempTarget.data('gbadge');
          if ('widget' === type && !badgeSettings) {
            badgeSettings = tempTarget2.data('gbadge');
          }
        } else {
          badgeSettings = $scope.data('gbadge');
        }
        if (!badgeSettings) {
          settings = false;
          return;
        }
        settings.text = badgeSettings.text;
        settings.icon = badgeSettings.icon;
        if (badgeSettings.icon) {
          settings.iconType = badgeSettings.iconType;
        }
        if (badgeSettings.svgLayer) {
          settings.svgLayer = badgeSettings.svgLayer;
        }
        if (badgeSettings.floating) {
          settings.floating = badgeSettings.floating;
        }
        if (0 !== Object.keys(settings).length) {
          return settings;
        }
      }
      function generateGlobalbadge() {
        var uniqueClass = 'ha-gb-wrap-' + id,
          badgeHtml = '<div class="ha-gb-wrap ' + uniqueClass + '">' + getbadgeHtml(settings) + '</div>';
        $scope.find("." + uniqueClass).remove();
        $scope.prepend(badgeHtml);
        if (settings.icon) {
          if ('icon' === settings.iconType && 'svg' === settings.icon.library) {
            handleSvgIcon(settings.icon.value.url, id);
          }
          if ('lottie' === settings.iconType) {
            var $item = $scope.find('.ha-gb-lottie-animation');
            var loop = $item.data("lottie-loop"),
              reverse = $item.data("lottie-reverse");
            var animItem = lottie.loadAnimation({
              container: $item[0],
              // loop: true,
              loop: loop ? true : false,
              path: $item.data("lottie-url"),
              autoplay: true
            });
          }
        }
        if (settings.floating) {
          if ($scope.hasClass("ha-gb-disable-on-safari-yes")) {
            if (window.paCheckSafari) return;
          }
          var animeTarget = !settings.svgLayer ? uniqueClass : uniqueClass + ' , .ha-gb-svg-' + id;
          applyFloatingEffects(settings.floating, animeTarget);
        }
      }
      function getbadgeHtml(settings) {
        var badgeHtml = '<div class="ha-gb-inner">';
        if (settings.text) {
          badgeHtml += '<span class="ha-gb-text">' + escapeHtml(settings.text) + '</span>';
        }
        if (settings.icon) {
          badgeHtml += '<span class="ha-gb-icon">';
          if ('icon' === settings.iconType) {
            if ('svg' !== settings.icon.library) {
              badgeHtml += '<i class=" ha-gb-icon-fa ' + settings.icon.value + '"></i>';
            }
          } else if ('image' === settings.iconType) {
            badgeHtml += '<img class="ha-gb-img" src="' + settings.icon.url + '" alt="' + settings.icon.alt + '">';
          } else if ('lottie' === settings.iconType) {
            badgeHtml += '<div class="ha-gb-lottie-animation ha-gb-lottie-icon" data-lottie-url="' + settings.icon.url + '" data-lottie-loop="' + settings.icon.loop + '" data-lottie-reverse="' + settings.icon.reverse + '" ></div>';
          }
          badgeHtml += '</span>';
        }
        return badgeHtml + '</div>';
      }
      function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        // .replace(/"/g, "&quot;")
        // .replace(/'/g, "&#039;");
      }
      function handleSvgIcon(url, id) {
        var parser = new DOMParser();
        fetch(url).then(function (response) {
          if (200 !== response.status) {
            console.log('Looks like there was a problem loading your svg. Status Code: ' + response.status);
            return;
          }
          response.text().then(function (text) {
            var parsed = parser.parseFromString(text, 'text/html'),
              svg = parsed.querySelector('svg');
            $(svg).attr('class', 'ha-gb-icon-svg');
            $scope.find('.ha-gb-wrap-' + id + ' .ha-gb-icon').html($(parsed).find('svg'));
          });
        });
      }
      function applyFloatingEffects(effects, target) {
        var animeSettings = {
          targets: '.' + target,
          loop: true,
          direction: 'alternate',
          easing: 'easeInOutSine'
        };
        if (effects.translate) {
          var data = effects.translate,
            x_translate = {
              value: [data.x_param_from || 0, data.x_param_to || 0],
              duration: data.speed
            },
            y_translate = {
              value: [data.y_param_from || 0, data.y_param_to || 0],
              duration: data.speed
            };
          animeSettings.translateX = x_translate;
          animeSettings.translateY = y_translate;
        }
        if (effects.rotate) {
          var data = effects.rotate,
            x_rotate = {
              duration: data.speed,
              value: [data.x_param_from || 0, data.x_param_to || 0]
            },
            y_rotate = {
              duration: data.speed,
              value: [data.y_param_from || 0, data.y_param_to || 0]
            },
            z_rotate = {
              duration: data.speed,
              value: [data.z_param_from || 0, data.z_param_to || 0]
            };
          animeSettings.rotateX = x_rotate;
          animeSettings.rotateY = y_rotate;
          animeSettings.rotateZ = z_rotate;
        }
        if (effects.scale) {
          var data = effects.scale,
            x_scale = {
              duration: data.speed,
              value: [data.x_param_from || 0, data.x_param_to || 0]
            },
            y_scale = {
              duration: data.speed,
              value: [data.y_param_from || 0, data.y_param_to || 0]
            };
          animeSettings.scaleX = x_scale;
          animeSettings.scaleY = y_scale;
        }
        if (effects.opacity) {
          var data = effects.opacity;
          animeSettings.opacity = {
            value: [data.from || 0, data.to || 0],
            duration: data.speed
          };
        }
        if (effects.filters) {
          var data = effects.filters,
            filterArr = [];
          if (data.blur) {
            var blurEffect = {
              value: [data.blur.from || 0, data.blur.to || 0],
              duration: data.blur.duration,
              delay: data.blur.delay || 0
            };
            filterArr.push(blurEffect);
          }
          if (data.gscale) {
            var gscaleEffect = {
              value: [data.gscale.from || 0, data.gscale.to || 0],
              duration: data.gscale.duration,
              delay: data.gscale.delay || 0
            };
            filterArr.push(gscaleEffect);
          }
          animeSettings.filter = filterArr;
        }
        anime(animeSettings);
      }
    };
    elementorFrontend.hooks.addAction("frontend/element_ready/global", HappyGlobalBadge);
  });
})(jQuery, window);