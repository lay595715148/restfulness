<?php
namespace Lay\Util;

/**
 * 工具类
 *
 * @author Lay Li
 */
class Utility {
    /**
     * 服务器系统是不是Windows
     *
     * @var boolean
     */
    private static $_is_windows = false;
    /**
     * 判断服务器系统是不是Windows
     *
     * @return boolean
     */
    public static function isWindows() {
        if(! is_bool(self::$_is_windows)) {
            self::$_is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        }
        return self::$_is_windows;
    }
    /**
     * 判断是不是绝对路径
     *
     * @param string $path            
     * @return boolean
     */
    public static function isAbsolutePath($path) {
        return false;
    }
    /**
     * 转变为纯粹的数组
     *
     * @param array $arr            
     * @return array
     */
    public static function toPureArray($arr) {
        if(is_array($arr) || is_object($arr)) {
            $tmp = array();
            foreach($arr as $i => $a) {
                $tmp[] = $a;
            }
            return $tmp;
        } else if(! is_resource($arr)) {
            return (array)$arr;
        } else {
            return $arr;
        }
    }
    /**
     * 判断是不是纯粹的数组
     *
     * @param array $arr            
     * @return boolean
     */
    public static function isPureArray($arr) {
        $bool = true;
        if(is_array($arr)) {
            foreach($arr as $i => $a) {
                if(is_string($i) || ! is_int($i)) {
                    $bool = false;
                    break;
                }
            }
        } else {
            $bool = false;
        }
        return $bool;
    }
    /**
     * 计算有没有下页
     *
     * @param int $total            
     * @param int $offset            
     * @param int $num            
     * @return boolean
     */
    public static function hasNext($total, $offset = -1, $num = -1) {
        if($offset == - 1 || $num == - 1) {
            return false;
        } else if($total > $offset + $num) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 获取代微秒数的当前时间
     *
     * @param string $format            
     * @return string number
     */
    public static function microtime($format = false) {
        if($format) {
            return date($format) . substr((string)microtime(), 1, 8);
        } else {
            return time() + substr((string)microtime(), 1, 8);
        }
    }
    /**
     * php array to php content
     *
     * @param array $arr
     *            convert array
     * @param boolean $encrypt
     *            if encrypt
     * @return string
     */
    public static function array2PHPContent($arr, $encrypt = false) {
        if($encrypt) {
            $r = '';
            $r .= self::array2String($arr);
        } else {
            $r = "<?php";
            $r .= "\r\nreturn ";
            self::a2s($r, $arr);
            $r .= ";\r\n// PHP END\r\n";
        }
        return $r;
    }
    /**
     * convert a multidimensional array to url save and encoded string
     *
     * 在Array和String类型之间转换，转换为字符串的数组可以直接在URL上传递
     *
     * @param array $Array
     *            convert array
     * @return string
     */
    public static function array2String($Array) {
        $Return = '';
        $NullValue = "^^^";
        foreach($Array as $Key => $Value) {
            if(is_array($Value))
                $ReturnValue = '^^array^' . self::array2String($Value);
            else
                $ReturnValue = (strlen($Value) > 0) ? $Value : $NullValue;
            $Return .= urlencode(base64_encode($Key)) . '|' . urlencode(base64_encode($ReturnValue)) . '||';
        }
        return urlencode(substr($Return, 0, - 2));
    }
    /**
     * convert a string generated with Array2String() back to the original (multidimensional) array
     *
     * @param string $String
     *            convert string
     * @return array
     */
    public static function string2Array($String) {
        $Return = array();
        $String = urldecode($String);
        $TempArray = explode('||', $String);
        $NullValue = urlencode(base64_encode("^^^"));
        foreach($TempArray as $TempValue) {
            list($Key, $Value) = explode('|', $TempValue);
            $DecodedKey = base64_decode(urldecode($Key));
            if($Value != $NullValue) {
                $ReturnValue = base64_decode(urldecode($Value));
                if(substr($ReturnValue, 0, 8) == '^^array^')
                    $ReturnValue = self::string2Array(substr($ReturnValue, 8));
                $Return[$DecodedKey] = $ReturnValue;
            } else {
                $Return[$DecodedKey] = NULL;
            }
        }
        return $Return;
    }
    /**
     * array $a to string $r
     *
     * @param string $r
     *            output string pointer address
     * @param array $a
     *            input array pointer address
     * @param array $l
     *            左则制表字符串
     * @param array $b
     *            左则制表字符串单元
     * @return void
     */
    public static function a2s(&$r, array &$a, $l = "", $b = "    ") {
        $f = false;
        $h = false;
        $i = 0;
        $r .= 'array(' . "\r\n";
        foreach($a as $k => $v) {
            if(! $h)
                $h = array(
                        'k' => $k,
                        'v' => $v
                );
            if($f)
                $r .= ',' . "\r\n";
            $j = ! is_string($k) && is_numeric($k) && $h['k'] === 0;
            self::o2s($r, $k, $v, $i, $j, $l, $b);
            $f = true;
            if($j && $k >= $i)
                $i = $k + 1;
        }
        $r .= "\r\n$l" . ')';
    }
    /**
     * to string $r
     *
     * @param string $r
     *            output string pointer address
     * @param string $k
     *            键名
     * @param string $v
     *            键值
     * @param string $i            
     * @param string $j            
     * @param array $l
     *            左则制表字符串
     * @param array $b
     *            左则制表字符串单元
     * @return void
     */
    private static function o2s(&$r, $k, $v, $i, $j, $l, $b) {
        $isW = self::isWindows();
        if($k !== $i) {
            if($j)
                $r .= "$l$b$k => ";
            else
                $r .= "$l$b'$k' => ";
        } else {
            $r .= "$l$b";
        }
        if(is_array($v))
            self::a2s($r, $v, $l . $b);
        else if(is_numeric($v))
            $r .= "" . $v;
        else
            $r .= "'" . str_replace("'", "\'", $v) . "'";
    }
    
    /**
     * xml format string to php array
     *
     * @param string $xml
     *            xml format string
     * @param bool $simple
     *            if use simplexml,default false
     * @return array bool
     */
    public static function xml2Array($xml, $simple = false) {
        if(! is_string($xml)) {
            return false;
        }
        if($simple) {
            $xml = @simplexml_load_string($xml);
        } else {
            $xml = @json_decode(json_encode((array)simplexml_load_string($xml)), 1);
        }
        return $xml;
    }
    /**
     * php array to xml format string
     *
     * @param array $value
     *            convert array
     * @param string $encoding
     *            xml encoding
     * @param string $root
     *            xml root tag
     * @param string $nkey
     *            纯数组转换时使用的标签名
     * @return string
     */
    public static function array2XML($value, $encoding = 'utf-8', $root = 'root', $nkey = '') {
        if(! is_array($value) && ! is_string($value) && ! is_bool($value) && ! is_numeric($value) && ! is_object($value)) {
            return false;
        }
        $nkey = preg_match('/^[A-Za-z_][A-Za-z0-9\-_]{0,}$/', $nkey) ? $nkey : '';
        return simplexml_load_string('<?xml version="1.0" encoding="' . $encoding . '"?>' . self::x2str($value, $root, $nkey))->asXml();
    }
    /**
     * object or array to xml format string
     *
     * @param object $xml
     *            array or object
     * @param string $key
     *            tag name
     * @param string $nkey
     *            纯数组转换时使用的标签名
     * @return string
     */
    private static function x2str($xml, $key, $nkey) {
        if(! is_array($xml) && ! is_object($xml)) {
            return "<$key>" . htmlspecialchars($xml) . "</$key>";
        }
        
        $xml_str = '';
        foreach($xml as $k => $v) {
            if(is_numeric($k)) {
                $k = $nkey ? $key . '-' . $nkey : $key . '-item';
            }
            $xml_str .= self::x2str($v, $k, $nkey);
        }
        return "<$key>$xml_str</$key>";
    }
    /**
     * php array to csv format string
     *
     * @param array $input convert array
     * @param string $delimiter
     * @return string
     */
    public static function array2CSV($input = array(), $delimiter = ',') {
        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $handler = fopen('php://temp', 'w');
        /** loop through array  */
        foreach ($input as $line) {
            /** default php csv handler **/
            fputcsv($handler, (array)$line, $delimiter);
        }
        /** rewrind the "file" with the csv lines **/
        fseek($handler, 0);
        $output = stream_get_contents($handler);
        fclose($handler);
        return $output;
    }
    /**
     * 递归创建文件夹目录
     * @param string $dir
     * @return boolean
     */
    public static function createFolders($dir) {
        return is_dir($dir) | (self::createFolders(dirname($dir)) & mkdir($dir, 0777));
    }
    /**
     * 删除文件夹及文件夹内的文件
     * @param string $dir
     * @return boolean
     */
    public static function rmdir($dir, $involve = true) {
        $dir = realpath($dir);
        if (is_dir($dir) && $handle = opendir($dir)) {
            while( false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dir/$item")) {
                        $this->rmdir("$dir/$item");
                    } else {
                        unlink("$dir/$item");
                    }
                }
            }
            closedir($handle);
            $involve && rmdir($dir);
        } else {
            return false;
        }
        return true;
    }
    /**
     * 压缩文件夹或文件
     * @param string $dir
     * @param string $dest
     * @return boolean
     */
    public static function zip($dir, $dest, $flags = ZipArchive::OVERWRITE) {
        if(class_exists('ZipArchive') && (is_dir($dir) || is_file($dir))) {
            $zip = new ZipArchive();
            $res = $zip->open($dest, $flags);
            if($res && is_dir($dir)) {
                self::zipdir($dir, '', $zip);
            } else if($res && is_file($dir)) {
                $zip->addFile($dir);
            } else {
                return false;
            }
            $zip->close();
        } else {
            return false;
        }
        return true;
    }
    /**
     * 压缩文件夹至压缩包里的指定目录下
     * @param string $dir
     * @param string $pre 指定目录
     * @param ZipArchive $zip
     * @return boolean
     */
    private static function zipdir($dir, $pre, $zip) {
        $ret = true;
        $dir = realpath($dir) . "/";
        $basename = basename($dir);
        $predir = $pre . $basename . "/";
        //添加目录
        $ret = $zip->addEmptyDir($predir);
        //添加文件
        $handler = opendir($dir); //打开当前文件夹由$path指定。
        while(($filename = readdir($handler)) !== false) {
            if($filename != "." && $filename != "..") {//文件夹文件名字为'.'和'..'，不要对他们进行操作
                if(is_dir($dir . $filename)) {// 如果读取的某个对象是文件夹，则递归
                    $ret = $ret && self::zipdir($dir . $filename . "/", $predir, $zip);
                } else { //将文件加入zip对象
                    $ret = $ret && $zip->addFile($dir . $filename, $predir . $filename);
                }
            }
            if(empty($ret)) {
                break;
            }
        }
        @closedir($dir);
        return $ret;
    }
    /**
     * 是关联数组还是普通数组
     * @param array $array
     * @return boolean
     */
    public static function isAssocArray($array) {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
    /**
     * 根据参数构建url字符串
     * url('/', array('a' => 1, 'b' => 2));
     * /?a=1&b=2
     * url('/?c=3', array('a' => 1, 'b' => 2, 'c' => false));
     * /?a=1&b=2
     * url('/', array('a' => 1, 'b' => 2, 'c' => 3), array('c' => 4));
     * /?a=1&b=2&c=4
     * @param string $url
     * @param array $args
     * @return string
     */
    public static function url($url, $args = null) {
        $url = parse_url($url);
        if (!isset($url['path']) || !$url['path'])
            $url['path'] = '';
        $query = array();
        if (isset($url['query']))
            parse_str($url['query'], $query);
        if ($args !== null) {
            foreach (array_slice(func_get_args(), 1) as $args) {
                if (!is_array($args)) continue;
                foreach ($args as $k => $v) {
                    if ($v === false) {
                        unset($query[$k]);
                    } else {
                        $query[$k] = $v;
                    }
                }
            }
        }
        $result = '';
        if (isset($url['scheme'])) $result .= $url['scheme'].'://';
        if (isset($url['user'])) {
            $result .= $url['user'];
            if (isset($url['pass'])) $result .= ':'.$url['pass'];
            $result .= '@';
        }
        if (isset($url['host'])) $result .= $url['host'];
        if (isset($url['port'])) $result .= ':'.$url['port'];
        $result .= $url['path'];
        if ($query) $result .= '?'.http_build_query($query);
        if (isset($url['fragment'])) $result .= '#'.$url['fragment'];
        return $result;
    }
    /**
     * 2到62，任意进制转换
     * @param string $number: 转换的数字
     * @param string $from: 本来的进制
     * @param string $to: 转换到进制
     * @param string $use_bcmath: 是否使用bcmath模块处理超大数字
     */
    public static function base_convert($number, $from, $to, $use_bcmath = null) {
        $base = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $loaded = extension_loaded('bcmath');
        if ($use_bcmath && !$loaded)
            throw new \RuntimeException('Require bcmath extension!');
        $use_bcmath = $loaded;
        // 任意进制转换为十进制
        $any2dec = function($number, $from) use ($base, $use_bcmath) {
            if ($from === 10)
                return $number;
            $base = substr($base, 0, $from);
            $dec = 0;
            $number = (string)$number;
            for ($i = 0, $len = strlen($number); $i < $len; $i++) {
                $c = substr($number, $i , 1);
                $n = strpos($base, $c);
                if ($n === false)   // 出现了当前进制不支持的数字
                    trigger_error('Unexpected base character: '. $c, E_USER_ERROR);
                $pos = $len - $i - 1;
                if ($use_bcmath) {
                    $dec = bcadd($dec, bcmul($n, bcpow($from, $pos)));
                } else {
                    $dec += $n * pow($from, $pos);
                }
            }
            return $dec;
        };
        // 十进制转换为任意进制
        $dec2any = function($number, $to) use ($base, $use_bcmath) {
            if ($to === 10)
                return $number;
            $base = substr($base, 0, $to);
            $any = '';
            while ($number >= $to) {
                if ($use_bcmath) {
                    list($number, $c) = array(bcdiv($number, $to), bcmod($number, $to));
                } else {
                    list($number, $c) = array((int)($number / $to), $number % $to);
                }
                $any = substr($base, $c, 1) . $any;
            }
            $any = substr($base, $number, 1) . $any;
            return $any;
        };
        ////////////////////////////////////////////////////////////////////////////////
        $from = (int)$from;
        $to = (int)$to;
        $min_base = 2;
        $max_base = strlen($base);
        if ($from < $min_base || $from > $max_base || $to < $min_base || $to > $max_base)
            trigger_error("Only support base between {$min_base} and {$max_base}", E_USER_ERROR);
        if ($from === $to)
            return $number;
        // 转换为10进制
        $dec = ($from === 10) ? $number : $any2dec($number, $from);
        return $dec2any($dec, $to);
    }

    /* * *************************************************************************
     * pinyin.php
     * Desc. : 拼音转换
      //默认是gb编码
      echo pinyin('第二个参数随意设置',2); //第二个参数随意设置即为utf8
     * ************************************************************************* 
     */
    public static function pinyin($_String, $_Code = 'gb2312', $isInitial = false) {
        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
                "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
                "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
                "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
                "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
                "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
                "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
                "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
                "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
                "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
                "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
                "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
                "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
                "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
                "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
                "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
                "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
                "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
                "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
                "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
                "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
                "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
                "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
                "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
                "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
                "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
                "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
                "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
                "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
                "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
                "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
                "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
                "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
                "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
                "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
                "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
                "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
                "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
                "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
                "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
                "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
                "|-10270|-10262|-10260|-10256|-10254";
        $_TDataKey = explode('|', $_DataKey);
        $_TDataValue = explode('|', $_DataValue);
        $_Data = (PHP_VERSION >= '5.0') ? array_combine($_TDataKey, $_TDataValue) : self::_array_combine($_TDataKey, $_TDataValue);
        arsort($_Data);
        reset($_Data);
        if ($_Code != 'gb2312')
            $_String = self::_u2_utf8_gb($_String);
        $_Res = '';
        for ($i = 0; $i < strlen($_String); $i++) {
            $_P = ord(substr($_String, $i, 1));
            if ($_P > 160) {
                $_Q = ord(substr($_String, ++$i, 1));
                $_P = $_P * 256 + $_Q - 65536;
            }
            $_Res .= self::_pinyin($_P, $_Data, $isInitial);
        }
        return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }
     
    private static function _pinyin($_Num, $_Data, $isInitial) {
        if ($_Num > 0 && $_Num < 160)
            return chr($_Num);
        elseif ($_Num < -20319 || $_Num > -10247)
            return '';
        else {
            foreach ($_Data as $k => $v) {
                if ($v) break;
            }
            if ($isInitial)
                $k = substr($k, 0, 1); //是否只显示首个拼音字母
            return $k;
        }
    }
     
    private static function _u2_utf8_gb($_C) {
        $_String = '';
        if ($_C < 0x80)
            $_String .= $_C;
        elseif ($_C < 0x800) {
            $_String .= chr(0xC0 | $_C >> 6);
            $_String .= chr(0x80 | $_C & 0x3F);
        } elseif ($_C < 0x10000) {
            $_String .= chr(0xE0 | $_C >> 12);
            $_String .= chr(0x80 | $_C >> 6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        } elseif ($_C < 0x200000) {
            $_String .= chr(0xF0 | $_C >> 18);
            $_String .= chr(0x80 | $_C >> 12 & 0x3F);
            $_String .= chr(0x80 | $_C >> 6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }
        return iconv('UTF-8', 'GB2312', $_String);
    }
     
    private static function _array_combine($_Arr1, $_Arr2) {
        for ($i = 0; $i < count($_Arr1); $i++)
            $_Res[$_Arr1[$i]] = $_Arr2[$i];
        return $_Res;
    }
}