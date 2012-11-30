<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Oliver Lohoff 2011
 * @author     Oliver Lohoff <http://www.contao4you.de>
 * @package    News-Slideshow
 * @license    LGPL
 * @filesource
 */



/**
 * Class ModuleNews
 *
 * Parent class for news modules.
 * @copyright  contao4you 2011
 * @author     Oliver Lohoff <http://www.contao4you.de>
 * @package    news_galerie
 */
abstract class ModuleNewsC4Y extends Module
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();


	/**
	 * Sort out protected archives
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrArchives)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrArchives) || count($arrArchives) < 1)
		{
			return $arrArchives;
		}

		$this->import('FrontendUser', 'User');
		$objArchive = $this->Database->execute("SELECT id, protected, groups FROM tl_news_archive WHERE id IN(" . implode(',', array_map('intval', $arrArchives)) . ")");
		$arrArchives = array();

		while ($objArchive->next())
		{
			if ($objArchive->protected)
			{
				if (!FE_USER_LOGGED_IN)
				{
					continue;
				}

				$groups = deserialize($objArchive->groups);

				if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1)
				{
					continue;
				}
			}

			$arrArchives[] = $objArchive->id;
		}

		return $arrArchives;
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseArticles(Database_Result $objArticles, $blnAddArchive=false)
	{
		
		$row = array();
		$items = array();
		
		if ($objArticles->numRows < 1)
		{
			return array();
		}

		$this->import('String');

		$arrArticles = array();
		$limit = $objArticles->numRows;
		$count = 0;
		$imgSize = false;

		// Override the default image size (Big Picture)
		if ($this->imgSize != '')
		{
			$size = deserialize($this->imgSize);

			if ($size[0] > 0 || $size[1] > 0)
			{
				$imgSize = $this->imgSize;
			}
		}

		while ($objArticles->next())
		{

			$objTemplate = new FrontendTemplate($this->news_template);
			$objTemplate->setData($objArticles->row());
            $objTemplate->showinfobox = $this->showinfobox;

			$objTemplate->count = ++$count;
            $objTemplate->id1 = $this->Template->id;
			$objTemplate->class = (strlen($objArticles->cssClass) ? ' ' . $objArticles->cssClass : '') . (($count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even');
			$row['count'] = $objTemplate->count;
			$row['newsHeadline'] = $objArticles->headline;
			$row['subHeadline'] = $objArticles->subheadline;
			$row['teaser'] = $objArticles->teaser;
			$row['hasSubHeadline'] = $objArticles->subheadline ? true : false;
			$row['linkHeadline'] = $this->generateLink($objArticles->headline, $objArticles, $blnAddArchive);
			$row['more'] = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], $objArticles, $blnAddArchive, true);
			$row['link'] = $this->generateNewsUrl($objArticles, $blnAddArchive);
			$row['archive'] = $objArticles->archive;
			$row['showmenupicture'] = $this->showmenupicture;


			// Display the "read more" button for external/article links
			if (($objArticles->source == 'external' || $objArticles->source == 'article') && !strlen($objArticles->text))
			{
				$objTemplate->text = true;
			}

			// Encode e-mail addresses
			else
			{
				// Clean RTE output
				$row['text'] = str_ireplace
				(
					array('<u>', '</u>', '</p>', '<br /><br />', ' target="_self"'),
					array('<span style="text-decoration:underline;">', '</span>', "</p>\n", "<br /><br />\n", ''),
					$this->String->encodeEmail($objArticles->text)
				);
			}

			$arrMeta = $this->getMetaFields($objArticles);

			// Add meta information
			$row['date'] = $arrMeta['date'];
			$row['hasMetaFields'] = count($arrMeta) ? true : false;
			$row['numberOfComments'] = $arrMeta['ccount'];
			$row['commentCount'] = $arrMeta['comments'];
			$row['timestamp'] = $objArticles->date;
			$row['author'] = $arrMeta['author'];
			$row['addImage'] = false;

            $objUrl = \FilesModel::findByPk($objArticles->singleSRC);
            $size = deserialize($this->imgSize);

    		$src = \Image::get($objUrl->path, $size[0], $size[1], $size[2]);
            $image = $this->generateImage(TL_FILES_URL . $src, $row['newsHeadline']);
    		$row['linkImage'] = $this->generateLink($image, $objArticles);

            if ($this->showmenupicture)
            {
                $size = deserialize($this->showmenupicturesize);
                $src = \Image::get($objUrl->path, $size[0], $size[1], $size[2]);
                $image = $this->generateImage(TL_FILES_URL . $src, $row['newsHeadline']);
                //$row['menupicture'] = $this->generateLink($image, $objArticles);
                $row['menupicture'] = $image;
            }

			$items[] = $row;
		}

		// hier wird das News-Template geparsed (innerhalb der mod_newslist)
		$objTemplate->items = $items;
		return count($items) ? $objTemplate->parse() : '';
	
	}


	/**
	 * Return the meta fields of a news article as array
	 * @param object
	 * @return array
	 */
	protected function getMetaFields(Database_Result $objArticle)
	{
		$meta = deserialize($this->news_metaFields);

		if (!is_array($meta))
		{
			return array();
		}

		$return = array();

		foreach ($meta as $field)
		{
			switch ($field)
			{
				case 'date':
					$return['date'] = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objArticle->date);
					break;

				case 'author':
					if (strlen($objArticle->author))
					{
						$return['author'] = $GLOBALS['TL_LANG']['MSC']['by'] . ' ' . $objArticle->author;
					}
					break;

				case 'comments':
					$objComments = $this->Database->prepare("SELECT COUNT(*) AS total FROM tl_comments WHERE source='tl_news' AND parent=?" . (!BE_USER_LOGGED_IN ? " AND published=1" : ""))
												  ->execute($objArticle->id);

					if ($objComments->numRows)
					{
						$return['ccount'] = $objComments->total;
						$return['comments'] = sprintf($GLOBALS['TL_LANG']['MSC']['commentCount'], $objComments->total);
					}
					break;
			}
		}

		return $return;
	}


	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateNewsUrl(Database_Result $objArticle, $blnAddArchive=false)
	{
		$strCacheKey = 'id_' . $objArticle->id;

		// Load URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey]))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize cache
		self::$arrUrlCache[$strCacheKey] = '';

		switch ($objArticle->source)
		{
			// Link to external page
			case 'external':
				$this->import('String');

				if (substr($objArticle->url, 0, 7) == 'mailto:')
				{
					self::$arrUrlCache[$strCacheKey] = $this->String->encodeEmail($objArticle->url);
				}
				else
				{
					self::$arrUrlCache[$strCacheKey] = ampersand($objArticle->url);
				}
				break;

			// Link to an internal page
			case 'internal':
				$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
									 	  ->limit(1)
										  ->execute($objArticle->jumpTo);

				if ($objPage->numRows)
				{
					self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row()));
				}
				break;

			// Link to an article
			case 'article':
				$objPage = $this->Database->prepare("SELECT a.id AS aId, a.alias AS aAlias, a.title, p.id, p.alias FROM tl_article a, tl_page p WHERE a.pid=p.id AND a.id=?")
										  ->limit(1)
										  ->execute($objArticle->articleId);

				if ($objPage->numRows)
				{
					self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row(), '/articles/' . ((!$GLOBALS['TL_CONFIG']['disableAlias'] && strlen($objPage->aAlias)) ? $objPage->aAlias : $objPage->aId)));
				}
				break;
		}

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] == '')
		{
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
								 	  ->limit(1)
									  ->execute($objArticle->parentJumpTo);

			if ($objPage->numRows)
			{
				self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row(), '/items/' . ((!$GLOBALS['TL_CONFIG']['disableAlias'] && strlen($objArticle->alias)) ? $objArticle->alias : $objArticle->id)));
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = ampersand($this->Environment->request, true);
			}

			// Add the current archive parameter (news archive)
			if ($blnAddArchive && strlen($this->Input->get('month')))
			{
				self::$arrUrlCache[$strCacheKey] .= ($GLOBALS['TL_CONFIG']['disableAlias'] ? '&amp;' : '?') . 'month=' . $this->Input->get('month');
			}
		}

		return self::$arrUrlCache[$strCacheKey];
	}


	/**
	 * Generate a link and return it as string
	 * @param string
	 * @param object
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	protected function generateLink($strLink, Database_Result $objArticle, $blnAddArchive=false, $blnIsReadMore=false)
	{
		// Internal link
		if ($objArticle->source != 'external')
		{
			return sprintf('<a href="%s" title="%s">%s%s</a>',
							$this->generateNewsUrl($objArticle, $blnAddArchive),
							specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objArticle->headline), true),
							$strLink,
							($blnIsReadMore ? ' <span class="invisible">'.$objArticle->headline.'</span>' : ''));
		}

		// Encode e-mail addresses
		if (substr($objArticle->url, 0, 7) == 'mailto:')
		{
			$this->import('String');
			$objArticle->url = $this->String->encodeEmail($objArticle->url);
		}

		// Ampersand URIs
		else
		{
			$objArticle->url = ampersand($objArticle->url);
		}

		// External link
		return sprintf('<a href="%s" title="%s"%s>%s</a>',
						$objArticle->url,
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['open'], $objArticle->url)),
						($objArticle->target ? LINK_NEW_WINDOW : ''),
						$strLink);
	}

    public static function addImageToRow($Row, $objArticle)
	{
		global $objPage;

		$size = deserialize($Row['size']);
		$imgSize = getimagesize(TL_ROOT .'/'. $Row['singleSRC']);

        $objUrl = \FilesModel::findByPk($objArticle->singleSRC);
		$src = \Image::get($objUrl->path, $size[0], $size[1], $size[2]);

		// Image dimensions
		if (($imgSize = @getimagesize(TL_ROOT .'/'. rawurldecode($src))) !== false)
		{
			$Row['arrSize'] = $imgSize;
			$Row['imgSize'] = ' ' . $imgSize[3];
		}

		// Image link
		if ($Row['imageUrl'] != '' && TL_MODE == 'FE')
		{
			$Row->href = $Row['imageUrl'];
			$Row->attributes = '';
		}



		// Do not urlEncode() here because getImage() already does (see #3817)
		$Row['linkImage'] = TL_FILES_URL . $src;
	}


}


?>