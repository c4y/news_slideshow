<?php

/**
 * PHP version 5
 * @copyright  Oliver Lohoff 2011
 * @author     Oliver Lohoff <http://www.contao4you.de>
 * @package    News-Galerie
 * @license    LGPL
 * @filesource
 */


/**
 * Add palettes to tl_module
 */

array_insert($GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'],0,'showmenupicture');

$GLOBALS['TL_DCA']['tl_module']['palettes']['newsslideshow']          = '{title_legend},name,headline,type;{slideshow_legend},showinfobox,showmenupicture, autostart,duration,intervall,transition;{config_legend},news_archives,news_numberOfItems,news_featured,perPage,skipFirst;{template_legend:hide},news_metaFields,news_template,imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['showmenupicture']     =  'showmenupicturesize';
$GLOBALS['TL_DCA']['tl_module']['fields']['news_template']['default'] = 'news_slideshow';

array_insert($GLOBALS['TL_DCA']['tl_module']['fields'],0,array
(
	'showinfobox'   => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showinfobox'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
	),
	'showmenupicture'      => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showmenupicture'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
        'eval'                    => array('submitOnChange'=>true),
	),
	'duration'      => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['duration'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>5, 'rgxp'=>'digit'),
	),
	'intervall'      => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['intervall'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>5, 'rgxp'=>'digit'),
	),
	'transition'      => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['transition'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options'                  => array('fade', 'slide-left', 'slide-right', 'slide-top', 'slide-bottom' ),
	),
	'autostart'      => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['autostart'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
	),
    'showmenupicturesize' => array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_content']['size'],
        'exclude'                 => true,
        'inputType'               => 'imageSize',
        'options'                 => $GLOBALS['TL_CROP'],
        'reference'               => &$GLOBALS['TL_LANG']['MSC'],
        'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true),
	    'save_'
    ),
));