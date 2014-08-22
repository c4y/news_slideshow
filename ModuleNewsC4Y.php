<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */


/**
 * Class ModuleNews
 *
 * Parent class for news modules.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
abstract class ModuleNewsC4Y extends \ModuleNews
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();




	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseArticle($objArticle, $blnAddArchive=false, $strClass='', $intCount=0)
	{
		global $objPage;

        // contao4you begin
        // Packe die Infos in ein Array statt in ein Template
        // und gib es ungeparsed zurück
        $article = $objArticle->row();
        // contao4you end

        $article["id"] = $this->id;

		$article["class"] = (($objArticle->cssClass != '') ? ' ' . $objArticle->cssClass : '') . $strClass;
		$article["newsHeadline"] = $objArticle->headline;
		$article["subHeadline"] = $objArticle->subheadline;
		$article["hasSubHeadline"] = $objArticle->subheadline ? true : false;
		$article["linkHeadline"] = $this->generateLink($objArticle->headline, $objArticle, $blnAddArchive);
		$article["more"] = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], $objArticle, $blnAddArchive, true);
		$article["link"] = $this->generateNewsUrl($objArticle, $blnAddArchive);
		$article["archive"] = $objArticle->getRelated('pid');
		$article["count"] = $intCount; // see #5708
		$article["text"] = '';

		// Clean the RTE output
		if ($objArticle->teaser != '')
		{
			if ($objPage->outputFormat == 'xhtml')
			{
				$article["teaser"] = \String::toXhtml($objArticle->teaser);
			}
			else
			{
				$article["teaser"] = \String::toHtml5($objArticle->teaser);
			}

			$article["teaser"] = \String::encodeEmail($article["teaser"]);
		}

		// Display the "read more" button for external/article links
		if ($objArticle->source != 'default')
		{
			$article["text"] = true;
		}

		// Compile the news text
		else
		{
			$objElement = \ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_news');

			if ($objElement !== null)
			{
				while ($objElement->next())
				{
					$article["text"] .= $this->getContentElement($objElement->current());
				}
			}
		}

		$arrMeta = $this->getMetaFields($objArticle);

		// Add the meta information
		$article["date"] = $arrMeta['date'];
		$article["hasMetaFields"] = !empty($arrMeta);
		$article["numberOfComments"] = $arrMeta['ccount'];
		$article["commentCount"] = $arrMeta['comments'];
		$article["timestamp"] = $objArticle->date;
		$article["author"] = $arrMeta['author'];
		$article["datetime"] = date('Y-m-d\TH:i:sP', $objArticle->date);

		$article["addImage"] = false;

		// Add an image
		if ($objArticle->addImage && $objArticle->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objArticle->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objArticle->singleSRC))
				{
					$article["text"] = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
                // contao4you begin
                // Slider
                $size = deserialize($this->imgSize);
				$article['singleSRC'] = \Image::get($objModel->path, $size[0], $size[1], $size[2]);
                // Menu-Image
                if($this->showmenupicture)
                {
                    $size = deserialize($this->showmenupicturesize);
                    $article["showmenupicture"] = true;
                    $article["menupicture"] = \Image::get($objModel->path, $size[0], $size[1], $size[2]);
                }
                // contao4you end
			}
		}


        // contao4you begin
        // gib nur das Array zurück, damit das news_slideshow Template
        // alle Infos auf einmal hat
		return $article;
        // contao4you end
	}


	/**
	 * Parse one or more items and return them as array
     * contao4you: Template wird hier für alle Articles auf einmal geparsed
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseArticles($objArticles, $blnAddArchive=false)
	{

	    $objTemplate = new \FrontendTemplate($this->news_template);

		$limit = $objArticles->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrArticles = array();

		while ($objArticles->next())
		{
			$arrArticles[] = $this->parseArticle($objArticles, $blnAddArchive, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

        $objTemplate->id = $this->id;
        $objTemplate->articles = $arrArticles;

		return $objTemplate->parse();
	}



}
