<?php

require_once(IA_ROOT_DIR . 'common/db/db.php');
require_once(IA_ROOT_DIR . 'common/external_libs/recaptchalib.php');

/**
 * Get the current tokens
 * If we already asked for it do not do an extra SQL request
 * FIXME: should we find a different method rather than global variables
 * to hold the value?
 *
 * @param string $ip
 * @return int
 */
function get_tokens($ip = null) {
    if ($ip == null) {
        $ip = remote_ip_info();
    }
    global $tokens;
    if (isset($tokens[$ip])) {
        return $tokens[$ip][0];
    }

    $query = sprintf("SELECT tokens FROM ia_tokens WHERE `ip` = '%s'",
            db_escape($ip));
    $tokens[$ip][0] = $tokens[$ip][1] = db_query_value($query, IA_TOKENS_MAX);
    return $tokens[$ip][0];
}

/**
 * Remove tokens
 * Returns true on succes or false if not enough tokens
 * 0 tokens are never enough
 *
 * @param int $price
 * @param string $ip
 * @return bool
 */
function pay_tokens($price, $ip = null) {
    if ($ip == null) {
        $ip = remote_ip_info();
    }
    global $tokens;
    // The first element tells us the current tokens and the second the initial ones
    if (!isset($tokens[$ip])) {
        get_tokens($ip);
    }

    if ($tokens[$ip][0] <= $price) {
        return false;
    }

    $tokens[$ip][0] -= $price;
    return true;
}

/**
 * Updates the current ip's tokens only if modifications were made to
 * lower the amount of requests on database
 *
 * @param int $amount   optional parameter in case we want something different
 * @param string $ip
 */
function save_tokens($amount = null, $ip = null) {
    if ($ip == null) {
        $ip = remote_ip_info();
    }
    global $tokens;
    if ($amount == null) {
        if (!isset($tokens[$ip]) || $tokens[$ip][0] == $tokens[$ip][1]) {
            return;
        }

        $amount = min($tokens[$ip][0], IA_TOKENS_MAX);
    }

    $query = sprintf("REPLACE INTO `ia_tokens` VALUES('%s', %d)",
            db_escape($ip), db_escape($amount));
    db_query($query);
}

/**
 * Receives tokens on a correct recaptcha
 * It adds the maximum amount of tokens thus surpassing the limit,
 * no problem though because we don't push anything bigger than the
 * limit
 * FIXME: should these be function parameters?
 * It searches for recaptcha information in $_POST
 * FIXME: is it okay to use pay_tokens with negative parameter?
 *
 * Returns an error if a captcha is submitted
 * @param bool $required   weather we need to check for errors even if there
 *                         is no request
 * @param string $ip
 * @return string
 */
function check_captcha_for_tokens($required = false, $ip = null) {
    if ($ip == null) {
        $ip = remote_ip_info();
    }
    if (IA_DEVELOPMENT_MODE) {
        pay_tokens(-IA_TOKENS_MAX, $ip);
        return 'Confirmati ca sunteti om';
    }

    $challenge = request('recaptcha_challenge_field');
    $response = request('recaptcha_response_field');

    if (($challenge && $response) || $required == true) {
        $captcha =  recaptcha_check_answer(IA_CAPTCHA_PRIVATE_KEY,
                        $_SERVER["REMOTE_ADDR"],
                        $challenge,
                        $response);
        if (!$captcha -> is_valid) {
            if ($challenge === null && $response === null) {
                return 'Confirmati ca sunteti om';
            } else {
                return 'Cuvintele introduse de tine sunt incorecte';
            }
        }
        pay_tokens(-IA_TOKENS_MAX, $ip);
        return '';
    }
    return 'Confirmati ca sunteti om';
}

?>
