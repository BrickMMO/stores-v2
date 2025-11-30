<?php

$query = 'SELECT * 
    FROM colours 
    ORDER BY name'; 
$result = mysqli_query($connect, $query);

$colours = array();

if(mysqli_num_rows($result))
{

    while($colour = mysqli_fetch_assoc($result))
    {

        $colours[]= $colour;
        
    }

    $data = array(
        'message' => 'Colours retrieved successfully.',
        'error' => false, 
        'colours' => $colours,
    );
    
}
else 
{

    $data = array(
        'message' => 'Error retrieving colours detail.',
        'error' => true,
        'colours' => null,
    );

}