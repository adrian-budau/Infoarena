<?php

require_once(IA_ROOT . "common/db/job.php");

function controller_job_detail($suburl) {
    // Get job id.
    $job_id = request("id"); 
    if (!is_whole_number($job_id)) {
        flash_error("Numar de job invalid.");
        redirect(url('monitor'));
    }

    // Get job.
    $job = job_get_by_id($job_id);
    if (!$job) {
        flash_error("Nu exista job-ul #$job_id");
        redirect(url('monitor'));
    }

    // Check security.
    $action = request('action', 'view');
    if ($action == 'view-source') {
        identity_require('job-view-source', $job);
    } else if ($action == 'view') {
        identity_require('job-view', $job);
    } else {
        flash_error("Actiune invalida");
        redirect(url('monitor'));
    }

    if ($action == 'view-source') {
        header("Content-Type: text/plain");
        $filename = "{$job['task_id']}_{$job['user_name']}.{$job['compiler_id']}";
        header("Content-Disposition: inline; filename=".urlencode($filename).";");
        header('Content-Length: ' . strlen($job['file_contents']));
        echo $job['file_contents'];
    }
    
    $view['title'] = 'Borderou de evaluare (job #'.$job_id.')';
    $view['job'] = $job;

    if (!$view['job']['eval_message']) {
        $view['job']['eval_message'] = "&nbsp";
    }
    execute_view('views/job_detail.php', $view);
}

?>
