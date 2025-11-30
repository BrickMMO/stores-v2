<?php

define('APP_NAME', 'Colours');
define('PAGE_TITLE', 'Colour Details');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT colours.*, (
    SELECT GROUP_CONCAT(DISTINCT externals.name SEPARATOR ", ")
    FROM externals WHERE colours.id = externals.colour_id
) AS externals
FROM colours
WHERE id = "'.addslashes($_GET['key']).'"
LIMIT 1';
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

?>

<div class="w3-center">
    <h1><?=$record['name']?></h1>
</div>

<hr>

<div>
    <div style="display: inline-block; position: relative; width: 100%;  height: 150px; margin-bottom: 16px;">
        <div style="background-color: #<?=$record['rgb']?>; width: 100%; height: 100%;"></div>
        <?php if($record['is_trans'] == 'yes'): ?>
            <span style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; background: url('https://cdn.brickmmo.com/images@1.0.0/trans-checkers.png') repeat; background-position: center; opacity: 0.5; border-radius: 8px;"></span>
        <?php endif; ?>
    </div>
    <p>Name: <span class="w3-bold"><?=$record['name']?></span></p>
    <p>
        RGB: <span class="w3-bold">#<?=$record['rgb']?></span>
        <br>
        Transparent: <span class="w3-bold"><?=$record['is_trans'] == 'yes' ? 'Yes' : 'No'?></span>
    </p>
    <p>Externals: <span class="w3-bold"><?=$record['externals'] ?: '-'?></span></p>
</div>

<hr>

<a href="/q" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-caret-left fa-padding-right"></i>
    Back to Colour List
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');