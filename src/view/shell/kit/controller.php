{{?php

/**
 * 自动生成控制器
 * User: echosong
 * Date: <?=date('Y-m-d H:i:s').PHP_EOL?>
 */
class <?=ucfirst($tableName)?>Controller extends BaseController
{
    /**
    * 列表分页显示
    **/
    public function getList()
    {
       $where = [];
        //todo
        $<?=$tableName?>Db = new <?=ucfirst($tableName)?>();
        $page = Helper::request('page',1);
        $this-><?=$tableName?>s = $<?=$tableName?>Db->findAll($where, 'id desc', '*', [$page, $this->_config['admin']['page']]);
        $this->page = $this->pager($<?=$tableName?>Db->page, $where);
        $this->display();
    }

    /*
     * 获取表单
     */
    public function getForm()
    {
        $id = Helper::request('id', '-1');
        $<?=$tableName?>Db = new <?=ucfirst($tableName)?>();
        if ($id != '-1') {
            $where['id'] = $id;
            $<?=$tableName?>Db->find($where, 'id desc', '*');
        }
        $this-><?=$tableName?> =  $<?=$tableName?>Db;
        $this->display();
    }

    /*
     * 设置表提交
     */
    public function postForm()
    {
        $v = App::validator($_POST);
        $v->mapFieldsRules(<?=ucfirst($tableName)?>::$rules);
        if (!$v->validate()) {
            $errors = (array)$v->errors();
            Helper::responseJson($errors, 1);
        }
        $id = Helper::request('id', '');
        $<?=$tableName?>Db = new <?=ucfirst($tableName)?>();
        if (is_numeric($id)) {
            $<?=$tableName?>Db->update(['id' => $id], $_POST);
            Helper::responseJson('操作成功！', 0);
        } else {
            unset($_POST['id']);
            $count = $<?=$tableName?>Db->create($_POST);
            if ($count == 0) {
                Helper::responseJson('操作失败！', 1);
            } else {
                Helper::responseJson('操作成功！', 0);
            }
        }
    }

    /**
     * 删除
     */
    public function postDelete()
    {
        $id = Helper::request('id', '0');
        if (count($id) > 0) {
            $<?=$tableName?>Db =  new <?=ucfirst($tableName)?>();
            $<?=$tableName?>Db->delete(['id' => $id]);
            Helper::responseJson('删除成功！', 0);
        } else {
            Helper::responseJson('请选择要删除的数据！', 3);
        }
    }
}