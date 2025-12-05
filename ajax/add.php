<?php

if($_SERVER['REQUEST_METHOD'] == 'POST')
{

    if( !isset($_POST['name']) || 
        empty($_POST['name']) ||
        !isset($_POST['url']) || 
        empty($_POST['url']))
    {

        $data = array(
            'message' => 'Name and URL are required.',
            'error' => true,
        );

        return;

    }

    $query = 'SELECT *
        FROM stores
        WHERE slug = "'.$_POST['slug'].'"
        LIMIT 1';
    $result = mysqli_query($connect, $query);

    if(mysqli_num_rows($result))
    {

        $record = mysqli_fetch_assoc($result);

        $qery = 'UPDATE stores SET
            name = "'.$_POST['name'].'",
            phone = "'.$_POST['phone'].'",
            region = "'.$_POST['region'].'",
            url = "'.$_POST['url'].'",
            additional = "'.$_POST['additional'].'",
            certified = "'.($_POST['certified'] ? 1 : 0).'",
            new = "'.($_POST['new'] ? 1 : 0).'",
            soon = "'.($_POST['soon'] ? 1 : 0).'",
            store_id = "'.$_POST['store_id'].'",
            opening_at = '.($_POST['opening_at'] ? '"'.$_POST['opening_at'].'"' : 'NULL').',
            updated_at = NOW()
            WHERE id = '.$record['id'].'
            LIMIT 1';
        mysqli_query($connect, $qery); 

        $data = array(
            'message' => 'Store updated successfully.',
            'error' => false,
            'store' => $record,
        );

    }
    else
    {

        $query = 'SELECT *
            FROM countries
            WHERE country_code = "'.$_POST['country_code'].'"
            LIMIT 1';
        $result = mysqli_query($connect, $query);
        $record = mysqli_fetch_assoc($result);

        $query = 'INSERT INTO stores (
                name,
                phone,
                region,
                url,
                slug, 
                additional,
                certified,
                new,
                soon,
                country_id,
                store_id,
                opening_at,
                created_at,
                updated_at
            ) VALUES (
                "'.$_POST['name'].'",
                "'.$_POST['phone'].'",
                "'.$_POST['region'].'",
                "'.$_POST['url'].'",
                "'.$_POST['slug'].'",
                "'.$_POST['additional'].'",
                "'.($_POST['certified'] ? 1 : 0).'",
                "'.($_POST['new'] ? 1 : 0).'",
                "'.($_POST['soon'] ? 1 : 0).'",
                "'.$record['id'].'",
                "'.$_POST['store_id'].'",
                '.($_POST['opening_at'] ? '"'.$_POST['opening_at'].'"' : 'NULL').',
                NOW(),
                NOW()
            )';
        mysqli_query($connect, $query);

        $query = 'SELECT *
            FROM stores
            WHERE slug = "'.$_POST['slug'].'"
            LIMIT 1';
        $result = mysqli_query($connect, $query);
        $record = mysqli_fetch_assoc($result);
        
        $data = array(
            'message' => 'Store added successfully.',
            'error' => false,
            'email' => $record,
        );
    }

}
else
{

    $data = array(
        'message' => 'Invalid request method. POST required.',
        'error' => true,
    );

}