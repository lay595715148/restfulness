<?php
namespace Lay\DB;

use Lay\Core\Model;

abstract class DataBase {
	protected $model;
    /**
     * 设置模型对象
     * @param Model $model 模型对象
     * @return void
     */
    public final function setModel(Model $model) {
        $this->model = $model;
    }
    /**
     * 获取模型对象
     * @return Model
     */
    public final function getModel() {
        return $this->model;
    }
    /**
     * 连接数据库
     * @return boolean
     */
    public abstract function connect();
    /**
     * 获取某条记录
     * 
     * @param int|string $id
     *            ID
     * @return array
     */
    public abstract function get($id);
    /**
     * 删除某条记录
     * 
     * @param int|string $id
     *            ID
     * @return boolean
     */
    public abstract function del($id);
    /**
     * 增加一条记录
     * 
     * @param array $info
     *            数据数组
     * @return boolean
     */
    public abstract function add(array $info);
    
    /**
     * 更新某条记录
     * 
     * @param int|string $id
     *            ID
     * @param array $info
     *            数据数组
     * @return boolean
     */
    public abstract function upd($id, array $info);
    
    /**
     * 某些条件下的记录数
     * 
     * @param array $info
     *            数据数组
     * @return int
     */
    public abstract function count(array $info = array());
    /**
     * close connection
     * @return boolean
     */
    public abstract function close();
}
// PHP END