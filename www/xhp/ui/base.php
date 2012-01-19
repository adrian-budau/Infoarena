<?php

class :ui:element extends :x:element {
    attribute
        string name,
        string id,
        string class,
        string access_key;
    // TODO: facebook was doing cool stuff with CSS margin and padding in ui
    // elements

    final public function sendAttributes($destination, $attributes = null) {
        if ($attributes == null) {
            $attributes = array_keys($this -> getAttributes());
        }

        if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                $this -> sendAttributes($destination, $attribute);
            }
        } elseif ($this -> getAttribute($attributes) && method_exists($destination, 'hasAttribute') && $destination -> hasAttribute($attributes)) {
                $destination -> setAttribute($attributes, $this -> getAttribute($attributes));
        }
    }
}
