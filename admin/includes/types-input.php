<div id="data-input">
    <form name="add-type" action="" method="post">
        <input name="add_type_nonce" type="hidden" value="<?php echo wp_create_nonce( 'add-type-nonce' ); ?>" />
        <?php if ( $current_type ) { ?>
            <input name="update_type" type="hidden" value="<?php echo $current_type; ?>" />
        <?php } ?>
        <table class="data-input">
            <thead>
            <tr>
                <th>
                    Type (name)
                </th>
                <th>
                    Order
                </th>
                <th class="hide-type">
                    Hide
                </th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <label>
                        <input name="bp_type" type="text" value="<?php echo $type_value; ?>" required />
                    </label>
                </td>
                <td>
                    <label>
                        <input name="bp_order" type="number" value="<?php echo $order_value; ?>" />
                    </label>
                </td>
                <td class="hide-type">
                    <label>
                        <input name="bp_hide" type="checkbox" value="1" <?php checked( $hide_value ); ?> />
                    </label>
                </td>
                <td>
                    <input type="submit" class="admin-button admin-button-small" />
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>