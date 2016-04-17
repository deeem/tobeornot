<?php if ( $message === FALSE ) : ?>
    <?php // echo 'дата голосования не определена'; ?>
<?php elseif ( $message === NULL ) : ?>
    <p>завершено</p>
<?php else : ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
