<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/form.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/list.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/button.php');
require_once(IA_ROOT_DIR . 'www/views/utilities.php');

class :ia:login-form extends :ui:form {

    protected function render() {
        // FIXME: remove old style global form_values and form_errors
        $this -> setAttribute('action', url_login());
        $this -> setAttribute('method', 'post');
        $this -> setAttribute('class', 'login');
        $this -> setAttribute('imageURL', url_static('images/icons/login.png'));
        $this -> setAttribute('legend', ' Autentificare');

        $this -> appendChild(
          <x:frag>
            <ui:form:input name="username" type="text" accesskey="c">
              Cont de utilizator
            </ui:form:input>

            <ui:form:input name="password" type="password" accesskey="p">
              Parola
            </ui:form:input>

            <ui:form:checkbox name="remember" class="checkbox" order="reversed">
              Pastreaza-ma autentificat 5 zile
            </ui:form:checkbox>

            <ui:button type="submit" id="form_submit" important="true">
              Autentificare
            </ui:button>
          </x:frag>);

        return :ui:form::render();
    }
}
