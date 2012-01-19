<?php

// FIXME: This should be marged with macro_rankings.php

require_once(IA_ROOT_DIR . 'www/format/table.php');
require_once(IA_ROOT_DIR . 'www/format/pager.php');
require_once(IA_ROOT_DIR . 'www/format/format.php');
require_once(IA_ROOT_DIR . 'common/db/score.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/pager.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/table.php');

// Displays *interactive* rankings table displaying user *ratings*.
//
// Arguments:
//     count    (optional) how many to display at once
//
// Examples:
//      TopRated()
//      TopRated(count="10")
function macro_toprated($args) {
    $args['param_prefix'] = 'toprated_';
    $options = pager_init_options($args);
    $options['show_count'] = true;

    $rankings = get_users_by_rating_range($options['first_entry'], $options['display_entries'], true);

    $header =
      <x:frag>
        <th class="number rank">
          Loc
        </th>
        Nume
        <th class="number rating">
          Rating
        </th>
      </x:frag>;

    $content = <x:frag />;
    foreach ($rankings as $user) {
        $content -> appendChild(<x:frag>
                                  <td class="number rank">
                                    {(int)$user['position']}
                                  </td>
                                  <ui:user:normal user={$user} />
                                  <td class="number rating">
                                    {(int)rating_scale($user['rating_cache']) }
                                  </td>
                                </x:frag>);
    }
    $options['total_entries'] = get_users_by_rating_count();

    if (0 >= count($rankings)) {
        return macro_message('Nici un utilizator cu rating.');
    } else {
        $pager =
          <ui:pager:page-number first_entry={$options['first_entry']} display_entries={$options['display_entries']}
              total_entries={$options['total_entries']} show_count={true} show_display_entries={true}
              prefix="toprated_" />;

        return
          <x:frag>
            {$pager}
            <ui:table sortable={true} cols={3}>
              {$header}
              {$content}
            </ui:table>
            {$pager}
          </x:frag>;
    }
}

?>
