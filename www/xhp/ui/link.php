<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');
require_once(IA_ROOT_DIR . 'www/JSON.php');

class :ui:highlight_accesskey extends :ui:element {
    attribute
        string accesskey;

    protected function render() {
        $value = (string)(<x:frag>
                            {$this -> getChildren()}
                          </x:frag>);
        $access_key = $this -> getAttribute('accesskey');
        if (($pos = stripos($value, $access_key)) !== false) {
            return
              <x:frag>
                {substr($value, 0, $pos)}
                <span class="access-key">
                  {$value[$pos]}
                </span>
                {substr($value, $pos + 1)}
              </x:frag>;
        } else {
            return
                <x:frag>
                    {$value}
                </x:frag>;
        }
    }
}

class :ui:link extends :ui:element {
    attribute
        bool highlight_accesskey = true,    // Whether or not to highlight the
                                            // link's accesskey if specified.
        :a;

    protected function render() {
        $elem = <a>{$this->getChildren()}</a>;

        $accesskey = $this->getAttribute('accesskey');
        if ($accesskey && $this->getAttribute('highlight_accesskey')) {
            // This assumes that no HTML tags are present inside the link.
            // If these are present, they will be escaped.
            $elem = <a>
                      <ui:highlight_accesskey accesskey={$this -> getAttribute('accesskey')}>
                        {$this -> getChildren()}
                      </ui:highlight_accesskey>
                    </a>;
        }

        $this -> sendAttributes($elem);
        return $elem;
    }
}

class :ui:link:post extends :ui:link {
    attribute
        array post_data;

    protected function render() {
        $post_data = $this -> getAttribute('post_data');
        $href = $this -> getAttribute('href');

        $json = new Services_JSON();
        $href = "javascript:PostData(" . str_replace('"', "'", $json -> encode($href)) . ", " . str_replace('"', "'", $json -> encode($post_data)) . ")";
        $this -> setAttribute('href', $href);
        return :ui:link::render();
    }
}

class :ui:link:user extends :ui:link {
    attribute
        array user @required,
        enum { "default", "rating", "stats" } page = "default";

    protected function render() {
        $user = $this -> getAttribute('user');
        $page = $this -> getAttribute('page');
        if ($page == "default") {
            $href = url_user_profile($user['username'], true);
        } else if ($page == "rating") {
            $href = url_user_rating($user['username']);
        } else if ($page == "stats") {
            $href = url_user_stats($user['username']);
        }
        $this -> setAttribute('href', $href);

        foreach ($this -> getChildren() as $child) {
            $this -> sendAttributes($child);
        }
        return :ui:link::render();
    }
}

class :ui:link:pager extends :ui:link {
    attribute
        int display_entries = 50,
        int first_entry = 0,
        string prefix = "";

    protected function render() {
        $prefix = $this -> getAttribute('prefix');

        $options = array();
        $options[$prefix . 'first_entry'] = $this -> getAttribute('first_entry');
        $options[$prefix . 'display_entries'] = $this -> getAttribute('display_entries');

        $this -> setAttribute('href', url_options_add($options));

        if (count($this -> getChildren()) == 0) {
            $page = ($this -> getAttribute('first_entry') / $this -> getAttribute('display_entries')) + 1;
            $this -> appendChild(<x:frag> {$page} </x:frag>);
        }
        return :ui:link::render();
    }
}
