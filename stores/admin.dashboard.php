<?php

security_check();
admin_check();

define('APP_NAME', 'Stores');
define('PAGE_TITLE', 'Dashboard');
define('PAGE_SELECTED_SECTION', 'admin-dashboard');
define('PAGE_SELECTED_SUB_PAGE', '/admin/dashboard');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT * 
    FROM stores
    INNER JOIN countries
    ON stores.country_id = countries.id 
    ORDER BY name';    
$result = mysqli_query($connect, $query);

$stores_count = mysqli_num_rows($result);

$stores_last_import = setting_fetch('STORES_LAST_IMPORT');

?>

<!-- CONTENT -->

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/stores.png"
        height="50"
        style="vertical-align: top"
    />
    Stores
</h1>

<p>
    Number of stores imported: <span class="w3-tag w3-blue"><?=$stores_count?></span> | 
    Last import: <span class="w3-tag w3-blue"><?=(new DateTime($stores_last_import))->format("D, M j g:i A")?></span>
</p>

<hr />

<h2>Store List</h2>

<?php if (mysqli_num_rows($result)): ?>

    <table class="w3-table w3-bordered w3-striped w3-margin-bottom">
        <tr>
            <th>Name</th>
            <th>Country</th>
        </tr>

        <?php while ($record = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td>
                    <?=$record['name'] ?>
                    <br>
                    <small>
                        URL: <a href="https://www.lego.com/en-ca/stores/store/<?=$record['slug']?>">
                            https://www.lego.com/en-ca/stores/store/<?=$record['slug']?>
                        </a>
                    </small>
                </td>
                <td>
                    <?=$record['long_name'] ?>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

<?php else: ?>

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Results Found</h3>
        <p>Stores have not yet been imported from <a href="https://www.lego.com/en-ca/stores" class="w3-bold">lego.com</a>.</p>
    </div>

<?php endif; ?>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
