<?php

if(!isset($_GET['key']) || !is_numeric($_GET['key']))
{

    $data = array('message' => 'No matching data specified.', 'error' => true);
    return;

}
elseif(!is_colour_hex($_GET['key']))
{

    $data = array('message' => 'Matching data is not a valid colour.', 'error' => true);
    return;

}

$query = 'SELECT * 
    FROM colours 
    ORDER BY name'; 
$result = mysqli_query($connect, $query);

$colours = array();

if(mysqli_num_rows($result))
{

    while($colour = mysqli_fetch_assoc($result))
    {

        $colour['distance'] = colour_distance($colour['rgb'], $_GET['key']);

        if($colour['distance'] < 0.25)
        {
            $colours[]= $colour;
        }
        
    }

}

usort($colours, function($first, $second) {
    if ($first['distance'] == $second['distance']) return 0;
    return ($first['distance'] < $second['distance']) ? -1 : 1;
});

if(count($colours))
{

    $data = array(
        'message' => 'Close colours retrieved successfully.',
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