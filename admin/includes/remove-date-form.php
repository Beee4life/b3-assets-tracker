<form method="post" onsubmit="return confirm('Are you sure you want to remove data ?');">
    <input name="b3_remove_date_nonce" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'b3-remove-date-nonce' ) ); ?>" />
    <label>
        <select name="bp_remove_date">
            <?php echo sprintf( '<option value="">%s</option>', esc_attr__( 'Select date', 'b3-assets-tracker' ) ); ?>
            <?php foreach( $all_dates as $date ) { ?>
                <?php echo sprintf( '<option value="%s">%s</option>', esc_attr( $date ), esc_html( bp_format_value( $date, 'date' ) ) ); ?>
            <?php } ?>
            <option value="all">
                <?php esc_html_e( 'All', 'b3-assets-tracker' ); ?>
            </option>
        </select>
    </label>

    <input name="" type="submit" value="<?php esc_attr_e( 'Remove', 'b3-assets-tracker' ); ?>" />
</form>
