<?php

require_once(IA_ROOT_DIR . 'www/xhp/ui/base.php');
require_once(IA_ROOT_DIR . 'www/format/format.php');
class :ui:user extends :ui:element {
    attribute
        array user;
}

class :ui:user:avatar extends :ui:user {
    attribute
        enum { "tiny", "small", "normal", "forum", "big", "full" } size = "full";

    protected function render() {
        $user = $this -> getAttribute('user');
        $avatar =
          <img src={url_user_avatar($user, $this -> getAttribute('size'))}
              alt={$user['username']} />;

        return $avatar;
    }
}

class :ui:user:rating-badge extends :ui:user {

    protected function render() {
        $user = $this -> getAttribute('user');
        $is_admin = user_is_admin($user, false);
        $rating = $user['rating_cache'];
        $class = rating_group($rating, $is_admin);
        $rating = rating_scale($rating);

        if ($rating) {
            $tag =
              <ui:link:user page="rating" user={$user} class={'rating-badge-' . $class}
                  title={'Rating ' . $user['username'] . ': ' . $rating}>
                &bull;
              </ui:link:user>;
        } else {
            $tag = <x:frag />;
        }
        return $tag;
    }
}

class :ui:user:link extends :ui:user {
    attribute
        bool show_rating = true;

    protected function render() {
        $user = $this -> getAttribute('user');
        $link =
          <ui:link:user user={$user}>
            {$user['full_name']}
          </ui:link:user>;

        if ($this -> getAttribute('show_rating') && !is_null($user['rating_cache'])) {
            $link -> setAttribute('class', 'user_' . rating_group($user['rating_cache']));
        }

        if ($this -> getAttribute('show_rating')) {
            $rating_badge = <ui:user:rating-badge user={$user} />;
        } else {
            $rating_badge = <x:frag />;
        }

        return
          <x:frag>
            {$rating_badge}
            {$link}
          </x:frag>;
    }
}
class :ui:user:tiny extends :ui:user {

    protected function render() {
        $user = $this -> getAttribute('user');
        $tag =
          <x:frag>
            <span class="tiny-user">
              <ui:link:user user={$user}>
                <ui:user:avatar size="tiny" />
                {$user['full_name']}
              </ui:link:user>

              <ui:user:rating-badge user={$user}/>

              <span class="username">
                <ui:link:user user={$user}>
                  {$user['username']}
                </ui:link:user>
              </span>

            </span>
          </x:frag>;

       return $tag;
    }
}

class :ui:user:normal extends :ui:user {

    protected function render() {
        $user = $this -> getAttribute('user');
        $tag =
          <x:frag>
           <div class="normal-user">
             <ui:link:user user={$user}>
               <ui:user:avatar size="small" />
             </ui:link:user>

             <span class="fullname">
               {$user['full_name']}
             </span>

             <br />

             <ui:user:rating-badge user={$user}/>

             <ui:link:user user={$user}>
               <span class="username">
                 {$user['username']}
               </span>
             </ui:link:user>
           </div>
         </x:frag>;
       return $tag;
    }
}
?>
