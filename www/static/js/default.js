/**
 * sitewide JS
 * (c) infoarena
 */

function Page_Init() {
    // fade away flash message
    var flash = $$('.flash');
    if (flash[0] && !hasElementClass(flash[0], 'flashError')) {
        var callback = function() {
            hideElement(flash[0]);
        }
        setTimeout(callback, 13000);
    }

    // page log (used in development mode)
    var log = $('log');
    if (log) {
        // scroll down
        log.scrollTop = log.scrollHeight;

        // maximize on click
        var callback = function(event) {
            log.style.height = log.scrollHeight+'px';
            log.onclick = null;
            log.id = 'log_active';
        }
        connect(log, 'onclick', callback);
    }
}

connect(window, 'onload', Page_Init);

