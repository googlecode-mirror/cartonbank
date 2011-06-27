<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Зарегистрироваться</h3>

<h2 id="register">Поздравляем!</h2>

<p><?php printf('Вы зарегистрированы как <strong>%s</strong>. В течение нескольких минут на Ваш email будет выслан пароль.', $user_login) ?></p>

<?php bb_get_footer(); ?>
