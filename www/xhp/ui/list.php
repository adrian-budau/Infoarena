<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

class :ui:list extends :ui:element {
    attribute
        :ul,
        enum { "unordered", "ordered"} type = "unordered";

    protected function render() {
        if ($this -> getAttribute('type') == "unordered") {
            $list = <ul />;
        } else {
            $list = <ol />;
        }

        $this -> sendAttributes($list);

        foreach ($this -> getChildren() as $child) {
            if ($child instanceof :li) {
                $list -> appendChild($child);
            } else {
                $list -> appendChild(<li>
                                   {$child}
                                 </li>);
            }
        }
        return $list;
    }
}
?>
