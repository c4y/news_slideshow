<?php

class NewsSlideshowModel extends \NewsModel
{

    public static function countPublishedByPids($arrPids, $blnFeatured=null, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return 0;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // contao4you begin
        $arrColumns[] = "news_slideshow = 1";
        // contao4you end

        if (!BE_USER_LOGGED_IN)
        {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::countBy($arrColumns, null, $arrOptions);
    }



    public static function findPublishedByPids($arrPids, $blnFeatured=null, $intLimit=0, $intOffset=0, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // contao4you begin
        $arrColumns[] = "news_slideshow = 1";
        // contao4you end

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order']  = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }



}