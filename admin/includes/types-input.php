<div id="data-input">
    <form name="add-type" action="" method="post">
        <input name="add_type_nonce" type="hidden" value="<?php echo wp_create_nonce( 'add-type-nonce' ); ?>" />
        <?php if ( $current_type ) { ?>
            <input name="update_type" type="hidden" value="<?php echo $current_type; ?>" />
        <?php } ?>
        
        <?php if ( $preset_types ) { ?>
            <h2>Preset choices</h2>
            <table class="data-input">
                <?php foreach( $preset_types as $id => $label ) { ?>
                    <tr>
                        <td><?php echo $label; ?></td>
                        <td>
                            <label>
                                <?php echo sprintf( '<input name="%d" type="checkbox" value="1" %s/>', $id, checked( $id, in_array( $id, [] ) ) ); ?>
                            </label>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        
        <?php if ( $preset_types ) { ?>
            <h2>Free choices</h2>
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
                <?php if ( ! empty( $asset_groups ) ) { ?>
                    <th class="asset-group">
                        Group
                    </th>
                <?php } ?>
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
                <?php if ( ! empty( $asset_groups ) ) { ?>
                    <td class="asset-group">
                        <label>
                            <select name="bp_asset_group">
                                <option value="">Group</option>
                                <?php foreach( $asset_groups as $group ) { ?>
                                    <?php echo sprintf( '<option value="%s" %s>%s</option>', $group->id, selected( $group->id ), $group->name ); ?>
                                <?php } ?>
                            </select>
                        </label>
                    </td>
                <?php } ?>
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
