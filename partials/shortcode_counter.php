<?php if ( $message === FALSE ) : ?>
    <?php // echo 'дата голосования не определена'; ?>
<?php elseif ( $message === NULL ) : ?>
    <div class="tobeornot_counter">
        <div class="tobeornot_counter--message">Завершён</div>
    </div>
<?php else : ?>
    <div class="tobeornot_counter">
        <div class="tobeornot_counter--message"><?php echo $message; ?></div>
    </div>
<?php endif; ?>
