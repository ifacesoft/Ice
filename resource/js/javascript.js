/**
 *
 * @package Ice
 *
 */
var Ice = {
    _callbacks: {},
    _lastback: 0,

    call: function (action, data, callback, url, method) {
        $('#icePreloader').show();

        var back = this._lastback++;

        Ice._callbacks [back] = callback;

        data.call = action;
        data.back = back;

        $.ajax({
            type: method ? method : 'POST',
            url: url ? url : location.href,
            data: data,
            //crossDomain: true,
            beforeSend: function (jqXHR, settings) {
                jqXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function (result) {
                if (result.error) {
                    Ice.notify($('#iceMessages'), result.error, 5000);
                }
                if (result.success) {
                    Ice.notify($('#iceMessages'), result.success, 5000);
                }

                if (result.back) {
                    back = result.back;
                }

                var callback = Ice._callbacks[back];
                callback(result);
                $('#icePreloader').hide();
            },
            error: function (result) {
                console.error(result);
                $('#icePreloader').hide();
            },
            dataType: 'json'
        });
    },

    reRenderClosest: function ($element, actionClassName, actionParams, callback, container) {
        Ice.call(
            actionClassName,
            actionParams,
            function (result) {
                if (result.actionName) {
                    var $block = $(result.html);

                    if (container) {
                        var parentActionName = '';

                        if (parentActionName = Ice_Helper_String.strstr(container, ':')) {
                            parentActionName = parentActionName.substr(1);
                        } else if (container = Ice_Helper_String.strstr(container, '\\')) {
                            parentActionName = parentActionName.substr(1);
                        } else {
                            parentActionName = container;
                        }

                        $element.closest('.' + parentActionName).find('.' + result.actionName).replaceWith($block);
                    } else {
                        $element.closest('.' + result.actionName).replaceWith($block);
                    }

                    if (callback != null) {
                        callback($block);
                    }
                }
            }
        );
    },

    reRender: function ($element, actionParams, callback, url, method) {
        var action = $element.attr('data-action');
        var block = $element.attr('data-block');

        Ice.call(
            action,
            actionParams,
            function (result) {
                $element.closest('.' + block).replaceWith(result.content);

                if (callback) {
                    callback();
                }

                var title = result.title
                    ? result.title
                    : document.title;

                history.pushState({}, title, url);
            },
            url,
            method
        );
    },

    notify: function ($element, body, time) {
        $element.append(body).delay(time).show(1, function () {
            $element.children().first().remove();
        });
    },

    querystringToObject: function (query) {
        var match,
            pl     = /\+/g,  // Regex for replacing addition symbol with a space
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
            //query  = window.location.search.substring(1);

        urlParams = {};
        while (match = search.exec(query)) {
            urlParams[decode(match[1])] = decode(match[2]);
        }

        console.log(urlParams);

        return urlParams;
    }
};
