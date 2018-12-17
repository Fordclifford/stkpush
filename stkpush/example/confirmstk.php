<?php

 $json= file_get_contents('php://input');
     //die(print_r($json));
         $post=  json_decode($json,TRUE);
         print_r($post);
         $file = fopen('response.json','w');
         fwrite($file, $json);