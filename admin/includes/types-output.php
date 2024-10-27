<div id="data-output">
    <?php if ( $preset_types ) { ?>
        <h2>All types</h2>
    <?php } ?>

    <?php if ( $asset_types ) { ?>
        <form name="delete-assets" action="" method="POST" onsubmit="return confirm('All data with this type will be deleted. Are you sure ?');">
            <input type="hidden" name="delete_types_nonce" value="<?php echo esc_attr( wp_create_nonce( 'delete-types-nonce' ) ); ?>" />
            <table class="data-types">
                <thead>
                    <tr>
                        <th>
                            Order
                        </th>
                        <th>
                            Name
                        </th>
                        <?php if ( current_user_can( 'setup_network' ) && is_admin() ) { ?>
                            <th class="data-id">
                                ID
                            </th>
                        <?php } ?>
                        <th class="asset-group">
                            Group
                        </th>
                        <?php if ( current_user_can( 'setup_network' ) && is_admin() ) { ?>
                            <th class="added">
                                Added
                            </th>
                        <?php } ?>
                        <th class="closed">
                            Closed
                        </th>
                        <th class="hide-asset">
                            Hide
                        </th>
                        <th class="delete-asset">
                            Delete
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $asset_types as $type ) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url_raw( sprintf( admin_url( 'admin.php?page=bp-assets-types&type_id=%s' ), $type->id ) ); ?>">
                                    <?php echo esc_html( $type->ordering ); ?>
                                </a>
                            </td>
                            <td>
                                <?php echo esc_html( $type->name ); ?>
                            </td>
                            <?php if ( current_user_can( 'setup_network' ) && is_admin() ) { ?>
                                <td class="data-id">
                                    <?php echo esc_html( $type->id ); ?>
                                </td>
                            <?php } ?>
                            <td class="asset-group">
                                <?php echo esc_html( bp_get_group_by_id( $type->asset_group ) ); ?>
                            </td>
                            <?php if ( current_user_can( 'setup_network' ) && is_admin() ) { ?>
                                <td class="added">
                                    <?php echo isset( $type->added ) && '0000-00-00' !== $type->added ? esc_html( bp_format_value( $type->added, 'date' ) ) : ''; ?>
                                </td>
                            <?php } ?>
                            <td class="closed">
                                <?php echo isset( $type->closed ) && '0000-00-00' !== $type->closed ? esc_html( bp_format_value( $type->closed, 'date' ) ) : ''; ?>
                            </td>
                            <td class="hide-asset">
                                <?php echo $type->hide ? 'X' : ''; ?>
                            </td>
                            <td class="delete-asset checkbox">
                                <label>
                                    <?php echo sprintf( '<input type="checkbox" name="bp_delete_type[]" value="%s" />', esc_attr( $type->id ) ); ?>
                                </label>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <input type="submit" class="admin-button admin-button-small" value="Delete (selected) type(s)" />
        </form>
    <?php } ?>

</div>
