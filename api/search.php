<?php

if(!isset($_GET['key']))
{
    $_GET['key'] = '';
}

$query = 'SELECT * 
    FROM colours 
    '.($_GET['key'] ? 'WHERE rgb LIKE "%'.mysqli_real_escape_string($connect, $_GET['key']).'%"
        OR name LIKE "%'.mysqli_real_escape_string($connect, $_GET['key']).'%"' : '' ).'
    ORDER BY name'; 
$result = mysqli_query($connect, $query);

if(mysqli_num_rows($result))
{

    $colours = array();

    while($colour = mysqli_fetch_assoc($result))
    {

        $colours[]= $colour;
        
    }

    $data = array(
        'message' => 'Search colours retrieved successfully.',
        'error' => false, 
        'colours' => $colours,
    );

}
else
{

    $data = array(
        'message' => 'No matching colours found.',
        'error' => false, 
    );

}