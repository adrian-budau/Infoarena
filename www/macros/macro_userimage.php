<?php

require_once(IA_ROOT_DIR . 'www/format/format.php');
require_once(IA_ROOT_DIR . 'common/common.php');
require_once(IA_ROOT_DIR . 'common/db/user.php');
require_once(IA_ROOT_DIR . 'www/url.php');
/**
 * Returns a an image which links to it's location of a giver username
 * and size
 *
 * @param  array $args      An array containing the user for whom to get the
 *                          avatar as well as the size
 *
 * @return :ui:link
 */
function macro_userimage($args) {
    $username = $args['user'];

    $size_type = $args['size'];

    if (is_valid_size_type($size_type) == false) {
        return macro_error("Unkown size type \"".$size_type."\".");
    }

    $user = user_get_by_username($username);
    return
      <ui:link href={url_user_avatar($user)}>
        <ui:user:avatar user={$user} size={$size_type} />
      </ui:link>;
}
