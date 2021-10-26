<?php

function tojson($data="",$code='',$msg=''){
        if(empty($data)){
            return json([
                'code' => $code,
                 'msg' => $msg
            ]); 
        }
        return json([
            'code' => 0,
            'msg' => $msg,
            'data' => $data
        ]);
    }