<form name="" action="" method="post">
    <input name="b3_date_range_nonce" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'b3-date-range-nonce' ) ); ?>" />
    
    <label>
        <select name="bp_date_range">
            <?php echo sprintf( '<option value="">%s</option>', 'All' ); ?>
            <?php foreach( $months as $month => $label ) { ?>
                <?php echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $month ), selected($date_range, $month), esc_html( $label ) ); ?>
            <?php } ?>
        </select>
    </label>

    <input name="" type="submit" value="Filter" />
</form>
