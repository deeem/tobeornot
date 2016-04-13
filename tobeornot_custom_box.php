<?php
$tobeornot_title = get_post_meta( $post->ID, '_tobeornot_title', true );
$tobeornot_description = get_post_meta( $post->ID, '_tobeornot_description', true );
$tobeornot_timestamp = get_post_meta( $post->ID, '_tobeornot_date', true );
$tobeornot_date = ( empty( $tobeornot_timestamp )) ? '' : DateTime::createFromFormat( 'U', $tobeornot_timestamp )->format( 'd.m.Y H:i' );
?>

<p>
    <label for="tobeornot_title">Заголовок прогноза</label>
    <input type="text" name="tobeornot_title" id="tobeornot_title" value="<?php echo $tobeornot_title; ?>">
</p>
<p>
    <label for="tobeornot_description">Описание прогноза</label>
    <textarea name="tobeornot_description" id="tobeornot_description"><?php echo $tobeornot_description; ?></textarea>
</p>
<p>
    <label for="tobeornot_date">Дата завершения</label>
    <input type="text" name="tobeornot_date" id="tobeornot_date" value="<?php echo $tobeornot_date; ?>">
</p>
