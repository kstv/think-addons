<?php
// +----------------------------------------------------------------------
// | thinkphp5 Addons [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@qq.com>
// +----------------------------------------------------------------------
namespace think;

use think\Controller;

/**
 * 插件执行默认控制器
 * Class Addons
 * @package app\common\controller
 */
class Addons extends Controller
{
    // 允许共公访问
    protected $allow = ['*'];

    // 当前插件操作
    protected $addon = null;
    protected $controller = null;
    protected $action = null;

    /**
     * 插件初始化
     */
    public function _initialize()
    {
        $this->addon        = ucfirst($this->request->get('_addon/s', ''));
        $this->controller   = ucfirst($this->request->get('_controller/s', ''));
        $this->action       = $this->request->get('_action/s', '');
        define('ADDONS_URL', __ROOT__ . '/addons/' . $this->addon);

        $parse_def = config('parse_str');
        $parse_string = [
            '__IMG__'       => ADDONS_URL . '/images',
            '__CSS__'       => ADDONS_URL . '/css',
            '__JS__'        => ADDONS_URL . '/js',
        ];
        if (is_array($parse_def)) {
            $parse_string = array_merge($parse_def, $parse_string);
        }
        config('parse_str', $parse_string);
    }

    /**
     * 插件执行
     */
    public function execute()
    {
        if(!empty($this->addon) && !empty($this->controller) && !empty($this->action)){
            // 获取类的命名空间
            $class = get_addon_class($this->addon, 'controller') . "\\{$this->controller}";
            $model = new $class();
            if ($model === false) {
                return $this->error(L('addon init fail'));
            }
            // 调用操作
            return call_user_func([$model, $this->action]);
        }
        return $this->error(lang('addon cannot name or action'));
    }
}