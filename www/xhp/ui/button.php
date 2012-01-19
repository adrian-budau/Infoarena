<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

class :ui:button extends :ui:element {
    attribute
        :input,
        enum { "submit", "button" } type = "button",
        bool important = false;

    protected function render() {
        $tag = <input value={(string) <x:frag>
                                        {$this -> getChildren()}
                                      </x:frag>}
                   type={$this -> getAttribute('type')}
                   class="button" />;

        if ($this -> getAttribute('important') == true) {
            $tag -> addClass('important');
        }

        if ($this -> getAttribute('class')) {
            $classes = explode(' ', $this -> getAttribute('class'));
            foreach ($classes as $class) {
                $tag -> addClass($class);
            }
        }
        return $tag;
    }
}
