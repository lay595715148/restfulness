<?php
namespace Lay\Core;

use ArrayAccess;
use Iterator;
use JsonSerializable;

interface Beanizable extends JsonSerializable {
    /**
     * 返回对象属性名对属性值的数组
     * @return array
     */
	public function toArray();
    /**
     * 返回对象转换为stdClass后的对象
     * @return stdClass
     */
    public function toStandard();
    /**
     * 清空对象所有属性值
     * @return Beanizable
     */
    public function restore();
}
// PHP END