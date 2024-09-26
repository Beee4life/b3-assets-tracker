<div id="data-output">
    <br>
    <h2>All types</h2>
    
    <?php if ( $types ) { ?>
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
                        Hide in views
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
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

</div>
