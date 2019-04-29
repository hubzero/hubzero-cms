/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

'use strict';

var _createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true;
      if ("value" in descriptor) {
        descriptor.writable = true;
      }
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }
  return function (Constructor, protoProps, staticProps) {
    if (protoProps) {
      defineProperties(Constructor.prototype, protoProps);
    }
    if (staticProps) {
      defineProperties(Constructor, staticProps);
    }
    return Constructor;
  };
}();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Api = function () {
  function Api() {
    _classCallCheck(this, Api);
  }

  _createClass(Api, [{
    key: 'get',
    value: function get(url) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      var promise = this._makeApiRequest(url, data, 'GET');
      return promise;
    }
  }, {
    key: 'post',
    value: function post(url, data) {
      var promise = this._makeApiRequest(url, data, 'POST');
      return promise;
    }
  }, {
    key: 'delete',
    value: function _delete(url, data) {
      var promise = this._makeApiRequest(url, data, 'DELETE');
      return promise;
    }
  }, {
    key: '_makeApiRequest',
    value: function _makeApiRequest(url, data, method) {
      var baseApiUrl = '/api';
      var promise = $.ajax({
        url: '' + baseApiUrl + url,
        data: data,
        method: method
      });
      return promise;
    }
  }]);

  return Api;
}();

var HUB = HUB || {};
HUB.Api = Api;