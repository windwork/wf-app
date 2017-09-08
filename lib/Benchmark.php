<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\app;

/**
 * Windwork性能检测类
 * 
 * 用于记录程序执行时间和使用内存量
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.benchmark.html
 * @since       0.1.0
 */
final class Benchmark 
{
    /**
     * 标注点运行信息记录
     * @var array
     */
    private static $marks = [];
    
    /**
     * 开始记录
     * 
     * 如果已defined('__WF_START_TIME', microtime(true))，则使用__WF_START_TIME作为开始时间
     * 
     * @return array 
     * <pre>
     * [
     *     'mem'  => 当前使用内存, // （M）精确到k
     *     'time' => microtime(1)
     * ]
     * <pre>
     */
    public static function start()
    {
        if (isset(static::$marks['@__start'])) {
            return;
        }
        
        // 如果已定义__WF_START_TIME并且格式是microtime(1)的，就使用作为初始时间，否则使用当前microtime
        $startTime = (defined('__WF_START_TIME') && preg_match("/^1\\d{9}\\.\\d+/", __WF_START_TIME, $m))
            ? __WF_START_TIME : microtime(true);
        
        return static::$marks['@__start'] = [
            'mem'  => number_format(memory_get_usage()/(1024*1024), 3),// （M）精确到k
            'time' => $startTime,
            //'incs' => get_included_files(),
        ];
    }
    
    /**
     * 当前标注点的执行时间和内存使用 
     * 
     * @param string $pointKey
     * @return array 
     * <pre>
     * [
     *     'mem'  => 当前使用内存, // （M）精确到k
     *     'time' => microtime(1),
     *     'elapsed' => 当前执行时间, // （s）
     * ]
     * <pre>
     */
    public static function mark($pointKey)
    {
        static::$marks[$pointKey] = [
            'mem'  => number_format(memory_get_usage()/(1024*1024), 3), // （M）精确到k
            'time' => microtime(1),
            //'incs' => get_included_files(),
        ];
        
        // 从开始到当前点执行时间
        static::$marks[$pointKey]['elapsed'] = number_format(static::$marks[$pointKey]['time'] - static::$marks['@__start']['time'], 4);
        
        return static::$marks[$pointKey];
    }
    
    /**
     * 获取标注点记录信息
     * @param string $pointKey = '' 为空时返回全部标注点信息
     * @return array
     */
    public static function getMark($pointKey = '')
    {
        return $pointKey ? static::$marks[$pointKey] : static::$marks;
    }
}

