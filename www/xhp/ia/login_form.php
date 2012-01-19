<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/form.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/list.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/button.php');

class :ia:login-form extends :x:element {
    children empty;

    protected function render() {
        // FIXME: remove old style global form_values and form_errors
        return
          <form action={url_login()} method="post" class="login">
            <fieldset>

              <legend>
                <img src={url_static('images/icons/login.png')} alt="!" />
                Autentificare
              </legend>

              <ui:list class="form">

                <ui:form_field name="username" type="string" access_key="c">
                  Cont de utilizator
                </ui:form_field>

                <ui:form_field name="password" type="password" access_key="p">
                  Parola
                </ui:form_field>

                <ui:form_field type="checkbox" value="on" name="remember" class="checkbox" checked={fval('remember') ? 'checked' : null} order="reversed">
                  Pastreaza-ma autentificat 5 zile
                </ui:form_field>

                <ui:button type="submit" id="form_submit" important="true">
                  Autentificare
                </ui:button>

              </ui:list>

            </fieldset>
          </form>;
    }
}
