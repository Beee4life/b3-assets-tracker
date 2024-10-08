<div id="data-output">
    <br>
    <h2>All types</h2>
    
    <?php if ( $asset_types ) { ?>
        <form name="delete-assets" action="" method="POST" onsubmit="return confirm('All data with this type will be deleted. Are you sure ?');">
            <input type="hidden" name="delete_types_nonce" value="<?php echo wp_create_nonce( 'delete-types-nonce' ); ?>" />
            <table class="data-types">
                <thead>
                    <tr>
                        <th>
                            Order
                        </th>
                        <th>
                            Name
                        </th>
                        <th class="data-id">
                            ID
                        </th>
                        <th class="asset-group">
                            Group
                        </th>
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
                                <a href="<?php echo sprintf(admin_url( 'admin.php?page=bp-assets-types&type_id=%s' ), $type->id ); ?>">
                                    <?php echo $type->ordering; ?>
                                </a>
                            </td>
                            <td>
                                <?php echo $type->name; ?>
                            </td>
                            <td class="data-id">
                                <?php echo $type->id; ?>
                            </td>
                            <td class="asset-group">
                                <?php echo bp_get_group_by_id( $type->asset_group ); ?>
                            </td>
                            <td class="closed">
                                <?php echo isset( $type->closed ) && '0000-00-00' !== $type->closed ? bp_format_value( $type->closed, 'date' ) : ''; ?>
                            </td>
                            <td class="hide-asset">
                                <?php echo $type->hide ? 'X' : ''; ?>
                            </td>
                            <td class="delete-asset">
                                <label>
                                    <?php echo sprintf( '<input type="checkbox" name="bp_delete_type[]" value="%s" />', $type->id ); ?>
                                </label>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <input type="submit" class="admin-button admin-button-small" value="Delete (selected) asset(s)" />
        </form>
    <?php } ?>

</div>
