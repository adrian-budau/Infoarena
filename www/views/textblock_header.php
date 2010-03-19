<?php
// Show wiki operations.
// Only show operations the current user can do.

// Check view parameters.
require_once(IA_ROOT_DIR . "common/textblock.php");
log_assert_valid(textblock_validate($textblock));
?>
<div id="wikiOps">
    <ul>
<?php if (($task_id = textblock_security_is_task($textblock['security'])) &&
          identity_can('task-edit', $task = task_get($task_id))) { ?>
<li><?= format_link_access(url_task_edit($task['id']), 'Editează', 'e') ?></li>
<?php } elseif(identity_can('textblock-edit', $textblock)) { ?>
<li><?= format_link_access(url_textblock_edit($textblock['name']), 'Editează', 'e') ?></li>
<?php } ?>
<?php if (($round_id = textblock_security_is_round($textblock['security'])) && identity_can('round-edit', $round = round_get($round_id))) { ?>
<li><?= format_link_access(url_round_edit($round['id']), 'Editează parametri', 'p') ?></li>
<?php } ?>
<?php if (identity_can('textblock-history', $textblock)) { ?>
<li><?= format_link_access(url_textblock_history($textblock['name']), 'Istoria', 'i') ?></li>
<?php } ?>
<?php if (identity_can('textblock-move', $textblock)) { ?>
<li><?= format_link_access(url_textblock_move($textblock['name']), 'Mută', 'u') ?></li>
<?php } ?>
<?php if (identity_can('textblock-copy', $textblock)) { ?>
<li><?= format_link_access(url_textblock_copy($textblock['name']), 'Copiază', 'c') ?></li>
<?php } ?>
<?php if (identity_can('textblock-delete', $textblock)) { ?>
<li><?= format_post_link(
    url_textblock_delete($textblock['name']),
    'Şterge', array(), true, array('onclick' =>
    "return confirm('Aceasta actiune este ireversibila! Doresti sa continui?')"),
    'r') ?>
</li>
<?php } ?>
<?php if (identity_can('textblock-attach', $textblock)) { ?>
<li><?= format_link_access(url_attachment_new($textblock['name']), 'Ataşează', 'a') ?></li>
<?php } ?>
<?php if (identity_can('textblock-list-attach', $textblock)) { ?>
<li><?= format_link_access(url_attachment_list($textblock['name']), 'Listează ataşamente', 'l') ?></li>
<?php } ?>
     </ul>
</div>
