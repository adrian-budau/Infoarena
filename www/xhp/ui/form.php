<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

class :ui:form_field extends :ui:element {
    attribute
        bool label=true,
        enum { "integer", "string", "password", "float", "datetime", "bool",
                "enum", "set", "checkbox" } type @required,
        enum { "normal", "reversed" } order = "normal",
        string checked,
        string error,
        string description,
        string value;

    protected function render() {
        // Setting the label for the form field
        if ($this -> getAttribute('label') == true && array_key_exists(0, $this -> getChildren())) {
            $label =
              <label for={'form_' . $this -> getAttribute('name')}
                accesskey={$this -> getAttribute('access_key')}>
                <ui:highlight_accesskey accesskey ={$this -> getAttribute('access_key')}>
                  {$this -> getChildren()}
                </ui:highlight_accesskey>
              </label>;
        } else {
            $label = <x:frag />;
        }

        // Getting any extra errors
        if ($this -> getAttribute('error')) {
            $error =
              <span class="fieldError">
                {$this -> getAttribute('error')}
              </span>;
        } else {
            $error = <x:frag />;
        }

        // Setting the editor(input or select)
        $type = $this -> getAttribute('type');
        if ($type == 'integer' || $type == 'string' || $type == 'float' ||
            $type == 'datetime' || $type == 'password') {
            $editor =
              <input name={$this -> getAttribute('name')}
                  id={'form_' . $this -> getAttribute('name')}
                  value={$this -> getAttribute('value')} />;

            if ($type == 'password') {
                $editor -> setAttribute('type', 'password');
            } else {
                $editor -> setAttribute('type', 'text');
            }
        } else if($type == 'checkbox') {
            $editor =
            <input name={$this -> getAttribute('name')}
                type="checkbox"
                id={'form_' . $this -> getAttribute('name')}
                value={$this -> getAttribute('value')}
                checked={$this -> getAttribute('checked')} />;
        }

        // Any extra description
        if ($this -> getAttribute('description')) {
            $description =
              <span class = "fieldHelp">
                {$this -> getAttribute('description')}
              </span>;
        } else {
            $description = <x:frag />;
        }

        // Classes
        $editor -> setAttribute('class', $this -> getAttribute('class'));
        $label -> setAttribute('class', $this -> getAttribute('class'));

        if ($this -> getAttribute('order') == "normal") {
            return
              <x:frag>
                {$label}
                {$error}
                {$editor}
                {$description}
              </x:frag>;
        }

        return
          <x:frag>
            {$description}
            {$editor}
            {$error}
            {$label}
          </x:frag>;
    }
}

