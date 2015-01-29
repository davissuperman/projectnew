<?php

class CompanyAction extends UserAction {

    public $token;
    public $isBranch;
    public $company_model;

    public function _initialize() {
        parent::_initialize();
        $this->token = session('token');
        $this->assign('token', $this->token);
        //权限
        if ($this->token != $_GET['token']) {
            exit();
        }
        //是否是分店
        $this->isBranch = 0;
        if (isset($_GET['isBranch']) && intval($_GET['isBranch'])) {
            $this->isBranch = 1;
        }
        $this->assign('isBranch', $this->isBranch);
        //
        $this->company_model = M('Company');
    }

    public function index() {
        $where = array('token' => $this->token);
        if ($this->isBranch) {
            $id = intval($_GET['id']);
            $where['id'] = $id;
            $where['isbranch'] = 1;
        } else {
            $where['isbranch'] = 0;
        }
        $thisCompany = $this->company_model->where($where)->find();
        if (IS_POST) {
            if (!$thisCompany) {
                if ($this->isBranch) {
                    $this->insert('Company', U('Company/branches', array('token' => $this->token, 'isBranch' => $this->isBranch)));
                } else {
                    $this->insert('Company', U('Company/index', array('token' => $this->token, 'isBranch' => $this->isBranch)));
                }
            } else {
                if ($this->company_model->create()) {
                    if ($this->company_model->where($where)->save($_POST)) {
                        if ($this->isBranch) {
                            $this->success('修改成功', U('Company/branches', array('token' => $this->token, 'isBranch' => $this->isBranch)));
                        } else {
                            $this->success('修改成功', U('Company/index', array('token' => $this->token, 'isBranch' => $this->isBranch)));
                        }
                    } else {
                        $this->error('操作失败');
                    }
                } else {
                    $this->error($this->company_model->getError());
                }
            }
        } else {
            $this->assign('set', $thisCompany);
            $this->display();
        }
    }

