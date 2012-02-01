<?php

require_once(IA_ROOT_DIR . 'common/db/db.php');
require_once(IA_ROOT_DIR . 'common/external_libs/recaptchalib.php');

/**
 * Get the current tokens
 * If we already asked for it do not do an extra SQL request
 * FIXME: should we find a different method rather than global variables
 * to hold the value?
 *
 * @param string $identifier
 * @return int
 */
function get_tokens($identifier = null) {
    if ($identifier == null) {
        $identifier = remote_ip_info();
    }
    global $tokens;
    if (isset($tokens[$identifier])) {
        return $tokens[$identifier][0];
    }

    $query = sprintf("SELECT tokens FROM ia_tokens WHERE `identifier` = '%s' "
            . "AND timestamp > '%s'", db_escape($identifier),
            db_escape(db_date_format(time() - IA_TOKENS_REGEN)));
    $tokens[$identifier][0] = $tokens[$identifier][1] =
            db_query_value($query, IA_TOKENS_MAX);
    return $tokens[$identifier][0];
}

/**
 * Remove tokens
 * Returns true on succes or false if not enough tokens
 * 0 tokens are never enough
 * if not enough tokens pay what we have
 *
 * @param int $price
 * @param string $identifier
 * @return bool
 */
function pay_tokens($price, $identifier = null) {
    if ($identifier == null) {
        $identifier = remote_ip_info();
    }
    global $tokens;
    // The first element tells us the current tokens and the second the initial
    // ones
    if (!isset($tokens[$identifier])) {
        get_tokens($identifier);
    }

    if ($tokens[$identifier][0] < $price) {
        $tokens[$identifier][0] = 0;
        return false;
    }

    $tokens[$identifier][0] -= $price;
    return true;
}

/**
 * Updates the current identifier's tokens only if modifications were made to
 * lower the amount of requests on database
 *
 * @param string $identifier
 * @param int $amount   optional parameter in case we want something different
 */
function save_tokens($identifier = null, $amount = null) {
    if ($identifier == null) {
        $identifier = remote_ip_info();
    }
    global $tokens;
    if ($amount == null) {
        if (!isset($tokens[$identifier]) ||
                $tokens[$identifier][0] == $tokens[$identifier][1]) {
            return;
        }

        $amount = min($tokens[$identifier][0], IA_TOKENS_MAX);
    }

    $query = sprintf("REPLACE INTO `ia_tokens` VALUES('%s', %d, '%s')",
            db_escape($identifier), db_escape($amount),
            db_escape(db_date_format()));
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
 * @param int $amount
 * @param string $identifier
 * @return string
 */
function check_captcha_for_tokens($amount = IA_TOKENS_CAPTCHA, $required = false, $identifier = null) {
    if ($identifier == null) {
        $identifier = remote_ip_info();
    }
    if (IA_DEVELOPMENT_MODE) {
        pay_tokens(-IA_TOKENS_MAX, $identifier);
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
        pay_tokens(-$amount, $identifier);
        return '';
    }
    return 'Confirmati ca sunteti om';
}

?>
