/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
'use strict';

class User {
    constructor() {
        this.api = new Api();
    }

    isAuthenticated() {
        return this.api.get('/users/currentuser/isAuthenticated');
    }
}

var HUB = HUB || {};
HUB.User = User;
