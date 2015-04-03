<?php
namespace Lay\Core;

class AbstractModel {
	protected $stores = array();
	public abstract function get($id, $options = array());
	public abstract function add(array $info, $options = array());
	public abstract function del($id, $options = array());
	public abstract function upd($id, array $info, $options = array());
    public function save() {
    	// TODO
        $pk = $this->primary();
        $data = $this->toArray();
        $data = array_filter($data, function ($var) {
            return $var !== null;
        });
        if ($this->$pk) {
            unset($data[$pk]);
            $this->getTable()->update($data, $this->$pk);
        } else {
            $last_id = $this->getTable()->insert($data);
            if ($last_id) {
                $this->$pk = $last_id;
            }
        }
    }
    /**
     * 返回模型对应数据表名或其他数据库中的集合名称
     * @return string
     */
    public abstract function table();
    /**
     * 返回模型属性名与对应数据表字段的映射关系数组
     * @return array
     */
    public abstract function columns();
    /**
     * 返回模型属性名对应数据表主键字段名
     * @return array
     */
    public abstract function primary();
    /**
     * 返回模型对应数据表所在数据库名
     * @return string
     */
    public abstract function schema();

}

// PHP END
