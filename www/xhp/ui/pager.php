<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/link.php');
require_once(IA_ROOT_DIR . 'www/url.php');

class :ui:pager extends :ui:element {
    attribute
        string prefix = "";
}

class :ui:pager:page-number extends :ui:pager {
    attribute
        int first_entry = 0,
        int display_entries = 50,
        int total_entries = 0,
        int surround_pages = 5,
        bool show_count = false,
        bool show_display_entries = false;

    protected function listPages($display_entries, $begin, $end) {
        $list = <x:frag />;
        for ($i = $begin; $i <= $end; ++$i) {
            $child = <ui:link:pager first_entry={($i - 1) * $display_entries} display_entries={$display_entries} />;
            $this -> sendAttributes($child, 'prefix');
            $list -> appendChild($child);
        }
        return $list;
    }

    protected function render() {
        $first_entry = $this -> getAttribute('first_entry');
        $display_entries = $this -> getAttribute('display_entries');
        $total_entries = $this -> getAttribute('total_entries');
        $surround_pages = $this -> getAttribute('surround_pages');
        $show_count = $this -> getAttributes('show_count');
        $show_display_entries = $this -> getAttribute('show_display_entries');

        // Setting the current page
        $pager = <x:frag>
                   Vezi pagina:
                 </x:frag>;

        $current_page = (int)($first_entry / $display_entries) + 1;
        $total_pages = (int)(($total_entries - 1) / $display_entries) + 1;

        // We could put a number instead of the "..."
        if ($current_page <= 2 * $surround_pages + 1) {
            $pager -> appendChild($this -> listPages($display_entries, 1, $current_page - 1));
        } else {
            $pager -> appendChild($this -> listPages($display_entries, 1, $surround_pages))
                   -> appendChild(<x:frag> ... </x:frag>)
                   -> appendChild($this -> listPages($display_entries, $current_page - $surround_pages, $current_page));
        }

        $pager -> appendChild(<span class="selected">
                                <strong>
                                  {$current_page}
                                </strong>
                              </span>);

        if ($total_pages - $current_page <= 2 * $surround_pages) {
            $pager -> appendChild($this -> listPages($display_entries, $current_page + 1, $total_pages));
        } else {
            $pager -> appendChild($this -> listPages($display_entries, $current_page + 1, $current_page + $surround_pages))
                   -> appendChild(<x:frag> ... </x:frag>)
                   -> appendChild($this -> listPages($display_entries, $total_pages - $surround_pages, $total_pages));
        }

        if ($show_count == true) {
            $pager -> appendChild(<span class="count">
                                    ({$total_entries} {$total_entries == 1 ? ' rezultat' : ' rezultate'})
                                  </span>);
        }

        if ($show_display_entries == true) {
            $display_entries_options = array(25, 50, 100, 250, $display_entries);
            sort($display_entries_options);
            $display_entries_options = array_unique($display_entries_options);

            $first = true;
            $options = <x:frag />;
            foreach ($display_entries_options as $option) {
                if ($first == false) {
                   $options -> appendChild(<x:frag> | </x:frag>);
                }
                $first = false;

                if ($option == $display_entries) {
                    $option =
                      <span class="selected">
                        <strong>
                          {$option}
                        </strong>
                      </span>;
                } else {
                    $option = <ui:link:pager display_entries={$option}>
                                {$option}
                              </ui:link:pager>;
                    $this -> sendAttributes($option, array('first_entry', 'prefix'));
                }

                $options -> appendChild($option);
            }

            $pager -> appendChild(<span class="entries-per-page"> ({$options})</span>);
        }
        return
          <div class="pager">
            <div class="standard-pager">
              {$pager}
            </div>
          </div>;
    }
}
