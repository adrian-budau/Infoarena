<?php
// Contains XHP objects for the site's left side bar.

require_once(IA_ROOT_DIR . 'www/xhp/ia/login_form.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/list.php');
require_once(IA_ROOT_DIR . 'www/wiki/wiki.php');

class :ia:left-col extends :x:element {
    attribute
        array user;

    protected function render() {
        $user = $this->getAttribute('user');
        foreach ($this->getChildren('ia:left-navbar') as $child) {
            $child->setAttribute('user', $user);
        }
        return
          <div id="sidebar">
            {$this->getChildren()}
          </div>;
    }
}

class :ia:left-navbar extends :x:element {
    attribute
        array user;
    children empty;

    protected function render() {
        $user = $this->getAttribute('user');
        $list =
          <ui:list id="nav" class="clear">
            <ui:link href={url_home()}>
              Home
            </ui:link>

            <ui:link href={url_textblock('arhiva')} accesskey="a">
              Arhiva de probleme
            </ui:link>
            <ui:link href={url_textblock('arhiva-educationala')}>
              Arhiva educatională
            </ui:link>
            <ui:link href={url_textblock('concursuri')}>
              Concursuri
            </ui:link>
            <ui:link href={url_textblock('concursuri-virtuale')}>
              Concursuri virtuale
            </ui:link>
            <ui:link href={url_textblock('clasament-rating')}>
              Clasament
            </ui:link>
            <ui:link href={url_textblock('articole')}>
              Articole
            </ui:link>
            <ui:link href={url_textblock('downloads')}>
              Downloads
            </ui:link>
            <ui:link href={url_textblock('links')}>
              Links
            </ui:link>
            <ui:link href={url_textblock('documentatie')}>
              Documentaţie
            </ui:link>
            <ui:link href={url_textblock('despre-infoarena')}>
              Despre infoarena
            </ui:link>
            <li class="separator">
              <hr/>
            </li>
          </ui:list>;

        if ($user) {
            $list->appendChild(
                <ui:link
                  href={url_monitor(array('user' => $user['username']))}
                  accesskey="m">
                  Monitorul de evaluare
                </ui:link>);
            $list->appendChild(
                <ui:link href={url_submit()}>
                  <strong>Trimite soluţii</strong>
                </ui:link>);
            $list->appendChild(
                <ui:link href={url_account()} accesskey="c">
                  Contul meu
                </ui:link>);
        } else {
            $list->appendChild(
                <ui:link href={url_monitor()} accesskey="m">
                  Monitorul de evaluare
                </ui:link>);
        }

        return $list;
    }
}

class :ia:sidebar-ad extends :x:element {
    children empty;

    protected function render() {
        $sidebar = textblock_get_revision(IA_SIDEBAR_PAGE);
        if (!$sidebar) {
            return <x:frag />;
        }

        // FIXME: add XHP support
        return
          <div class="ad">
            <div class="wiki_text_block">
              {HTML(wiki_process_textblock($sidebar))}
            </div>
          </div>;
    }
}

class :ia:sidebar-login extends :x:element {
    attribute
        bool show_login_form = true;
    children empty;

    protected function render() {
        if ($this->getAttribute('show_login_form')) {
            $form = <ia:login-form />;
        } else {
            $form = <x:frag />;
        }

        $extra =
          <p>
            <ui:link href={url_register()}>
              Mă înregistrez!
            </ui:link>
            <br />
            <ui:link href={url_resetpass()}>
              Mi-am uitat parola...
            </ui:link>
          </p>;

        return
          <div id="login">
            {$form}
            {$extra}
          </div>;
    }
}
