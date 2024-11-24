<div id="data-output">
    <?php if ( $preset_types ) { ?>
        <h2>All types</h2>
    <?php } ?>

    <?php if ( $asset_types ) { ?>
        <form name="delete-assets" action="" method="POST" onsubmit="return confirm( esc_attr__( 'All data with this type will be deleted. Are you sure ?', 'b3-assets-tracker' ) );">
            <input type="hidden" name="delete_types_nonce" value="<?php echo esc_attr( wp_create_nonce( 'delete-types-nonce' ) ); ?>" />
            <table class="data-types">
                <thead>
                    <tr>
                        <th>
                            <?php esc_html_e( 'Order', 'b3-assets-tracker' ); ?>
                        </th>
                        <th>
                            <?php esc_html_e( 'Name', 'b3-assets-tracker' ); ?>
                        </th>
                        <?php if ( current_user_can( 'setup_network' ) && is_admin() ) { ?>
                            <th class="data-id">
                                ID
                            </th>
                        <?php } ?>
                        <th class="asset-group">
                            <?php esc_html_e( 'Group', 'b3-assets-tracker' ); ?>
                        </th>
                        <th class="added">
                            <?php esc_html_e( 'Added', 'b3-assets-tracker' ); ?>
                        </th>
                        <th class="closed">
                            <?php esc_html_e( 'Closed', 'b3-assets-tracker' ); ?>
                        </th>
                        <th class="hide-asset">
                            <?php esc_html_e( 'Hide', 'b3-assets-tracker' ); ?>
                        </th>
                        <th class="delete-asset">
                            <?php esc_html_e( 'Delete', 'b3-assets-tracker' ); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $asset_types as $type ) { ?>
                        <tr>
                            <td>
                                <?php if ( is_admin() || bp_show_admin_links() ) { ?>
                                    <a href="<?php echo esc_url_raw( sprintf( admin_url( 'admin.php?page=bp-assets-types&type_id=%s' ), $type->id ) ); ?>">
                                        <?php echo esc_html( $type->ordering ); ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo esc_url_raw( sprintf( get_home_url( '', 'types?type_id=%s' ), $type->id ) ); ?>">
                                        <?php echo esc_html( $type->ordering ); ?>
                                    </a>
                                <?php } ?>
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
                            <td class="added">
                                <?php echo isset( $type->added ) && '0000-00-00' !== $type->added ? esc_html( bp_format_value( $type->added, 'date' ) ) : ''; ?>
                            </td>
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
            <input type="submit" class="" value="<?php esc_attr_e( 'Delete selected type(s)', 'b3-assets-tracker' ); ?>" />
        </form>
    <?php } ?>

</div>
