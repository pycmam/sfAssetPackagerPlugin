<?php

class AssetPackagerHelper
{
    /**
     * Обработать файлы со стилями
     *   - расставляет timestamp
     *   - разворачивает пути к compiled
     *   - заменяет compiled-версию на список исходных файлов
     *
     * @param  array $stylesheets
     * @return
     */
    public static function processStylesheets(array $stylesheets)
    {
        $time     = sfConfig::get('app_sf_assets_timestamp');
        $compiled = sfConfig::get('app_sf_assets_compile');
        $packages = sfConfig::get('app_sf_assets_packages', array());

        $result = array();
        foreach ($stylesheets as $name => $options) {

            if (isset($packages[$name])) {
                if ($compiled) {
                    $name = sprintf('/%s/%s', trim(sfConfig::get('app_sf_assets_compile_dir'), '/'), $name);
                } else {
                    foreach ($packages[$name] as $file) {
                        $result[self::addTimestamp($file)] = $options;
                    }
                    continue;
                }
            }

            $result[self::addTimestamp($name)] = $options;
        }
        return $result;
    }


    /**
     * Добавить timestamp в название файла
     *
     * @param  string $name
     * @return string
     */
    public static function addTimestamp($name)
    {
        $time  = sfConfig::get('app_sf_assets_timestamp');
        if ($time) {
            $name .= '?'.$time;
        }
        return $name;
    }

}


/**
 * Подключить css и js с указанием таймстампа последней сборки
 * Оборачивает стандартные хелперы
 *
 * @param  string $type - css | js
 * @return void
 */
function include_assets($type)
{
    switch ($type) {
        case 'css':
            $response = sfContext::getInstance()->getResponse();
            $stylesheets = AssetPackagerHelper::processStylesheets($response->getStylesheets());

            sfConfig::set('symfony.asset.stylesheets_included', true);
            foreach ($stylesheets as $file => $options) {
                echo stylesheet_tag($file, $options);
            }
            break;

        case 'js':
            $version = sfConfig::get('app_sf_assets_timestamp');
            echo str_replace('.js', '.js?'.$version, get_javascripts());
            break;

        default:
            throw new InvalidArgumentException(__FUNCTION__.": Unknown asset type `{$type}`");
    }
}
