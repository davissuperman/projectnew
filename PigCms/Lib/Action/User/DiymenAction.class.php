<?php

class DiymenAction extends UserAction {

    //自定义菜单配置
    public function index() {
        if (IS_POST) {
            $class = M("Diymen_class")->where(array('token' => $_SESSION['token'], 'id' => $_POST['id']))->find();
            if ($class) {
                $class['id'] = $_POST['id'];
                $class['token'] = $_SESSION['token'];
                $class['title'] = $_POST['title'];
                $class['keyword'] = $_POST['keyword'];
                $class['url'] = htmlspecialchars_decode($_POST['url']);
                $class['is_show'] = $_POST['is_show'];
                if ($class['title'] == "" && ($class['keyword'] == "" || $class['url'] == "")) {
                    M('Diymen_class')->where(array('token' => session('token'), 'id' => $_POST['id']))->delete();
                } else {

                    M("Diymen_class")->save($class);
                }
                $this->success('保存成功', U('Diymen/index', array('token' => $this->token)));
                exit;
            } else {
                $count = M("Diymen_class")->where(array('token' => $_SESSION['token'], 'pid' => 0))->count(); //一级菜单数
                $where['token'] = $_SESSION['token'];
                $where['pid'] = array('neq', 0);
                $twocount = M("Diymen_class")->where($where)->count(); //二级菜单数量
                if ($count < 3 && $_POST['pid'] == 0) {
                    $_POST['url'] = htmlspecialchars_decode($_POST['url']);
                    $this->all_insert('Diymen_class', '/index');
                    exit;
                }
                if ($twocount < 15 && $_POST['pid'] != 0) {
                    $_POST['url'] = htmlspecialchars_decode($_POST['url']);
                    $this->all_insert('Diymen_class', '/index');
                }
                // $this->success('保存成功', U('Diymen/index', array('token' => $this->token)));
                exit;
            }
        } else {
            $this->assign('diymen', $data);
            $count = M('Diymen_class')->where(array('token' => session('token'), 'pid' => 0))->order('sort desc')->count();
            $page = new Page($count, 15);
            $show = $page->show();
            $class = M('Diymen_class')->where(array('token' => session('token'), 'pid' => 0))->order('sort desc')->limit($page->firstRow . ',' . $page->listRows)->select(); //dump($class);
            $classsize = count($class) ? count($class) : 0;
            if ($classsize < 3) {
                for ($classsize; $classsize < 3; $classsize++) {
                    $class[$classsize] = '';
                }
            }
            foreach ($class as $key => $vo) {
                $c = array();
                if ($vo['id']) {
                    $c = M('Diymen_class')->where(array('token' => session('token'), 'pid' => $vo['id']))->order('id desc')->select();
                }
                $size = count($c) ? count($c) : 0;
                if ($size < 5) {
                    $addd = array();
                    for ($size; $size < 5; $size++) {
                        $addd[] = '';
                    }
                    if ($c) {
                        $class[$key]['class'] = $c;
                        $class[$key]['class'] = array_merge($addd, $class[$key]['class']);
                    } else {
                        $class[$key]['class'] = $addd;
                    }
                } else {
                    $class[$key]['class'] = $c;
                }
            }
            $this->assign('class', $class);
            $this->Assign('page', $show);
            $this->display();
        }
    }

    /**
     * 授权设置 程云2014-4-7
     */
    public function set() {
        $data = M('Diymen_set')->where(array('token' => $_SESSION['token']))->find();
        if (IS_POST) {
            $_POST['token'] = $_SESSION['token'];
            if ($data == false) {
                M('Diymen_set')->data($_POST)->add();
                $this->success('添加成功', U('Diymen/set', array('token' => $this->token)));
            } else {
                $_POST['id'] = $data['id'];
                M('Diymen_set')->save($_POST);
                $this->success('修改成功', U('Diymen/set', array('token' => $this->token)));
            }
        } else {
            $this->assign('diymen', $data);
            $this->display();
        }
    }

    public function class_del() {
        $class = M('Diymen_class')->where(array('token' => session('token'), 'pid' => $this->_get('id')))->order('sort desc')->find();
        //echo M('Diymen_class')->getLastSql();exit;
        if ($class == false) {
            $back = M('Diymen_class')->where(array('token' => session('token'), 'id' => $this->_get('id')))->delete();
            if ($back == true) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('请删除该分类下的子分类');
        }
    }

    public function class_send() {
        if (IS_GET) {
            $api = M('Diymen_set')->where(array('token' => session('token')))->find();
            //dump($api);
            $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api['appid'] . '&secret=' . $api['appsecret'];
            $json = json_decode($this->curlGet($url_get));

            if ($api['appid'] == false || $api['appsecret'] == false) {
                $this->error('必须先填写【AppId】【 AppSecret】');
                exit;
            }

            $data = '{"button":[';
            $class = M('Diymen_class')->where(array('token' => session('token'), 'pid' => 0))->limit(3)->order('sort desc')->select();
            $kcount = M('Diymen_class')->where(array('token' => session('token'), 'pid' => 0))->limit(3)->order('sort desc')->count();
            $k = 1;
            foreach ($class as $key => $vo) {
                //主菜单
                $data.='{"name":"' . $vo['title'] . '",';
                $c = M('Diymen_class')->where(array('token' => session('token'), 'pid' => $vo['id']))->limit(5)->order('id desc')->select();
                $count = M('Diymen_class')->where(array('token' => session('token'), 'pid' => $vo['id']))->limit(5)->order('id desc')->count();
                //子菜单
                if ($c != false) {
                    $data.='"sub_button":[';
                } else {
                    if ($vo['url']) {
                        $data.='"type":"view","url":"' . $vo['url'].'&mid='.$vo['id'] . '"';
                    } else {
                        $data.='"type":"click","key":"' . $vo['keyword'] . '"';
                    }
                }
                $i = 1;
                foreach ($c as $voo) {
                    if ($i == $count) {
                        if ($voo['url']) {
                            $voo['url'] = str_replace("&amp;", "&", $voo['url']);
                            $data.='{"type":"view","name":"' . $voo['title'] . '","url":"' . $voo['url'].'&mid='.$voo['id'] . '"}';
                        } else {
                            $data.='{"type":"click","name":"' . $voo['title'] . '","key":"' . $voo['keyword'] . '"}';
                        }
                    } else {
                        if ($voo['url']) {
                            $voo['url'] = str_replace("&amp;", "&", $voo['url']);
                            $data.='{"type":"view","name":"' . $voo['title'] . '","url":"' . $voo['url'].'&mid='.$voo['id'] . '"},';
                        } else {
                            $data.='{"type":"click","name":"' . $voo['title'] . '","key":"' . $voo['keyword'] . '"},';
                        }
                    }
                    $i++;
                }
                if ($c != false) {
                    $data.=']';
                }

                if ($k == $kcount) {
                    $data.='}';
                } else {
                    $data.='},';
                }
                $k++;
            }
            $data.=']}';

            file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $json->access_token);

            $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $json->access_token;
            if ($this->api_notice_increment($url, $data) == false) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功');
            }
            exit;
        } else {
            $this->error('非法操作');
        }
    }

    function api_notice_increment($url, $data) {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);


        if (curl_errno($ch)) {
            return false;
        } else {

            return true;
        }
    }

    function curlGet($url) {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }

}

?>