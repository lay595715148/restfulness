<?php
namespace core;

abstract class Service {
	use traits\Singleton;
    /**
     * 数据访问对象，为主表（或其他）数据模型的数据访问对象
     * 
     * @var Store
     */
    protected $store;
    /**
     * 构造方法
     * @param Store $store 数据库访问对象
     */
    protected function __construct($store = '') {
        //if($store && is_a($store, 'lay\core\Store')) {
        //    $this->store = $store;
        //}
        //PluginManager::exec(Service::H_CREATE, array($this));
        //EventEmitter::emit(Service::E_CREATE, array($this));
    }
    /**
     * 获取某条记录
     * 
     * @param int|string $id
     *            ID
     * @return array
     */
    public function get($id) {
        return $this->store->get($id);
    }
    /**
     * 增加一条记录
     * 
     * @param array $info
     *            数据数组
     * @return boolean
     */
    public function add(array $info) {
        return $this->store->add($info);
    }
    /**
     * 删除某条记录
     * 
     * @param int|string $id
     *            ID
     * @return boolean
     */
    public function del($id) {
        return $this->store->del($id);
    }
    /**
     * 更新某条记录
     * 
     * @param int|string $id
     *            ID
     * @param array $info
     *            数据数组
     * @return boolean
     */
    public function upd($id, array $info) {
        return $this->store->upd($id, $info);
    }
    /**
     * 某些条件下的记录数
     * 
     * @param array $info
     *            数据数组
     * @return int
     */
    public function count(array $info = array()) {
        return $this->store->count($info);
    }
}

// PHP END