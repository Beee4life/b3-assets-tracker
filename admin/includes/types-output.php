<div id="data-output">
    <br>
    <h2>All types</h2>
    
    <?php if ( $asset_types ) { ?>
        <form name="" action="" method="POST" onsubmit="return confirm('All data with this type will be deleted. Are you sure ?');">
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
                        <th>
                            Group
                        </th>
                        <th>
                            Hide
                        </th>
                        <th>
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
                            <td class="">
                                <?php echo bp_get_group_by_id( $type->asset_group ); ?>
                            </td>
                            <td>
                                <?php echo $type->hide ? 'X' : ''; ?>
                            </td>
                            <td>
                                <label>
                                    <?php echo sprintf( '<input type="checkbox" name="bp_delete_type[]" value="%s" />', $type->id ); ?>
                                </label>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <input type="submit" class="admin-button admin-button-small" value="Delete" />
        </form>
    <?php } ?>

</div>
