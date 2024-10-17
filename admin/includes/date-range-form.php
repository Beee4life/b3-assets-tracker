<form name="" action="" method="post">
    <?php // @TODO: add nonce ?>
    <label>
        <select name="bp_date_range">
            <?php echo sprintf( '<option value="">%s</option>', 'All' ); ?>
            <?php foreach( $months as $month => $label ) { ?>
                <?php echo sprintf( '<option value="%s" %s>%s</option>', $month, selected($date_range, $month), $label ); ?>
            <?php } ?>
        </select>
    </label>

    <input name="" type="submit" value="Filter" />
</form>
