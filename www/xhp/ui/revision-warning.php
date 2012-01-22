<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');

class :ui:revision-warning extends :ui:element {
    attribute
        int revision = 1,
        int revision_count = 1,
        string timestamp @required,
        bool can_delete = false;

    protected function render() {
        $revision = $this -> getAttribute('revision');
        $revision_count = $this -> getAttribute('revision_count');
        $timestamp = $this -> getAttribute('timestamp');

        $warning = <div class="warning" />;

        if ($revision < $revision_count) {
            $warning -> appendChild(<x:frag> Atenţie! Aceasta este o versiune veche a paginii </x:frag>);
        } else {
            $warning -> appendChild(<x:frag> Atenţie! Aceasta este ultima versiune a paginii </x:frag>);
        }

        $warning -> appendChild(<x:frag>
                                  , scrisă la {$timestamp}
                                  <br />
                                </x:frag>);

        if ($revision > 1) {
            $warning -> appendChild(<ui:link href={url_textblock_revision(url_no_options(), $revision - 1)}>
                                      Revizia anterioară
                                    </ui:link>);
        } else {
            $warning -> appendChild(<x:frag> Revizia anterioară </x:frag>);
        }


        if ($revision < $revision_count) {
            $warning -> appendChild(<ui:link href={url_textblock_revision(url_no_options(), $revision + 1)}>
                                      Revizia următoare
                                    </ui:link>);
        } else {
            $warning -> appendChild(<x:frag> Revizia următoare </x:frag>);
        }


        if ($this -> getAttribute('can_delete') == true) {
            $warning -> appendChild(<ui:link:post href={url_textblock_delete_revision(url_no_options(), $revision)}>
                                      Şterge
                                    </ui:link:post>);
        }
    }
}
?>
