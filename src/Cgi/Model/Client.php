<?php
namespace Lay\Cgi\Model;

use Lay\Core\Model;

class Client extends Model {
	public function properties() {
		return array(
			'id',
			'nick'
		);
	}
	public function get($id) {
		
	}
	public function add(array $info) {
		
	}
	public function del($id) {
		
	}
	public function upd($id, array $info) {
		
	}
    public function save() {
		
	}
    /**
     * 返回模型对应数据表名或其他数据库中的集合名称
     * @return string
     */
    public function table() {
		
	}
    /**
     * 返回模型属性名与对应数据表字段的映射关系数组
     * @return array
     */
    public function columns() {
		return array(
			'id' => 'id',
			'nick' => 'nick'
		);
	}
    /**
     * 返回模型属性名对应数据表主键字段名
     * @return array
     */
    public function primary() {
		
	}
    /**
     * 返回模型对应数据表所在数据库名
     * @return string
     */
    public function schema() {
		
	}
}
// PHP END