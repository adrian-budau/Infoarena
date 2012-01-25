<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

class :ui:form extends :ui:element {
    attribute
        :form,
        string legend = "",
        string imageURL = "",
        array values,
        array errors,
        array descriptions,
        enum { "list", "inline" } type = "list";

    protected function render() {
        $legend = $this -> getAttribute('legend');
        $imageURL = $this -> getAttribute('imageURL');
        $values = $this -> getAttribute('values');
        $errors = $this -> getAttribute('errors');
        $descriptions = $this -> getAttribute('descriptions');
        $type = $this -> getAttribute('type');

        foreach($this -> getChildren() as $child) {
            if ($child instanceof :ui:form_field) {
                $name = $child -> getAttribute('name');

                if ($value = getattr($values, $name, '')) {
                    $child -> setAttribute('value', $value);
                }

                if ($error = getattr($errors, $name, '')) {
                    $child -> setAttribute('error', $error);
                }

                if ($description = getattr($descriptions, $name, '')) {
                    $child -> setAttribute('description', $description);
                }
            }
        }

        if ($legend || $imageURL) {
            $title = <legend />;
            if ($imageURL) {
                $title -> appendChild(<img src={$imageURL} alt="!" />);
            }

            if ($legend) {
                $title -> appendChild(<x:frag>{$legend}</x:frag>);
            }
        } else {
            $title = <x:frag />;
        }

        if ($type == 'list') {
            $container =
              <ui:list class="form">
                {$this -> getChildren()}
              </ui:list>;
        } else {
            $container =
              <x:frag>
                {$this -> getChildren()}
              </x:frag>;
        }

        $form =
          <form>
            <fieldset>
              {$title}
              {$container}
            </fieldset>
          </form>;

        $this -> sendAttributes($form);

        return $form;
    }
}

abstract class :ui:form_field extends :ui:element {
    attribute
        var editor,
        bool label=true,
        enum { "normal", "reversed" } order = "normal",
        string accesskey,
        string error,
        string description;

    protected function render() {
        // Setting the label for the form field
        if ($this -> getAttribute('label') == true && array_key_exists(0, $this -> getChildren())) {
            $label =
              <label for={'form_' . $this -> getAttribute('name')}
                accesskey={$this -> getAttribute('accesskey')}>
                <ui:highlight_accesskey accesskey ={$this -> getAttribute('accesskey')}>
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

        // Getting the editor
        $editor = $this -> getAttribute('editor');

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
        $this -> sendAttributes($label, 'class');

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

class :ui:form:input extends :ui:form_field {
    attribute
        :input,
        enum { "text", "password" } type = "text";

    protected function render() {
        $editor =
          <input name={$this -> getAttribute('name')}
              id={'form_' . $this -> getAttribute('name')}
              value={$this -> getAttribute('value')}
              type={$this -> getAttribute('type')} />;

        $this -> setAttribute('editor', $editor);
        return :ui:form_field::render();
    }
}

class :ui:form:checkbox extends :ui:form_field {
    attribute
        :input;

    protected function render() {
        $editor =
          <input name={$this -> getAttribute('name')}
              id={'form_' . $this -> getAttribute('name')}
              checked={$this -> getAttribute('value')}
              type="checkbox" />;


        $this -> sendAttributes($editor, 'checked');
        $this -> setAttribute('editor', $editor);
        return :ui:form_field::render();
    }
}
