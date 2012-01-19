<?php

require_once(IA_ROOT_DIR . 'www/format/pager.php');
require_once(IA_ROOT_DIR . 'www/format/format.php');
require_once(IA_ROOT_DIR . 'common/db/score.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/user.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/table.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/pager.php');

// Displays *interactive* rankings table summing up score points from a
// pre-defined set of contest rounds.
//
// Arguments:
//     rounds   (required) a | (pipe) separated list of round_id : round_name.
//              Round name is the name which will appear in the column dedicated
//              to that round in case detail_round == true
//              If detail_round == false you can leave just the round_id (see examples)
//     count    (optional) how many to display at once, defaults to infinity
//     detail_task   (optional) true/false print score columns for each task
//     detail_round  (optional) true/false  print score columns for each round
// Examples:
//      Macro: Rankings(rounds="preONI2007/1/a | preONI2007/2/a" count = "10")
//      Columns: | pos | name | score |
//
//      Rankings(rounds="preONI2007/1/a : round 1 | preONI2007/2/a : round 2" detail_round = "true")
//      Columns: | pos | name | round 1 | round 2 | total |
//
//      Rankings(rounds="preONI2007/1/a : round 1 | preONI2007/2/a : round 2" detail_round = "true" detail_task = "true")
//      Columns: | pos | name | task | task | task | round 1 | task | task | task | round 2 | total |
function macro_rankings($args) {
    $args['param_prefix'] = 'rankings_';
    if (isset($args['count'])) {
        $args['display_entries'] = $args['count'];
    }

    // Detail parameters
    $detail_round = getattr($args, 'detail_round', 'false');
    $detail_task = getattr($args, 'detail_task', 'false');

    $detail_round = ($detail_round == 'true');
    $detail_task = ($detail_task == 'true');

    // Paginator options
    $options = pager_init_options($args);

    // Rounds parameters
    $roundstr = getattr($args, 'rounds', '');
    if ($roundstr == '') {
        return macro_error("Parameters 'rounds' is required.");
    }
    $round_param = preg_split('/\s*\|\s*/', $roundstr);
    $rounds = array();
    foreach ($round_param as $param) {
        $round = preg_split('/\s*\:\s*/', $param);
        if (!identity_can('round-view-scores', round_get($round[0]))) {
            continue;
        }
        array_push($rounds, array(
            'round_id' => $round[0],
            'round_name' => getattr($round, 1, ' ')
        ));
    }

    // Generating Table

    $header =
      <x:frag>
        <th class="number rank">
          {'Pozitie'}
        </th>
        Nume
      </x:frag>;

    $columns = array();
    $tasks = array();
    if ($detail_round == true || $detail_task == true) {
        foreach ($rounds as $round) {
            $round_id = $round['round_id'];

            if ($detail_task == true) {
                $new_tasks = round_get_tasks($round_id);
                foreach ($new_tasks as $task) {
                    $header -> appendChild(<th class="number score">
                                          {$task['title']}
                                        </th>);
                    $columns[] = 'task_scores_' . $task['id'];
                    array_push($tasks, $task['id']);
                }
            }

            if ($detail_round == true) {
                $header -> appendChild(<th class="number score">
                                      {$round['round_name']}
                                    </th>);
                $columns[] = 'round_scores_' . $round['round_id'];
            }
        }
    }

    $total = 'Scor';
    if ($detail_round == true || $detail_task == true) {
        $total = 'Total';
    }
    $header -> appendChild(<th class="number score">
                             {$total}
                           </th>);

    $content = <x:frag />;

    $round_ids = array();
    foreach ($rounds as $round) {
        array_push($round_ids, $round['round_id']);
    }
    $rankings = score_get_rankings($round_ids, $tasks, $options['first_entry'], $options['display_entries'], $detail_task, $detail_round);
    foreach ($rankings as $user) {
        $content -> appendChild(<x:frag>
                                  <td class="number rank">
                                    {$user['ranking']}
                                  </td>
                                  <ui:user:normal user={$user} />
                                </x:frag>);
        foreach ($columns as $column) {
            $content -> appendChild(<td class="number score">
                                      {(int)$user[$column]}
                                    </td>);
        }
        $content -> appendChild(<td class="number score">
                                {(int)$user['score']}
                              </td>);
    }

    $options['total_entries'] = score_get_count(null, null, $round_ids);

    if (0 >= count($rankings)) {
        return macro_message('Nici un rezultat inregistrat pentru aceasta runda.');
    } else {
        $pager =
          <ui:pager:page-number first_entry={$options['first_entry']} display_entries={$options['display_entries']}
            total_entries={$options['total_entries']} show_count={true} show_display_entries={true}
            prefix="rankings_"/>;

        return
          <x:frag>
            {$pager}
            <ui:table sortable={true} cols={count($columns) + 3} >
              {$header}
              {$content}
            </ui:table>
            {$pager}
          </x:frag>;
    }
}

?>
