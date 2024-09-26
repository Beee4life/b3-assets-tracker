<div id="data">
    <table class="data-output">
        <?php $row_counter = 1; ?>
        <?php $amount_rows = count($grouped_data); ?>
        <?php $amount_cols = count($grouped_data[0]); ?>
        
        <?php foreach( $grouped_data as $row ) { ?>
            <?php if ( 1 == $row_counter ) { ?>
                <?php include 'top-row.php'; ?>
            <?php } else { ?>
                <?php include 'content-row.php'; ?>
            <?php } ?>
            <?php $row_counter++; ?>
        <?php } ?>
    </table>
</div>
