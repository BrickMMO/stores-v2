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
    header_redirect('/admin/import/countries');
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




// $query = 'TRUNCATE stores';
// mysqli_query($connect, $query);

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
    <a href="/admin/dashboard">Stores</a> / 
    Import Stores
</p>

<hr>

<h2>Import Stores</h2>

<p>
    Importing Stores:
    <span class="w3-tag w3-blue" id="store-count">0/0</span>
    Importing from: 
    <a href="https://www.lego.com/en-ca/stores">LEGO® Store</a>.
</p>

<hr />

<div class="w3-light-grey w3-margin-bottom">
    <div class="w3-container w3-green w3-padding w3-center" style="width:0%; min-width: 50px" id="progress">0%</div>
</div>

<div class="w3-container w3-border w3-padding-16 w3-margin-bottom" id="loading" style="max-height: 500px; overflow: scroll; display: none;">
    <h3>
        <i class="fa-solid fa-spinner fa-spin"></i>
        Loading...
    </h3>
</div>

<a href="#" onclick="startScan();" class="w3-button w3-margin-bottom w3-border">
    <i class="fa-solid fa-cloud-download-alt fa-padding-right"></i>
    Start Import
</a> 

<script>

    async function fetchStores() {
        
        /*
        const url = 'https://www.lego.com/api/graphql/StoresDirectory';

        const query = `
            query StoresDirectoryQuery {
                storesDirectory {
                    id
                    country
                    region
                    stores {
                    storeId
                    name
                    phone
                    state
                    openingDate
                    certified
                    additionalInfo
                    storeUrl
                    urlKey
                    isNewStore
                    isComingSoon
                    __typename
                    }
                    __typename
                }
            }`;

        const requestBody = {
            query: query
        };

        const headers = {
            // Required by GraphQL standard
            'Content-Type': 'application/json', 
            // Recommended to bypass security/CSRF checks (matches the PHP fix)
            'x-apollo-operation-name': 'StoresDirectoryQuery',
            // Spoofing a User-Agent is less effective in a browser but good practice
            // Browsers automatically handle cookies and other security checks
        };
        */

        let url = '/api/stores.json';
        let response = await fetch(url);
        let json = await response.json();
        let stores = [];    

        const storesData = json.data.storesDirectory
            
        storesData.forEach(country => {

            country.stores.forEach(store => {

                stores.push({
                    country_code: country.country,
                    region: country.region,
                    store_id: store.storeId,
                    name: store.name,
                    phone: store.phone,
                    state: store.state,
                    opening_at: store.openingDate,
                    certified: store.certified,
                    additional: store.additionalInfo,
                    url: store.storeUrl,
                    slug: store.urlKey,
                    new: store.isNewStore,
                    soon: store.isComingSoon
                });

            });

        });

        return stores;
        
    }

    

    function startScan()
    {

        let loading = document.getElementById('loading');
        loading.style.display = "block";

        scanStores();
        
    }

    async function scanStores() {

        let loading = document.getElementById('loading');
        let progress = document.getElementById('progress');
        let storeCount = document.getElementById('store-count');

        let totalStores = 0;
        let countProgress = 0;
    
        const stores = await fetchStores();
        
        storeCount.innerHTML = '0/'+stores.length;

        for(let i = 0; i < stores.length; i++)
        {

            let percent = Math.round(((countProgress+1) / stores.length) * 100)+'%';

            progress.innerHTML = percent;
            progress.style.width = percent;

            storeCount.innerHTML = (countProgress+1)+'/'+stores.length;

            if(i == 0 ) loading.innerHTML = '';

            let div = document.createElement('div');

            let h3 = document.createElement('h3');
            div.append(h3);

            let h3Text = document.createTextNode(stores[i].name);
            h3.append(h3Text);

            if(stores[i].url == 'store.default.url')
            {
                stores[i].url = "https://www.lego.com/stores/stores/" + stores[i].key;
            }
                
            let link = document.createElement('p');
            link.innerHTML = '<a href="' + stores[i].url + 
                '">' + stores[i].url + '</a>';
            div.append(link);

            let hr = document.createElement('hr');
            div.append(hr);

            loading.prepend(div);

            console.log(stores[i]);

            await fetch('/ajax/add',{
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(stores[i])
            });

            countProgress++;
            
            await new Promise(resolve => setTimeout(resolve, 1000));

        }     
    }

</script>

    
<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
