<?php

require_once(IA_ROOT_DIR . "common/db/user.php");

function controller_login() {
    if (!is_connection_secure()) {
        redirect(url_login());
    }

    global $identity_user;

    // `data` dictionary is a dictionary with data to be displayed by form view
    // when displaying the form for the first time, this is filled with
    $data = array();

    // array for the captcha error
    $form_errors = array();

    // The flash error
    $errors = '';

    // process input?
    $submit = request_is_post();

    if ($submit) {
        // Validate data here and place stuff in errors.
        $data['username'] = getattr($_POST, 'username');
        $data['password'] = getattr($_POST, 'password');
        $data['remember'] = getattr($_POST, 'remember');
        $user = user_test_password($data['username'], $data['password']);
        if (!$user) {
            $user = user_test_ia1_password($data['username'], $data['password']);
            if (!$user) {
                $errors = 'Numele de utilizator inexistent sau parola ' .
                          'incorecta. Incearca din nou.';
            }
            else {
                // update password to the SHA1 algorithm
                user_update(array('password' => $data['password'],
                                  'username' => $data['username']),
                            $user['id']);
            }
        }

        // We won't give any tokens to a wrong login attempt
        if (($form_errors['captcha'] = check_captcha_for_tokens()) == '') {
            if ($errors) {
                pay_tokens(IA_TOKENS_MAX);
            }
        };
        // obtain referer
        $referer = getattr($_SERVER, 'HTTP_REFERER', '');
        if ($referer == url_login()) {
            // we don't care about the login page
            $referer = null;
        }

        // pay tokens for loging in
        if (get_tokens() <= IA_TOKENS_LOGIN) {
            if (!$errors) {
                $errors = 'Va rugam confirmati ca sunteti om';
            }
        }

        // process
        if (!$errors) {
            // persist user to session (login)
            $remember_user = ($data['remember'] ? true : false);
            identity_start_session($user, $remember_user);

            flash('Bine ai venit!');

            // redirect
            if (isset($_SESSION['_ia_redirect'])) {
                // redirect to where identity_require() failed
                $url = $_SESSION['_ia_redirect'];
                unset($_SESSION['_ia_redirect']);
                redirect($url);
            }
            elseif ($referer) {
                // redirect to HTTP referer if set, but not to login
                redirect($_SERVER['HTTP_REFERER']);
            }
            else {
                // home, sweet home
                redirect(url_home());
            }
        }
        else {
            // wrong login, pay tokens
            pay_tokens(IA_TOKENS_LOGIN);
            // save referer so we know where to redirect when login finally
            // succeeds.
            if (!isset($_SESSION['_ia_redirect']) && $referer) {
                $_SESSION['_ia_redirect'] = $_SERVER['HTTP_REFERER'];
            }

            flash_error($errors);
        }
    }

    // always reset password before displaying web form
    $data['password'] = '';

    $view['page_name'] = "login";
    $view['title'] = "Autentificare";
    $view['form_values'] = $data;
    $view['form_errors'] = $form_errors;
    $view['topnav_select'] = 'login';
    $view['no_sidebar_login'] = true;

    if (get_tokens() <= IA_TOKENS_LOGIN) {
        $view['captcha'] = recaptcha_get_html(IA_CAPTCHA_PUBLIC_KEY, null,
                true);
    }

    execute_view_die('views/login.php', $view);
}

?>
