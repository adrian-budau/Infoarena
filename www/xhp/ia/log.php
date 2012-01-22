<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

class :ia:log extends :ui:element {

    protected function render() {
        return
          <textarea id="log" rows="50" cols="80">
            {$this -> getChildren()}
          </textarea>;
    }
}
