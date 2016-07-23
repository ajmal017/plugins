<?php
include '../../../wp-load.php';
$title =  $_GET['post_title'];
$post = array(
        'post_title'	=> $title,
        'post_content'	=> '',
        'post_status'	=> 'draft',
        'post_type'	=> 'post'
);
$ID = wp_insert_post($post); 
echo $ID;
?>
