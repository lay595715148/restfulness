<?php
namespace Lay\DB;

use Lay\Core\Model;
use Lay\DB\CRUDable;
use Lay\DB\Mysql;
use Lay\DB\Mongo;
use Lay\DB\Redis;
use Lay\DB\Memcache;
use Lay\Traits\Singleton;

abstract class DataBase implements CRUDable {
    use Singleton;
    public static function factory($name) {
        switch ($name) {
            case 'memcache':
            case 'memcached':
                return Memcache::getInstance();
                break;
            case 'redis':
                return Redis::getInstance();
                break;
            case 'mongodb':
            case 'mongo':
                return Mongo::getInstance();
                break;
            case 'mysql':
            default:
                return Mysql::getInstance();
                break;
        }
    }

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
     * close connection
     * @return boolean
     */
    public abstract function close();
}
// PHP END