<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/revision-warning.php');
require_once(IA_ROOT_DIR . 'www/macros/macro_smfcomments.php');
require_once(IA_ROOT_DIR . 'www/xhp/ui/link.php');

class :ui:textblock extends :ui:element {
    children empty;

    attribute
        array textblock @required,
        bool show_forum = true,
        int revision = 0,
        int revision_count = 0,
        array permitted_actions;

    protected function render() {
        $textblock = $this -> getAttribute('textblock');
        $show_forum = $this -> getAttribute('show_forum');
        $revision = $this -> getAttribute('revision');
        $revision_count = $this -> getAttribute('revision_count');
        $permitted_actions = $this -> getAttribute('permitted_actions');

        $element = <x:frag />;

        $element -> appendChild(<ui:textblock:actions textblock={$textblock}
                                    permitted_actions={$permitted_actions} />);

        // Show revision warning in case we show a revision
        if ($revision != 0 && $revision_count != 0) {
            $warning = <ui:revision-warning revision={$revision}
                    revision_count={$revision}
                    timestamp={$textblock['timestamp']} />;

            if (in_array('delete-revision', $permitted_actions)) {
                $warning -> setAttribute('can_delete', true);
            }

            $element -> appendChild($warning);
        }

        $element -> appendChild(<div class="wiki_text_block"> {HTML($textblock['text'])} </div>);

        if ($show_forum == true && $textblock['forum_topic']) {
            $element -> appendChild(macro_smfcommects(array('topic' => $textblock['forum_topic'],
                                                            'display' => 'hide')));
        }

        return $element;
    }
}

class :ui:textblock:actions extends :ui:element {
    children empty;

    attribute
        array permitted_actions @required,
        array textblock @required;

    protected function render() {
        $permitted_actions = $this -> getAttribute('permitted_actions');
        $textblock = $this -> getAttribute('textblock');
        $actions = <ui:list /> ;

        if (in_array('edit', $permitted_actions)) {
           $actions -> appendChild(<ui:link accesskey="e" href={url_textblock_edit($textblock['name'])}> Editează </ui:link>);
        }

        if (in_array('history', $permitted_actions)) {
            $actions -> appendChild(<ui:link accesskey="i" href={url_textblock_history($textblock['name'])}> Istoria </ui:link>);
        }

        if (in_array('move', $permitted_actions)) {
            $actions -> appendChild(<ui:link accesskey="u" href={url_textblock_move($textblock['name'])}> Mută </ui:link>);
        }

        if (in_array('copy', $permitted_actions)) {
            $actions -> appendChild(<ui:link accesskey="c" href={url_textblock_copy($textblock['name'])}> Copiază </ui:link>);
        }
        if (in_array('delete', $permitted_actions)) {
            $actions -> appendChild(<ui:link:post accesskey="r" href={url_textblock_delete($textblock['name'])}
                                        confirm_request="Aceasta actiune este ireversibila! Doresti sa continui?">
                                      Şterge
                                    </ui:link:post>);
        }
        if (in_array('attach', $permitted_actions)) {
            $actions -> appendChild(<ui:link accesskey="t" href={url_attachment_new($textblock['name'])}> Ataşează </ui:link>);
        }

        if (in_array('attach-list', $permitted_actions)) {
            $actions -> appendChild(<ui:link accesskey="l" href={url_attachment_list($textblock['name'])}> Listează ataşamente </ui:link>);
        }

        if (count($actions -> getChildren()) == 0) {
            return <x:frag />;
        } else {
            return <div id="wikiOps"> {$actions} </div>;
        }
    }
}
