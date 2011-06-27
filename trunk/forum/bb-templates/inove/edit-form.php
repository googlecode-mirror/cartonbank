
<?php if ( $topic_title ) : ?>
<p>
  <label>Тема:<br />

  <input name="topic" type="text" id="topic" size="50" maxlength="80"  value="<?php echo attribute_escape( get_topic_title() ); ?>" />
</label>
</p>
<?php endif; ?>
<p><label>Сообщение:<br />
  <textarea name="post_content" cols="50" rows="8" id="post_content"><?php echo apply_filters('edit_text', get_post_text() ); ?></textarea>
  </label>
</p>
<p class="submit">
<input type="submit" name="Submit" value="<?php echo attribute_escape( 'Редактировать сообщение &raquo;' ); ?>" />
<input type="hidden" name="post_id" value="<?php post_id(); ?>" />
<input type="hidden" name="topic_id" value="<?php topic_id(); ?>" />
</p>
<p>Разрешенные коды: <code><?php allowed_markup(); ?></code>. <br />Вставляйте код между <code>`апострофами`</code>.</p>
