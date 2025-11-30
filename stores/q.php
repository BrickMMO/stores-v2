<?php

if(isset($_GET['key']))
{

    $q = string_url($_GET['key']);
    if($q != $_GET['key'])
    {
        header_redirect('/q/'.$q);
    }
 
}

// Get page number from URL if set
if(isset($_GET['page']) && is_numeric($_GET['page']))
{
    $current_page = (int)$_GET['page'];
}
else
{
    $current_page = 1;
}

define('APP_NAME', 'Stores');
define('PAGE_TITLE', 'Stores');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/main_header.php');
include('../templates/message.php');

    // Pagination setup
    $results_per_page = 24;
    $offset = ($current_page - 1) * $results_per_page;

    $where_clause = '';

    if(isset($q))
    {

        // Split search term by dashes
        $search_terms = explode('-', $q);
        
        // Build WHERE clause for multiple terms
        $where_conditions = [];
        foreach($search_terms as $term) 
        {

            $term = trim($term);

            if(!empty($term)) 
            {
                $term = mysqli_real_escape_string($connect, $term);
                $where_conditions[] = 'colours.rgb LIKE "%'.$term.'%"';
                $where_conditions[] = 'colours.name LIKE "%'.$term.'%"';
                $where_conditions[] = 'externals.name LIKE "%'.$term.'%"';
            }

        }
        
        // $where_clause .= 'WHERE ('.implode(' OR ', $where_conditions).')';

    }

    // Count total results
    $count_query = 'SELECT COUNT(DISTINCT stores.id) AS total
        FROM stores
        INNER JOIN countries
        ON countries.id = stores.country_id
        '.$where_clause;
    $count_result = mysqli_query($connect, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_results = $count_row['total'];
    $total_pages = ceil($total_results / $results_per_page);

    // Get paginated results
    $query = 'SELECT DISTINCT stores.name
        FROM stores 
        INNER JOIN countries
        ON countries.id = stores.country_id
        '.$where_clause.'
        GROUP BY stores.id
        ORDER BY stores.name DESC
        LIMIT '.$offset.', '.$results_per_page;
    $result = mysqli_query($connect, $query);

?>

<div class="w3-center">

    <h1>Stores</h1>

    <input 
        class="w3-input w3-border w3-margin-top w3-margin-bottom" 
        type="text" 
        value="<?=isset($_GET['key']) ? htmlspecialchars(str_replace('-', ' ', $_GET['key'])) : ''?>"
        placeholder="" 
        style="max-width: 300px; display: inline-block; box-sizing: border-box; vertical-align: middle;" 
        id="search-term">

    <a
        href="#"
        class="w3-button w3-white w3-border w3-margin-top w3-margin-bottom" 
        style="display: inline-block; box-sizing: border-box; vertical-align: middle;"
        id="search-button"
    >
        <i class="fa-solid fa-magnifying-glass"></i> Search
    </a>
    
</div>

<hr>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php
        $start_result = ($current_page - 1) * $results_per_page + 1;
        $end_result = min($current_page * $results_per_page, $total_results);
    ?>

    <p class="w3-center">Displaying <?=$start_result?>-<?=$end_result?> of <?=$total_results?> results</p>

    <div class="w3-flex" style="flex-wrap: wrap; gap: 16px; align-items: stretch;">

        <?php while ($display = mysqli_fetch_assoc($result)): ?>

            <div style="width: calc(33.3% - 16px); box-sizing: border-box; display: flex; flex-direction: column;">
                <div class="w3-card-4 w3-margin-top" style="max-width:100%; height: 100%; display: flex; flex-direction: column;">

                    <header class="w3-container w3-green">
                        <h4 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <i class="fa-${colour.is_trans == 'yes' ? 'regular' : 'solid'} fa-square"></i>
                            <?=$display['name']?>
                        </h4>
                    </header>

                    <div class="w3-margin">
                        <a href="/details/<?=$display['id']?>" style="position: relative; background-color: #<?=$display['rgb']?>; height: 100px; display: block;">
                            <?php if($display['is_trans'] == 'yes'): ?>
                                <span style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; background: url('https://cdn.brickmmo.com/images@1.0.0/trans-checkers.png') repeat; background-position: center; opacity: 0.5;"></span>
                            <?php endif; ?>
                        </a>
                        <div class="w3-container w3-center w3-margin-top">
                            <a href="#" onclick="return copy('#<?=$display['rgb']?>');" class="w3-button w3-white w3-border ">
                                <i class="fa-solid fa-copy"></i>
                                #<?=$display['rgb']?>
                            </a>
                            <a href="/details/<?=$display['id']?>" class="w3-button w3-white w3-border ">
                                <i class="fa-solid fa-circle-info"></i> Details
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        <?php endwhile; ?>

    </div>

<?php else: ?>

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Results Found</h3>
        <?php if(isset($q)): ?>
            <p>
                No results found for 
                <span class="w3-bold"><?=htmlspecialchars(str_replace('-', ' ', $q))?></span>.
            </p>
        <?php else: ?>
            <p>There are currently no stores available.</p>
        <?php endif; ?>
    </div>

<?php endif; ?>

<nav class="w3-text-center w3-section">

    <div class="w3-bar">            

        <?php
        
        // Display pagination links
        for ($i = 1; $i <= $total_pages; $i++) 
        {
            echo '<a href="'.ENV_DOMAIN.'/q';
            if($i > 1) echo '/page/'.$i;
            if(isset($q)) echo '/'.$q; 
            echo '" class="w3-button';
            if($i == $current_page) echo ' w3-border';
            echo '">'.$i.'</a>';
        }

        ?>

    </div>

</nav>

<script>

(function() {

    let searchButton = document.getElementById('search-button');

    searchButton.addEventListener('click', function(event) 
    {

        event.preventDefault();
        performSearch();

    });

    let searchTerm = document.getElementById('search-term');

    searchTerm.addEventListener('keypress', function(event) 
    {

        if (event.key === 'Enter') 
        {
            event.preventDefault();
            performSearch();
        }

    });

    function performSearch() 
    {

        let query = searchTerm.value.trim();

        // Remove anything that's not letters, numbers, or spaces
        query = query.replace(/[^a-zA-Z0-9\s]/g, '');
        // Replace spaces with hyphens
        query = query.replace(/\s+/g, '-');
        window.location.href = '/q/' + query;

    }

})();

</script>

<?php

include('../templates/main_footer.php');
include('../templates/html_footer.php');
