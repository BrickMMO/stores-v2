<?php

security_check();
admin_check();

$query = 'SELECT * 
    FROM countries 
    ORDER BY long_name';
$result = mysqli_query($connect, $query);

if(!mysqli_num_rows($result))
{

    message_set('Import Error', 'Import countries before importing stores.', 'red');
    header_redirect('/admin/stores/dashboard');
}

define('APP_NAME', 'Stores');

define('PAGE_TITLE', 'Import Stores');
define('PAGE_SELECTED_SECTION', 'admin-content');
define('PAGE_SELECTED_SUB_PAGE', '/admin/stores/import');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_slideout.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'TRUNCATE stores';
mysqli_query($connect, $query);

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
    <a href="/city/dashboard">Dashboard</a> / 
    <a href="/admin/stores/dashboard">Stores</a> / 
    Import Stores
</p>
<hr />
<h2>Importing Stores</h2>

<p>
    Total Countries:
    <span class="w3-tag w3-blue" id="country-count">0</span>
    Importing Stores:
    <span class="w3-tag w3-blue" id="store-count">0/0</span>
    Importing from: 
    <a href="https://www.lego.com/en-ca/stores">LEGO® Store</a>.
</p>

<hr />

<div class="w3-light-grey w3-margin-bottom">
    <div class="w3-container w3-green w3-padding w3-center" style="width:0%; min-width: 50px" id="progress">0%</div>
</div>

<div class="w3-container w3-border w3-padding-16 w3-margin-bottom" id="loading" style="max-height: 500px; overflow: scroll">
    <h3>
        <i class="fa-solid fa-spinner fa-spin"></i>
        Loading...
    </h3>
</div>

<script>

    async function fetchStores() {
        return fetch('/ajax/lego/stores',{
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                }
            })  
            .then((response)=>response.json())
            .then((responseJson)=>{return responseJson});
    }

    async function scanStore(urlKey) {
        return fetch('/ajax/lego/store/scan/urlKey/'+urlKey,{
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                }
            })  
            .then((response)=>response.json())
            .then((responseJson)=>{return responseJson});
    }

    async function scanStores() {

        let loading = document.getElementById('loading');
        let progress = document.getElementById('progress');
        let countryCount = document.getElementById('country-count');
        let storeCount = document.getElementById('store-count');

        let totalStores = 0;
        let countProgress = 0;
    
        const resultStore = await fetchStores();

        let countCountry = resultStore.stores.data.storesDirectory;

        countryCount.innerHTML = countCountry.length;

        for(let i = 0; i < countCountry.length; i++){
            totalStores = totalStores + countCountry[i].stores.length;            
        }
        
        storeCount.innerHTML = '0/'+totalStores;

        for(let i = 0; i < countCountry.length; i++)
        {
            for(let j = 0; j < countCountry[i].stores.length; j++){
                let percent = Math.round(((countProgress+1) / totalStores) * 100)+'%';

                progress.innerHTML = percent;
                progress.style.width = percent;

                storeCount.innerHTML = (countProgress+1)+'/'+totalStores;

                const storeInfo = await scanStore(countCountry[i].stores[j].urlKey);

                const detailsStore = storeInfo.storeInfo.data.storeInfo;

                if(i == 0 && j == 0) loading.innerHTML = '';

                let div = document.createElement('div');

                let h3 = document.createElement('h3');
                div.append(h3);

                let h3Text = document.createTextNode(countCountry[i].stores[j].name);
                h3.append(h3Text);

                // let country = document.createElement('p');
                // country.innerHTML = '<strong>Country: </strong>' + detailsStore.country;
                // div.append(country);

                // let city = document.createElement('p');
                // city.innerHTML = '<strong>City: </strong>' + detailsStore.city;
                // div.append(city);

                let urlKey = document.createElement('p');
                urlKey.innerHTML = '<a href="' + detailsStore.storeUrl + 
                    '">' + detailsStore.storeUrl + '</a>';
                div.append(urlKey);

                console.log(detailsStore);
                let hr = document.createElement('hr');
                div.append(hr);

                loading.prepend(div);

                countProgress++;
                
                await new Promise(resolve => setTimeout(resolve, 0));
            }
        }     
    }

    scanStores();

</script>

    
<?php

include('../templates/modal_city.php');

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
