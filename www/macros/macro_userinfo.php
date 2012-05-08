<?php

/*
 * Copyright 2012 Infoarena
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once(IA_ROOT_DIR."common/db/user.php");
require_once(IA_ROOT_DIR."www/format/format.php");

// Displays user information.
// Includes avatar, etc.
//
// Args:
//      user(required): user id.
//      info(required): parameter.
function macro_userinfo($args) {
    $user_id = getattr($args, 'user', '');
    if ($user_id === '') {
        return macro_error("User parameter required.");
    }

    $info = getattr($args, 'info', '');
    if ($info === '') {
        return macro_error("Info parameter required");
    }

    $user = user_get_by_username($user_id);
    if (!$user) {
        return macro_error("No such username: ".$user_id);
    }

    switch ($info) {
        case 'email':
            // FIXME: display e-mail only for admins
            return macro_error("Adresa de email este ascunsa");
        case 'fullname':
            return html_escape($user['full_name']);
        case 'username':
            return html_escape($user['username']);
        case 'security':
            switch ($user['security_level']) {
            case 'admin':
                return 'Administrator';
            case 'normal':
                return 'Utilizator normal';
            default:
                return html_escape(ucfirst($user['security_level']));
            }
        case 'rating':
            if ($user['rating_cache']) {
                return html_escape(rating_scale($user['rating_cache']));
            } else {
                return 'n/a';
            }
        default:
            return macro_error("Invalid info paramater");
    }
}
