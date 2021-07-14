<?php
$url = TSInit::$app->getVar('url');
$urlWithoutProtocol = TSInit::$app->getVar('urlWithoutProtocol');
$redirectUrl = TSInit::$app->getVar('completeRedirectUrl') ?: '/';
$homeUrl = TSInit::$app->getVar('homeUrl') ?: '/';
?>

<iframe
    src="<?= $url ?>"
    id="questionnaire-container"
    frameborder="0"
    style="position:fixed;top:0px;left:0px;width:100%;height:100%;z-index:100;"
></iframe>
<style>
    /* Fix for adaptive design */
    header .top, .login-block {
        z-index: 90 !important;
    }
    @media (max-width: 1024px) {
        html, body {
            height: 100vh !important;
            -webkit-overflow-scrolling : touch !important;
            overflow: hidden !important;
        }
    }
</style>
<script>
    // only global variable. Need for trigger popups
    var GLOBAL = GLOBAL || {};
    GLOBAL.pageInfo = {
        id: 'survey',
        submitFailed: 0
    };

    ;(function () {
        "use strict";

        window.Questionnaire = api;

        var errors = {
            notIframe: "Selector is not an Iframe element. Emit methods will not work",
            notFound: "Iframe selector not found. Emit methods will not work",
            notOrigin: "Origin source url is empty. Messages will not work"
        };

        function api (selector, origin, debug) {
            if (!origin && debug) console.warn(errors.notOrigin);
            var general = {
                name: "Questionnaire",

                $iframe: (function () {
                    if (typeIs(selector, "string")) {
                        const $frame = document.querySelector(selector);
                        if ($frame) {
                            if (typeIs($frame, "htmliframeelement")) {
                                return $frame;
                            } else {
                                debug && console.warn(errors.notIframe);
                            }
                        } else {
                            debug && console.warn(errors.notFound);
                        }
                    } else if (typeIs(selector, "htmliframeelement")) {
                        return selector;
                    } else {
                        debug && console.warn(errors.notFound);
                    }
                })(),

                stack: [],

                set: function (data) {
                    if (!origin) return;
                    if (!typeIs(data, "object")) data = {};
                    data.entity = general.name;
                    const payload = JSON.stringify(data);
                    ((general.$iframe && general.$iframe.contentWindow) || window.parent).postMessage(payload, origin);
                },

                get: function (method, callback) {
                    if (!callback) return;
                    this.stack.push({
                        method: method,
                        callback: callback
                    });
                }
            };

            var api = {
                $iframe: general.$iframe,
                emit: emit,
                on: on
            };

            window.addEventListener("message", function (event) {
                if (!event.data) return;
                try {
                    var data = JSON.parse(event.data);
                    if (typeIs(data, "object") && data.entity === general.name) {
                        for (var i = 0; i < general.stack.length; i++) {
                            if (data.action === general.stack[i].method && typeIs(general.stack[i].callback, "function")) {
                                general.stack[i].callback.call(api, data.payload);
                            }
                        }
                    }
                } catch (error) {}
            });

            function emit (eventName, payload) {
                if (typeIs(eventName, "string")) {
                    general.set({
                        action: eventName,
                        payload: payload
                    });
                } else if (typeIs(eventName, "object")) {
                    for (var name in eventName) {
                        general.set({
                            action: name,
                            payload: eventName[name]
                        });
                    }
                }

                return api;
            }

            function on (eventName, callback) {
                if (typeIs(eventName, "string")) {
                    general.get(eventName, callback);
                } else if (typeIs(eventName, "object")) {
                    for (var name in eventName) {
                        general.get(name, eventName[name]);
                    }
                }

                return api;
            }

            return api;
        }

        function typeIs (entity, types) {
            var type = Object.prototype.toString
                .call(entity)
                .replace(/^\[object (.+)\]$/, "$1")
                .toLowerCase();
            return Array.isArray(types) ? types.indexOf(type) !== 1 : types === type;
        }
    })();

    /** USE */
    ;(function () {
        "use strict";

        initClient(window.location.protocol + "//" + "<?= $urlWithoutProtocol ?>", "questionnaire-container", function (questionnaire) {
            questionnaire.on({
                close: function () {
                    questionnaire.emit("preloader", true);
                    window.location.assign("/");
                },

                finish: function (payload) {
                    var data = payload.closeData;
                    if (!data) return false;
                    if (data.hasOwnProperty("action") && data.action === "redirect") {
                        questionnaire.emit("preloader", true);
                        window.location.href = _getRedirectUrl(data);
                    }
                }
            });
        });

        function initClient (url, id, callback, debug) {
            if (debug === undefined) debug = true;

            var questionnaire = window.Questionnaire("#" + id, url, debug);

            questionnaire.$iframe.addEventListener("load", function () {
                callback && callback(questionnaire);
                questionnaire.emit("origin", window.location.origin);
            });
        }

        /**
         * Getting redirect url from response data
         *
         * @param {object} data
         * @returns {string}
         * @private
         */
        function _getRedirectUrl(data) {
            var redirectUrl = '/trader/updateTraderInfo/?returnUrl=';

            data.url = data.url || 'default';
            switch (data.url) {
                case 'default':
                    return redirectUrl + "<?= $redirectUrl ?>";

                case 'home':
                    return redirectUrl + "<?= $homeUrl ?>";

                default:
                    return redirectUrl + data.url;
            }
        }
    })();
</script>