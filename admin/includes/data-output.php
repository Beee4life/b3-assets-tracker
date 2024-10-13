<?php
    $amount_rows  = count( $grouped_data );
    $amount_cols  = count( $grouped_data[ 0 ] );
?>

<table class="data-output<?php echo $scroll_class; ?>">
    <?php $row_counter = 1; ?>
    <?php foreach( $grouped_data as $row ) { ?>
        <?php if ( 1 == $row_counter ) { ?>
            <?php include 'top-row.php'; ?>
        <?php } else { ?>
            <?php include 'content-row.php'; ?>
        <?php } ?>
        <?php $row_counter++; ?>
    <?php } ?>
</table>
<?php if ( ! is_admin() && 9 <= $amount_cols ) { ?>
    <div class="table_note">
        Table scrolls horizontally (shift + mousewheel on desktop, swipe on mobile).
    </div>
<?php } ?>
