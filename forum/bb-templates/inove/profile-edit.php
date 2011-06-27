<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Редактировать профиль</h3>
<h2 id="userlogin"><?php echo get_user_name( $user->ID ); ?></h2>
<form method="post" action="<?php profile_tab_link($user->ID, 'edit');  ?>">
<fieldset>
<legend>Данные профиля</legend>
<?php bb_profile_data_form(); ?>
</fieldset>

<?php if ( bb_current_user_can( 'edit_users' ) ) : ?>
<fieldset>
<legend>Администрирование</legend>
<?php bb_profile_admin_form(); ?>
</fieldset>
<?php endif; ?>

<?php if ( bb_current_user_can( 'change_user_password', $user->ID ) ) : ?>
<fieldset>
<legend>Пароль</legend>
<p>Если Вы хотите изменить Ваш пароль, Вам нужно дважды ввести новый пароль:</p>
<?php bb_profile_password_form(); ?>
</fieldset>
<?php endif; ?>
<p class="submit right">
  <input type="submit" name="Submit" value="<?php echo attribute_escape( 'Обновить профиль &raquo;' ); ?>" />
</p>
</form>
<form method="post" action="<?php profile_tab_link($user->ID, 'edit');  ?>">
<p class="submit left">
<?php bb_nonce_field( 'edit-profile_' . $user->ID ); ?>
<?php user_delete_button(); ?>
</p>
</form>

<?php bb_get_footer(); ?>
