<?php

security_check();
admin_check();

$colours_last_import = setting_fetch('COLOURS_LAST_IMPORT');

if (isset($_GET['key']) && $_GET['key'] == 'go') 
{

    $url = 'https://rebrickable.com/api/v3/lego/colors/?page_size=500';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Authorization: key '.REBRICKABLE_KEY,
    ]);

    $response = curl_exec($curl);

    curl_close($curl);

    $response = json_decode($response, true);

    $query = 'TRUNCATE TABLE colours';
    mysqli_query($connect, $query);

    $query = 'TRUNCATE TABLE externals';
    mysqli_query($connect, $query);

    $query = 'UPDATE settings SET 
        value = NOW() 
        WHERE name = "COLOURS_LAST_IMPORT" 
        LIMIT 1';
    mysqli_query($connect, $query);

    foreach($response['results'] as $colour)
    {

        $query = 'INSERT INTO colours (
                name,
                rgb,
                is_trans,
                rebrickable_id,
                created_at,
                updated_at
            ) VALUES (
                "'.$colour['name'].'",
                "'.$colour['rgb'].'",
                "'.($colour['is_trans'] ? 'yes' : 'no').'",
                "'.$colour['id'].'",
                NOW(),
                NOW()
            )';
        mysqli_query($connect, $query);

        $id = mysqli_insert_id($connect);

        foreach($colour['external_ids'] as $key => $value)
        {

            foreach($value['ext_ids'] as $key2 => $value2)
            {

                $query = 'INSERT INTO externals (
                        name,
                        source,
                        colour_id,
                        created_at,
                        updated_at
                    ) VALUES (
                        "'.$colour['external_ids'][$key]['ext_descrs'][$key2][0].'",
                        "'.strtolower($key).'",
                        "'.$id.'",
                        NOW(),
                        NOW()
                    )';
                mysqli_query($connect, $query);

            }

        }

    }
    
    message_set('Import Success', 'Colour list has been imported from Rebrickable.');
    header_redirect('/admin/import');

}

define('APP_NAME', 'Colours');
define('PAGE_TITLE', 'Import Colours');
define('PAGE_SELECTED_SECTION', 'admin-import');
define('PAGE_SELECTED_SUB_PAGE', '/admin/import');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT * 
    FROM colours 
    ORDER BY name';
$result = mysqli_query($connect, $query);

?>

<!-- CONTENT -->

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/colours.png"
        height="50"
        style="vertical-align: top"
    />
    Colours
</h1>
<p>
    <a href="/admin/dashboard">Colours</a> / 
    Import Colours
</p>

<hr />

<h2>Import Colours</h2>

<p>
    There are currently 
    <span class="w3-tag w3-blue"><?=mysqli_num_rows($result)?></span>
    colours imported from 
    <a href="https://rebrickable.com/api/">Rebrickable</a>.
</p>

<hr />

<p>
    Re-importimg the colors from 
    <a href="https://rebrickable.com/api/">Rebrickable</a> will:
</p>

<ul class="w3-margin-bottom">
    <li>Delete the current colours data.</li>
    <li>Re-import the colour data from <a href="https://rebrickable.com/api/">Rebrickable</a>.</li>
</ul>
            
<a
    href="/admin/import/go"
    class="w3-button w3-white w3-border"
    onclick="loading();"
>
    <i class="fa-solid fa-download"></i> Start Import
</a>
    
<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
