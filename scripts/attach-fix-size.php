#! /usr/bin/env php
<?php

require_once(dirname($argv[0]) . "/utilities.php");
require_once(IA_ROOT_DIR."common/db/attachment.php");

db_connect();
$fixed = $errors = 0;
$query = "SELECT * FROM ia_file;";
$attachments = db_fetch_all($query);

log_print("There are ".count($attachments)." attachments in the database...");
foreach ($attachments as $attach) {
    log_print("Checking {$attach['page']}/{$attach['name']}");
    $name = attachment_get_filepath($attach);
    if (!file_exists($name)) {
        log_warn("File $name doens't exist!");
        continue;
    }
    $true_size = filesize($name);
    $db_size = $attach['size'];
    if ($db_size != $true_size) {
        log_print("Fixing {$attach['page']}/{$attach['name']} $db_size != $true_size");
        $query = sprintf("UPDATE ia_file SET size = %s WHERE id = %s",
                    $true_size, $attach['id']);
        db_query($query);
        log_assert(db_affected_rows() == 1);
        $fixed++;
    }
}

log_print("Fixed ".$fixed." attachments!");

?>
