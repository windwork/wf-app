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
 * Windwork框架版本信息
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.version.html
 * @since       0.1.0
 */
final class Version 
{
    /**
     * 当前Windwork版本号
     * @var string
     */
    const VERSION = '0.7.0';
        
    /**
     * 所有组件在GitHub上最后发布的版本号
     * 
     * @return string
     */
    public static function getLatest()
    {
        $composerFile = "https://raw.githubusercontent.com/windwork/wf-app/master/composer.json";
        $composerJson = @file_get_contents($composerFile);
        
        if (!$composerJson) {
            throw new Exception('can\'t read composer file: ' . $composerFile);
        }
        
        $composerObj = json_decode($composerJson);
        if (!$composerObj || !$composerObj->version) {
            throw new Exception('composer.json data error: ' . $composerJson);
        }
        
        $latest = $composerObj->version;
        
        return $latest;
    }
}
