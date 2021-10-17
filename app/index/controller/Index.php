<?php

namespace app\index\controller;

use think\Controller;
use app\admin\model\ArticleM as ArtM;
use app\admin\model\ArticleC as ArtC;
use think\Db;
use think\Session;

class Index extends Controller
{
  public function index()
  {

    //$str="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJKb3V6ZXl1IiwiYXVkIjoiIiwiaWF0IjoxNjM0Mjk2NDAxLCJuYmYiOjE2MzQyOTY0MDQsImV4cCI6MTAwMzQzNTA3MjYsInVpZCI6ImFkbWluIn0.wdYQWd8RxEWd_ZR9IxF0B9WVmVbFprOABu3z2dLab3M";
    $id = Request()->param('id');

    Session::set('token', createToken($id));
    $user = new \app\jk\controller\Login();
    return $user;
  }


  public function news()
  {
    $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 2;
    $nid = $this->request->has('nid') ? $this->request->param('nid', 0, 'intval') : false;
    //dump($nid);die; 
    $artm = new ArtM();
    $artc = new ArtC();
    if (!$nid) {
      $cates = $artc->all();
      $data = $artm->all(['article_cate_id' => $id]);
      foreach ($data as $tmp) {
        $sql = "select filepath from tplay_attachment where id='" . $tmp['thumb'] . "'";
        $imgpath = Db::query($sql);
        $tmp['imgpath'] = $imgpath[0]['filepath'];
      }


      $this->assign('cates', $cates);
      $this->assign('data', $data);
      return $this->fetch();
    } else {
      $cates = $artc->all();
      $art = $artm->get(['id' => $nid]);
      $this->assign('art', $art);
      $this->assign('cates', $cates);
      return $this->fetch();
    }
  }
}
