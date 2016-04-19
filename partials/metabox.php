<?php
$tobeornot_timestamp = get_post_meta( $post->ID, '_tobeornot_date', true );
$tobeornot_date = ( empty( $tobeornot_timestamp )) ? '' : DateTime::createFromFormat( 'U', $tobeornot_timestamp )->format( 'd.m.Y H:i' );
?>
<p>
    <label for="tobeornot_date">Дата завершения</label>
    <input type="text" name="tobeornot_date" id="tobeornot_date" value="<?php echo $tobeornot_date; ?>">
</p>