    public function branches() {
        if (IS_POST) {
            $delc = 0;
            array_pop($_POST); //把最后的那个哈希数据删除
            foreach ($_POST as $v) {
                $rr = $this->company_model->where(array('id' => $v))->delete();
                if ($rr) {
                    $delc++;
                }
            }
            $this->success("删除成功{$delc}条店铺数据", U('Company/branches', array('isbranch' => 1, 'token' => $this->token)));
        } else {
            $count = $this->company_model->where(array('isbranch' => 1, 'token' => $this->token))->count();
            $Page = new Page($count, 10);
            $show = $Page->show();
            $branches = $this->company_model->where(array('isbranch' => 1, 'token' => $this->token))->order('taxis ASC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('branches', $branches);
            $this->assign('page', $show);
            $this->display();
        }
    }

    public function delete() {
        $where = array('token' => $this->token, 'isbranch' => 1, 'id' => intval($_GET['id']));
        $rt = $this->company_model->where($where)->delete();
        if ($rt == true) {
            $this->success('删除成功', U('Company/branches', array('token' => $this->token, 'isBranch' => 1)));
        } else {
            $this->error('服务器繁忙,请稍后再试', U('Company/branches', array('token' => $this->token, 'isBranch' => 1)));
        }
    }

    /**
     * 加载导入分支机构表单
     * add by wuhaiyan 2014/3/25
     */
    public function import() {
        $this->display();
    }

    /**
     * 执行分支机构导入
     * add by wuhaiyan 2014/3/25
     */
    public function doImport() {
        if (empty($_FILES['company_data']) || strlen($_FILES['company_data']['tmp_name']) < 1) {
            $this->error('没有可上传的文件');
        }
        setlocale(LC_ALL, 'en_US.UTF-8');
        setlocale(LC_ALL, 'zh_CN'); //把信息转化成中文简体，必须加上，不然后中文解析失败

        $postfile = $_FILES['company_data'];
        $fileType = strrchr($postfile['name'], '.'); //判断文件格式并作拦截
        if ($fileType != '.csv') {
            $this->error('只允许上传.csv格式的数据');
        }
        $result = $this->doCsvImport($postfile); //csv数据上传、解析
        $len_result = count($result, 0);

        //获取文件编码
        $fileEncode = $this->getEncode($result[0][0]);
        if ($fileEncode == '文件编码非法') {
            $this->error('文件编码非法');
        }
        $m = M('Company');
        $successInsert = 0;
        $cData = array();
        $cData['token'] = $this->token;
        for ($i = 1; $i < $len_result; $i++) {
            //对数据去空格
            $result[$i][0] = trim($result[$i][0]);
            $result[$i][1] = trim($result[$i][1]);
            $result[$i][2] = trim($result[$i][2]);
            $result[$i][3] = trim($result[$i][3]);
            $result[$i][4] = trim($result[$i][4]);
            $result[$i][5] = trim($result[$i][5]);
            $result[$i][6] = trim($result[$i][6]);
            $cData['name'] = iconv($fileEncode, 'utf-8', $result[$i][0]); //中文转码
            $exists = $m->where(array('name' => $cData['name'], 'token' => $this->token))->find();
            if ($exists) {
                continue;
            }
            $cData['address'] = iconv($fileEncode, 'utf-8', $result[$i][1]);
            $cData['longitude'] = $result[$i][2];
            $cData['latitude'] = $result[$i][3];
            $cData['tel'] = $result[$i][4];
            $cData['isbranch'] = 1;
            $cData['active'] = iconv($fileEncode, 'utf-8', $result[$i][5]);
            $cData['logourl'] = $result[$i][6];
            $rr = $m->add($cData);
            if ($rr) {
                $successInsert++;
            }
        }
        if ($successInsert > 0) {
            $this->success("成功导入{$successInsert}条店铺数据", U('Company/branches', array('mark' => 1, 'token' => $this->token, 'isBranch' => 1)));
        } else {
            $this->error("导入失败!请检查数据格式！");
        }
    }

    /**
     * 实体卡导入数据获得 并进行各种错误判断、编码处理
     * add by wu 3/12
     * @param  array $postfile 上传文件表单即$_FILES['card_data'];
     * @return 导入文件的编码
     */
    public function doCsvImport($postfile) {
        //判断文件上传是否成功
        $ferror = $postfile['error'];
        switch ($ferror) {
            case 1:
                $this->error('文件大小超出了服务器的空间大小');
                break;
            case 2:
                $this->error('要上传的文件大小超出浏览器限制');
                break;
            case 3:
                $this->error('文件仅部分被上传 ');
                break;
            case 4:
                $this->error('没有找到要上传的文件');
                break;
            case 5:
                $this->error('服务器临时文件夹丢失');
                break;
            case 6:
                $this->error('文件写入到临时文件夹出错');
                break;
        }

        $filename = $postfile['tmp_name'];
        $handle = fopen($filename, 'r');
        if ($handle == false) {
            $this->error('文件打开失败');
        }
        //解析csv       
        $result = $this->input_csv($handle);
        if (count($result) == 0) {
            $this->error('没有任何数据！');
        }
        return $result;
    }

    /**
     * csv文件解析
     * add by wu 3/12
     * @param  string $handle csv文件流
     * @return array  $out 解析出来的文件内容
     */
    public function input_csv($handle) {
        $out = array();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }

    /**
     * 判断字符串文件编码
     * add by wu 3/12
     * @param  string $string 要检测的字符串
     * @return string $code 编码类型
     */
    public function getEncode($string) {
        if ($string === iconv('UTF-8', 'UTF-8', iconv('UTF-8', 'UTF-8', $string))) {
            return 'UTF-8';
        } elseif ($string === iconv('UTF-8', 'ASCII', iconv('ASCII', 'UTF-8', $string))) {
            return 'ASCII';
        } elseif ($string === iconv('UTF-8', 'GB2312', iconv('GB2312', 'UTF-8', $string))) {
            return 'GB2312';
        } else {
            return '文件编码非法';
        }
    }

    /**
     * 判断字符串文件编码
     * add by wu 3/26
     */
    public function edit() {
        $active = trim($_POST['active']);
        $branches = trim($_POST['branches'], ',');
        $branchArr = explode(',', $branches);
        for ($i = 0; $i < count($branchArr); $i++) {
            $where['id'] = $branchArr[$i];
            $data['active'] = $active;
            $this->company_model->where($where['id'])->save($data);
        }
        echo '修改成功！';
    }

}

?>