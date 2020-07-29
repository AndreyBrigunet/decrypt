<?php

function pr($data, $type = 0) {
    print '<pre>';
    print_r($data);
    print '</pre>';
    if ($type != 0) {
        exit();
    }
}

function get_processes() {
    $get = @file_get_contents('https://tg.brigu.net/api/numberofprocesses');
    $get = json_decode($get);
    
    if(is_object($get)){
       return (int)$get->count;
    }
    return 0;
}

function set_status($id) {
    $get = @file_get_contents('https://tg.brigu.net/api/proces/' . $id );
    $get = json_decode($get);
    
    if(is_object($get)){
       return (bool)$get->status;
    }

    return false;
}