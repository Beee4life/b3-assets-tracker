<form method="post" onsubmit="return confirm('Are you sure you want to remove data ?');">
    <?php // @TODO: add nonce ?>
    <label>
        <select name="bp_remove_date">
            <?php echo sprintf( '<option value="">%s</option>', 'Select date' ); ?>
            <?php foreach( $all_dates as $date ) { ?>
                <?php echo sprintf( '<option value="%s">%s</option>', esc_attr( $date ), esc_html( bp_format_value( $date, 'date' ) ) ); ?>
            <?php } ?>
            <option value="all">All</option>
        </select>
    </label>

    <input name="" type="submit" value="Remove" />
</form>
