<?php

require_once(IA_ROOT_DIR.'www/wiki/wiki.php');
require_once(IA_ROOT_DIR.'www/macros/macro_smfcomments.php');
require_once(IA_ROOT_DIR.'www/xhp/ia/blog.php');

// site header
include('header.php');

// wiki page header (actions)
include('textblock_header.php');

echo <ia:blog:post textblock={$textblock}/>;
// blog sidebar
echo '<div class="blog-sidebar">';
wiki_include(IA_BLOG_SIDEBAR);
echo '</div>';

// revision warning
if (getattr($view, 'revision')) {
    include('revision_warning.php');
}

// blog content
echo '<div class="blog">';
echo '<div class="wiki_text_block">';
$text = wiki_process_textblock($textblock);
// FIXME:
// Why do we need to hijack title to remove links? It's obvious the ones writing blog
// posts won't put links there and even if that would happen this hijack would render
// something like <h1> &lt;a href=.....

//echo hijack_title($text, null, $textblock['title']);
echo $text;
echo '<div class="strap">';
echo '<strong>Categorii: </strong>';
foreach ($tags as $tag) {
    echo format_link(url_blog($tag['name']), $tag['name'], true).' ';
}
echo '<br/>';
echo 'Creat la '.html_escape($first_textblock['creation_timestamp']).' de '.format_user_link($first_textblock["user_name"], $first_textblock["user_fullname"]);
echo '</div>';
// blog comments
if (getattr($view, 'forum_topic')) {
    echo macro_smfcomments(array('topic_id' => $view['forum_topic'], 'display' => 'show'));
}
echo '</div>';
echo '</div>';

// site footer
include('footer.php');

?>
