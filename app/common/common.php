<?php

function tojson($data="",$code='',$msg=''){
        if(empty($data)){
            return json([
                'code' => -1,
                 'msg' => '请求参数错误'
            ]); 
        }
        return json([
            'code' => 0,
            'msg' => $msg,
            'data' => $data
        ]);
    }