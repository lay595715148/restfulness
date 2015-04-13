<?php
namespace Lay\Core;

use Lay\Core\Model;
use Lay\Core\Asynchronous;

abstract class ModelAsync extends Model implements Asynchronous {
    public function async($sign, $params = array()) {
        switch ($sign) {
            case 'get':
                break;
            case 'add':
                break;
            case 'del':
                break;
            case 'upd':
                break;
            case 'count':
                break;
            default:
                break;
        }
    }
}
// PHP END