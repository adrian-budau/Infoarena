<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

// If the cols attribute is missing the table will have 1 row with all the elements
class :ui:table extends :ui:element {
    attribute
        :table,
        bool header = true,
        int cols = 0,
        bool sortable = false;

    // generates an array from all the elements
    protected function list_elements($element) {
        if (is_array($element)) {
            $elements = array();
            foreach ($element as $one) {
                $elements = array_merge($elements, $this -> list_elements($one));
            }
            return $elements;
        }

        if ($element instanceof tr || $element instanceof thead || $element instanceof tbody) {
            return $this -> list_elements($element -> getChildren());
        }

        return array($element);
    }
    protected function buildTable($list, $cols, $header = false, $parity = true) {
        $rows = count($list) / $cols;
        if (count($list) % $cols != 0) {
            if ($header == true) {
                $error = <th />;
            } else {
                $error = <td />;
            }

            $error -> setAttribute('colspan', $cols);
            $error -> addClass('error');
            $error -> appendChild(<x:frag> Elementele nu pot fi organizate intr-un tabel cu <strong> {$cols}
                                      </strong> coloane.
                                  </x:frag>);
            return
              <tr>
                {$error}
              </tr>;
         }

         $content = <x:frag />;

         for ($i = 0; $i < $rows; ++$i) {
             $row = <tr />;
             if ($parity == true) {
                if ($i % 2) {
                    $row -> addClass('even');
                } else {
                    $row -> addClass('odd');
                }
             }

             for ($j = 0; $j < $cols; ++$j) {
                 if ($list[$i * $cols + $j] instanceof :td or $list[$i * $cols + $j] instanceof :th) {
                     $row -> appendChild($list[$i * $cols + $j]);
                 } elseif ($header == false) {
                     $row -> appendChild(<td>
                                           {$list[$i * $cols + $j] }
                                        </td>);
                 } else {
                     $row -> appendChild(<th>
                                           {$list[$i * $cols + $j] }
                                         </th>);
                 }
             }
             $content -> appendChild($row);
         }

         return $content;
    }
    protected function render() {
        $cols = $this -> getAttribute('cols');
        if ($cols == 0) {
            $cols = count($this -> list_elements($this -> getChildren()));
        }

        $table = <table />;
        $this -> sendAttributes($table);

        // Making the table sortable
        if ($this -> getAttribute('sortable') == true) {
            $table -> addClass('sortable');
        }
        $children = $this -> getChildren();

        $bodybegin = 0;
        if ($this -> getAttribute('header') == true) {

            if ($children[0] instanceof thead || $children[0] instanceof tr) {
                $header = <thead>
-                           {$this -> buildTable($children[0] -> list_elements, $cols, true, false)}
                          </thead>;
                $bodybegin = 1;
            } else {
                $header = <thead>
                            {$this -> buildTable(array_slice($children, 0, $cols), $cols, true, false)}
                          </thead>;
                $bodybegin = $cols;
            }
        } else {
            $header = <x:frag />;
        }

        $content = <tbody>
                     {$this -> buildTable(array_slice($children, $bodybegin), $cols, false)}
                   </tbody>;

        // FIXME: add support for footer as it is for header
        $footer = <tfoot />;

        $table -> appendChild(array($header, $content, $footer));
        return $table;
    }
}
?>
