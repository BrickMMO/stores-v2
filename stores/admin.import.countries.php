<?php

security_check();
admin_check();

$countries_last_import = setting_fetch('COUNTRIES_LAST_IMPORT');   

if (isset($_GET['key']) && $_GET['key'] == 'go') 
{

    $response = json_decode(file_get_contents("http://country.io/names.json"), true);

    $query = 'TRUNCATE TABLE countries';
    mysqli_query($connect, $query);

    $query = 'UPDATE settings SET 
        value = NOW() 
        WHERE name = "COUNTRIES_LAST_IMPORT" 
        LIMIT 1';
    mysqli_query($connect, $query);

    foreach($response as $country)
    {

        $query = 'INSERT INTO countries (
                country_code,
                long_name,
                created_at,
                updated_at
            ) VALUES (
                "'.array_search($country,$response).'",
                "'.$country.'",
                NOW(),
                NOW()
            )';
        mysqli_query($connect, $query);
    }
    
    message_set('Import Success', 'Countries list has been imported from Country.io.');
    header_redirect('/admin/stores/countries');

}


define('APP_NAME', 'Stores');

define('PAGE_TITLE', 'Import Countries');
define('PAGE_SELECTED_SECTION', 'admin-content');
define('PAGE_SELECTED_SUB_PAGE', '/admin/stores/countries');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT * 
    FROM countries 
    ORDER BY long_name';
$result = mysqli_query($connect, $query);

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
    <a href="/city/dashboard">Dashboard</a> / 
    <a href="/admin/stores/dashboard">Stores</a> / 
    Import Countries
</p>
<hr>

<h2>Import Countries</h2>

<p>
    There are currently 
    <span class="w3-tag w3-blue"><?=mysqli_num_rows($result)?></span> 
    countries imported from 
    <a href="http://country.io/">Country.io</a>.
</p>

<hr />

<p>
    Re-importimg the Countries from 
    <a href="http://country.io/">Country.io</a> will:
</p>

<ul class="w3-margin-bottom">
    <li>Delete the current Countries data.</li>
    <li>Re-import the Country data from <a href="http://country.io/">Country.io</a>.</li>
</ul>
            
<a
    href="/admin/stores/countries/go"
    class="w3-button w3-white w3-border"
    onclick="loading();"
>
    <i class="fa-solid fa-download"></i> Start Import
</a>

<?php

include('../templates/modal_city.php');

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
