<div id="data-output">
    <br>
    <h2>All types</h2>
    
    <?php if ( $types ) { ?>
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
                            Hide
                        </th>
                        <th>
                            Delete
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $types as $type ) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo sprintf(admin_url( 'admin.php?page=bp-assets-types&id=%s' ), $type->id ); ?>">
                                    <?php echo $type->ordering; ?>
                                </a>
                            </td>
                            <td>
                                <?php echo $type->name; ?>
                            </td>
                            <td class="data-id">
                                <?php echo $type->id; ?>
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
            <input type="submit" class="admin-button admin-button-small" />
        </form>
    <?php } ?>

</div>
