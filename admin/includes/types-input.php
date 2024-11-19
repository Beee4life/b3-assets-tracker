<div id="data-input">
    <form name="add-type" action="" method="post">
        <input name="add_type_nonce" type="hidden" value="<?php echo esc_html( wp_create_nonce( 'add-type-nonce' ) ); ?>" />
        <?php if ( $current_type ) { ?>
            <input name="update_type" type="hidden" value="<?php echo esc_attr( $current_type ); ?>" />
        <?php } ?>

        <?php if ( $preset_types ) { ?>
            <h2>
                <?php esc_html_e( 'Preset choices', 'b3-assets-tracker' ); ?>
            </h2>
            <table class="data-input">
                <?php foreach( $preset_types as $id => $label ) { ?>
                    <tr>
                        <td><?php echo esc_html( $label ); ?></td>
                        <td>
                            <label>
                                <?php echo sprintf( '<input name="%d" type="checkbox" value="1" %s/>', esc_attr( $id ), checked( $id, in_array( $id, [] ) ) ); ?>
                            </label>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>

        <?php if ( $preset_types ) { ?>
            <h2>
                <?php esc_html_e( 'Free choices', 'b3-assets-tracker' ); ?>
            </h2>
        <?php } ?>

        <table class="data-input">
            <thead>
            <tr>
                <th>
                    <?php esc_html_e( 'Type (name)', 'b3-assets-tracker' ); ?>
                </th>
                <th>
                    <?php esc_html_e( 'Order', 'b3-assets-tracker' ); ?>
                </th>
                <?php if ( ! empty( $asset_groups ) ) { ?>
                    <th class="asset-group">
                        <?php esc_html_e( 'Group', 'b3-assets-tracker' ); ?>
                    </th>
                <?php } ?>
                <?php if ( $current_type ) { ?>
                    <th class="added">
                        <?php esc_html_e( 'Added', 'b3-assets-tracker' ); ?>
                    </th>
                    <th class="closed">
                        <?php esc_html_e( 'Closed', 'b3-assets-tracker' ); ?>
                    </th>
                <?php } ?>
                <th class="checkbox hide-type">
                    <?php esc_html_e( 'Hide', 'b3-assets-tracker' ); ?>
                </th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <label>
                        <input name="bp_type" type="text" value="<?php echo esc_attr( $type_value ); ?>" required />
                    </label>
                </td>
                <td>
                    <label>
                        <input name="bp_order" type="number" value="<?php echo esc_attr( $order_value ); ?>" />
                    </label>
                </td>
                <?php if ( ! empty( $asset_groups ) ) { ?>
                    <td class="asset-group">
                        <label>
                            <select name="bp_asset_group">
                                <option value="">
                                    <?php esc_attr_e( 'Group', 'b3-assets-tracker' ); ?>
                                </option>
                                <?php foreach( $asset_groups as $group ) { ?>
                                    <?php echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $group->id ), selected( $group->id, $group_value ), esc_attr( $group->name ) ); ?>
                                <?php } ?>
                            </select>
                        </label>
                    </td>
                <?php } ?>
                <?php if ( $current_type ) { ?>
                    <td>
                        <label>
                            <input name="bp_added" type="date" class="" min="" value="<?php echo esc_attr( $added_value ); ?>" max="<?php echo esc_attr( gmdate( 'Y-m-d', time() ) ); ?>" />
                        </label>
                    </td>
                    <td>
                        <label>
                            <input name="bp_closed" type="date" class="" min="" value="<?php echo esc_attr( $closed_value ); ?>" max="<?php echo esc_attr( gmdate( 'Y-m-d', time() ) ); ?>" />
                        </label>
                    </td>
                <?php } ?>
                <td class="checkbox hide-type">
                    <label>
                        <input name="bp_hide" type="checkbox" value="1" <?php checked( $hide_value ); ?> />
                    </label>
                </td>
                <td>
                    <input type="submit" class="" value="<?php echo esc_attr( $button_label ); ?>" />
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
