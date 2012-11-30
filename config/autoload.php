<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package News_slideshow
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModuleNewsC4Y'       => 'system/modules/news_slideshow/ModuleNewsC4Y.php',
	'ModuleNewsSlideshow' => 'system/modules/news_slideshow/ModuleNewsSlideshow.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_news_slideshow_list' => 'system/modules/news_slideshow/templates',
	'news_slideshow'          => 'system/modules/news_slideshow/templates',
));
