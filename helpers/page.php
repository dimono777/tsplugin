<?php

namespace tradersoft\helpers;

use tradersoft\helpers\System\PageKey;
use tradersoft\cache\TransientCache;
use TS_Functions;

/**
 * Class Page
 * @package tradersoft\helpers
 * @author Anatolii Lishchynskyi <anatolii.lishchynsky@tstechpro.com>
 */
class Page
{
    const CACHE_KEY = 'page_keys_data';

    // need to clear the cache after sort changes
    public static $sortType = 'ASC';
    public static $sortColumn = 'post_title';

    public static $pages;

    /**
     * Get all pages
     *
     * @return array
     */
    protected static function getAll()
    {
        if (!isset(self::$pages)) {
            self::$pages = self::preparePages(get_pages([
                'sort_order' => self::$sortType,
                'sort_column' => self::$sortColumn,
            ]));
        }

        return self::$pages;
    }

    /**
     * Get page data by page id
     *
     * @param $pageId
     * @return array
     */
    public static function getById($pageId)
    {
        if (!isset(self::$pages[$pageId])) {
            self::$pages[$pageId] = current(get_pages(['include' => $pageId]));
        }

        return self::$pages[$pageId];
    }

    /**
     * @param string $key
     * @param string $field
     * @param array $params
     * @return mixed
     */
    public static function getFieldValueByKey($key, $field = 'ID', array $params = [])
    {
        $pageId = current(self::getIdsByKey($key, $params));
        if (!$pageId) {
            return null;
        }

        $page = self::getById($pageId);

        return $page->{"$field"};
    }

    /**
     * Get pages id list by key
     *
     * @param string $key
     * @param array $params
     * @return array
     */
    public static function getIdsByKey($key, array $params = [])
    {
        $pageKeysData = self::getAllKeysData();

        $pageKeysData = array_filter($pageKeysData, function ($row) use ($key, $params) {
            $result = $row['key'] == $key;
            $additionalVerification = true;
            if ($result && !empty($params)) {
                foreach ($params as $key => $value) {
                    if ($value != Arr::get($row['params'], $key)) {
                        $additionalVerification = false;
                        break;
                    }
                }
            }

            return $result && $additionalVerification;
        });

        return array_unique(array_column($pageKeysData, 'page_id'));
    }

    /**
     * Get list of short codes on the page
     *
     * @param $pageId
     * @return array
     */
    public static function getKeysById($pageId)
    {
        $pageKeysData = self::getAllKeysData();

        $pageKeysData = array_filter($pageKeysData, function($row) use ($pageId) {
            return $row['page_id'] == $pageId;
        });

        return $pageKeysData;
    }

    /**
     * @param $pages
     * @return array
     */
    public static function preparePages($pages)
    {
        $return = [];
        foreach ($pages as $page) {
            $return[$page->ID] = $page;
        }
        return $return;
    }

    /**
     * Get page keys data
     *
     * @return array
     */
    public static function getAllKeysData()
    {
        $result = TransientCache::get(self::getCacheKey());

        if ($result === false) {
            $pageKeys = PageKey::getPagesActions();
            $pages = self::getAll();

            $result = [];
            foreach ($pages as $pageId => $page) {
                $postContent = Arr::get($page, 'post_content');

                foreach ($pageKeys as $key => $path) {
                    $pattern = '/\[' . str_replace(['[', ']'], '', $key) . '(\s[^\]]+)?\]/';

                    if (!preg_match_all($pattern, $postContent, $matches)) {
                        continue;
                    }

                    list($expressions, $variables) = $matches;

                    foreach ($expressions as $i => $expression) {

                        if (
                            !empty($variables[$i])
                            && preg_match_all('/([\w_-]+)=`([^`]*)`/', $variables[$i], $paramsData)
                        ) {

                            array_shift($paramsData);
                            list($paramsKeys, $paramsValues) = $paramsData;

                            $params = array_combine($paramsKeys, $paramsValues);
                        } else {
                            $params = [];
                        }

                        $result[] = [
                            'key' => $key,
                            'page_id' => $pageId,
                            'path' => $path,
                            'params' => $params,
                            'expression' => $expression,

                        ];
                    }
                }
            }

            TransientCache::set(self::getCacheKey(), $result);
        }

        return $result;
    }

    /**
     * Clear cached data
     */
    public static function clearCache()
    {
        $languages = TS_Functions::getActiveLanguages();

        TransientCache::clear(self::CACHE_KEY);
        foreach ($languages as $language) {
            TransientCache::clear(self::getCacheKey($language['code']));
        }
    }

    /**
     * Get cache key
     *
     * @param string $langCode
     * @return string
     */
    public static function getCacheKey($langCode = '')
    {
        if (!$langCode) {
            $langCode = TS_Functions::getCurrentLanguage();
        }

        return self::CACHE_KEY . '_' . $langCode;
    }

    /**
     * 404 response page
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function set404()
    {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }
}