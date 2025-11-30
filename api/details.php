<?php

if(!isset($_GET['key']) || !is_numeric($_GET['key']))
{

    $data = array('message' => 'No colour ID specified.', 'error' => true);
    return;

}

$query = 'SELECT * 
    FROM colours 
    WHERE id = "'.$_GET['key'].'"
    LIMIT 1'; 
$result = mysqli_query($connect, $query);

if(mysqli_num_rows($result))
{

    $colour = mysqli_fetch_assoc($result);

    $query = 'SELECT *
        FROM externals
        WHERE colour_id = "'.$colour['id'].'"';
    $result = mysqli_query($connect, $query);

    $externals = array();

    while($external = mysqli_fetch_assoc($result))
    {

        $externals[]= $external;
        
    }

    $colour['externals'] = $externals;

    $data = array(
        'message' => 'Colour details retrieved successfully.',
        'error' => false, 
        'colour' => $colour,
    );
    
}
else 
{

    $data = array(
        'message' => 'Error retrieving colour details.',
        'error' => true,
    );

}