<?php

// Displays DIV box that loads its content client-side via XmlHttpRequest
// You may only display one single remotebox on a page at a time.
//
// Arguments
//      url (required)      *Absolute* URL to load @ client-side
//
// NOTE: This macro requires special user permissions since it poses quite
// a few security concerns.
function macro_remotebox($args, $bypass_security = false) {
    $url = getattr($args, 'url');

    if (!$bypass_security && !identity_can('macro-remotebox')) {
        return macro_permission_error();
    }

    if (!$url) {
        return macro_error('Expecting argument `url`');
    }

    $buffer = '';
    $buffer .= '<div id="remotebox">remote content</div>';
    $buffer .= '<script type="text/javascript">RemoteBox_Url="';
    $buffer .= html_escape($args['url']);
    if (array_key_exists('display', $args)) {
        $buffer .= '";RemoteBox_Display="' . html_escape($args['display']);
    }
    if (array_key_exists('max_comm', $args)) {
        $buffer .= '";RemoteBox_MaxComm="' . html_escape((int)$args['max_comm']);
    }
    $buffer .= '";</script>';

    return $buffer;
}

?>
