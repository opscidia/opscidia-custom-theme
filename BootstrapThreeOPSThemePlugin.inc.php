<?php

/**
 * @file plugins/themes/default/BootstrapThreeOPSThemePlugin.inc.php
 *
 * Copyright (c) 2020 Opscidia
 * Distributed under the GNU GPL v3.
 *
 * @class BootstrapThreeOPSThemePlugin
 * @ingroup plugins_themes_bootstrap3
 *
 * @brief Default theme
 */

import('lib.pkp.classes.plugins.ThemePlugin');
import('lib.pkp.classes.plugins.GenericPlugin');

define('OLDGREGG_CSL_STYLE_DEFAULT', 'vancouver');
define('OLDGREGG_LATEST_ARTICLES_DEFAULT', 20);
define('OLDGREGG_ISSUE_COVER_RELATIVE_URL', 'images/issue_default.jpg');
define('OLDGREGG_LATEST_ISSUES_DEFAULT', 3);
define("CREATE_PDF_QUERY", "download=pdf");

class BootstrapThreeOPSThemePlugin extends ThemePlugin {
	/**
	 * Initialize the theme
	 *
	 * @return null
	 */
	public function init() {

		// Register option for bootstrap themes
		$this->addOption('bootstrapThemeOPS', 'radio', array(
			'label' => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.label',
			'description' => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.description',
			'options' => array(
				'bootstrap3' => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.bootstrap3',
				'cerulean'   => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.cerulean',
				'cleanblog'  => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.cleanblog',
				'cosmo'      => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.cosmo',
				'cyborg'     => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.cyborg',
				'darkly'     => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.darkly',
				'flatly'     => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.flatly',
				'journal'    => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.journal',
				'lumen'      => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.lumen',
				'paper'      => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.paper',
				'readable'   => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.readable',
				'sandstone'  => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.sandstone',
				'simplex'    => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.simplex',
				'slate'      => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.slate',
				'spacelab'   => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.spacelab',
				'superhero'  => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.superhero',
				'united'     => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.united',
				'yeti'       => 'plugins.themes.bootstrap3.options.bootstrapThemeOPS.yeti',
			)
		));

		// Determine the path to the glyphicons font in Bootstrap
		$iconFontPath = Application::getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/bootstrap/fonts/';

		$bootstrapThemeOPS = $this->getOption('bootstrapThemeOPS');
		if (empty($bootstrapThemeOPS) || $bootstrapThemeOPS === 'bootstrap3') {
			$this->addStyle('bootstrap', 'styles/bootstrap.less');
			$this->modifyStyle('bootstrap', ['addLessVariables' => '@icon-font-path:"' . $iconFontPath . '";']);
		} else {
			$this->addStyle('bootstrapThemeOPS-' . $bootstrapThemeOPS, 'styles/' . $bootstrapThemeOPS . '.less');
			$this->modifyStyle('bootstrapThemeOPS-' . $bootstrapThemeOPS, ['addLessVariables' => '@icon-font-path:"' . $iconFontPath . '";']);
		}

		$locale = AppLocale::getLocale();
		if (AppLocale::getLocaleDirection($locale) === 'rtl') {
			if (Config::getVar('general', 'enable_cdn')) {
				$this->addStyle('bootstrap-rtl', '//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css', array('baseUrl' => ''));
			} else {
				$this->addStyle('bootstrap-rtl', 'styles/bootstrap-rtl.min.css');
			}
		}

		// Load jQuery from a CDN or, if CDNs are disabled, from a local copy.
		$min = Config::getVar('general', 'enable_minified') ? '.min' : '';
		$request = Application::getRequest();
		if (Config::getVar('general', 'enable_cdn')) {
			$jquery = '//ajax.googleapis.com/ajax/libs/jquery/' . CDN_JQUERY_VERSION . '/jquery' . $min . '.js';
			$jqueryUI = '//ajax.googleapis.com/ajax/libs/jqueryui/' . CDN_JQUERY_UI_VERSION . '/jquery-ui' . $min . '.js';
		} else {
			// Use OJS's built-in jQuery files
			$jquery = $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jquery/jquery' . $min . '.js';
			$jqueryUI = $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jqueryui/jquery-ui' . $min . '.js';
		}
		// Use an empty `baseUrl` argument to prevent the theme from looking for
		// the files within the theme directory
		$this->addScript('jQuery', $jquery, array('baseUrl' => ''));
		$this->addScript('jQueryUI', $jqueryUI, array('baseUrl' => ''));
		$this->addScript('jQueryTagIt', $request->getBaseUrl() . '/lib/pkp/js/lib/jquery/plugins/jquery.tag-it.js', array('baseUrl' => ''));

		// Load Bootstrap
		$this->addScript('bootstrap', 'bootstrap/js/bootstrap.min.js');

		// Add navigation menu areas for this theme
		$this->addMenuArea(array('primary', 'user'));

		// From OldGregg theme

		if (!class_exists('\JATSParser\Body\Document', true)) {
			require_once  __DIR__ . '/JATSParser/vendor/autoload.php';
		}
		// Register theme options
		$this->addOption('latestArticlesNumber', 'text', array(
			'label' => 'plugins.gregg.latest.number',
			'description' => 'plugins.gregg.latest.description',
		));

		$this->addOption('displayIssuesSlider', 'text', array(
			'label' => 'plugins.gregg.if-display.issue-slider',
			'description' => 'plugins.gregg.if-display.issue-slider.description',
		));

		$this->addOption('journalSummary', 'radio', array(
			'label' => 'plugins.gregg.journal.summary.display',
			'options' => array(
				'true' => 'plugins.gregg.journal.summary.display.true',
				'false' => 'plugins.gregg.journal.summary.display.false',
			)
		));
/*
		$this->addStyle('bootstrap', 'bootstrap/css/bootstrap.min.css');
		$this->addStyle('header', 'css/header.css');
		$this->addStyle('footer', 'css/footer.css');
		$this->addStyle('issue', 'css/issue.css');
		$this->addStyle('site-wide', 'css/main.css');
		$this->addStyle('index', 'css/index.css');
		$this->addStyle('article', 'css/article.less');
		$this->addStyle('contact', 'css/contact.css');
		$this->addStyle('announcements', 'css/announcements.css');
		$this->addStyle('jats', 'css/jats.min.css');
*/
		$this->addScript('jquery', 'jquery/jquery.min.js');
		$this->addScript('popper', 'bootstrap/js/popper.min.js');
		$this->addScript('bootstrap', 'bootstrap/js/bootstrap.min.js');
		$this->addScript('fontawesome', 'js/fontawesome-all.min.js');
		$this->addScript('article', 'js/article.js');
		$this->addScript('jats', "js/jats.min.js");

/*
		$this->addStyle(
			'my-custom-font1',
			'//fonts.googleapis.com/css?family=Lora',
			array('baseUrl' => 'https://fonts.googleapis.com/css?family=Lora" rel="stylesheet'));

		$this->addStyle(
			'my-custom-font2',
			'//fonts.googleapis.com/css?family=PT+Serif',
			array('baseUrl' => ''));

		$this->addStyle(
			'my-custom-font3',
			'//fonts.googleapis.com/css?family=Arimo',
			array('baseUrl' => ''));
		$this->addStyle(
			'my-custom-font4',
			'//fonts.googleapis.com/css?family=Alegreya',
			array('baseUrl' => ''));
		$this->addStyle(
			'my-custom-font5',
			'//fonts.googleapis.com/css?family=Play',
			array('baseUrl' => ''));
		$this->addStyle(
			'my-custom-font6',
			'//fonts.googleapis.com/css?family=Source+Sans+Pro',
			array('baseUrl' => ''));
		$this->addStyle(
			'my-custom-font7',
			'//fonts.googleapis.com/css?family=Alegreya+Sans',
			array('baseUrl' => ''));
		$this->addStyle(
			'my-custom-font8',
			'https://fonts.googleapis.com/css?family=Roboto',
			array('baseUrl' => ''));
*/
		$this->addMenuArea(array('primary', 'user'));

		HookRegistry::register('TemplateManager::display',array(&$this, 'xmlDownload'));
		HookRegistry::register('TemplateManager::display',array(&$this, 'htmlDisplay'));
		HookRegistry::register('TemplateManager::display', array($this, 'browseLatest'), HOOK_SEQUENCE_CORE);
		HookRegistry::register('TemplateManager::display', array($this, 'latestIssuesSlider'), HOOK_SEQUENCE_NORMAL);
		HookRegistry::register('TemplateManager::display', array($this, 'journalDescription'), HOOK_SEQUENCE_NORMAL);
	}

	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName() {
		return __('Bootstrap 3 Base Opscidia custom Theme');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription() {
		return __('Bootstrap 3 + oldGregg theme mixed for Opscidia custom theme');
	}

	// From OldGregg theme

	/**
	 * Callback that renders the issue galley.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function xmlDownload ($hookName, $args) {
		$templateMgr = $args[0];
		$template = $args[1];
		$request = $this->getRequest();

		if ($template !== 'frontend/pages/article.tpl') return false;

		$articleArrays = $templateMgr->getTemplateVars('article');
		$issueArrays = $templateMgr->getTemplateVars('issue');

		/* @var $articleArrays PublishedArticle
		 * @var $localizedGalley ArticleGalley
		 */

		/* Check whether there is localized PDF galley already (for example, uploaded by the user);
		 * Also checking if there is XML file uploaded for current locale;
		 */
		$boolEmbeddedPdf = false;
		$embeddedXml = null;

		// find PDF or XML in the current locale
		foreach ($articleArrays->getLocalizedGalleys() as $localizedGalley) {
			if ($localizedGalley->isPdfGalley()) $boolEmbeddedPdf = true;
			if ($localizedGalley->getFileType() === "application/xml" || $localizedGalley->getFileType() ==="text/xml") {
				$embeddedXml = $localizedGalley;
			}
		}

		// find any JATS XML
		if (!$embeddedXml) {
			foreach ($articleArrays->getGalleys() as $galley) {
				if ($galley->getFileType() === "application/xml" || $galley->getFileType() === "text/xml") {
					$embeddedXml = $galley;
				}
			}
		}

		if (!$embeddedXml) return false;

		$submissionFile = $embeddedXml->getFile();

		/* PHP Object model of JATS XML
		 * @var $submissionFile  SubmissionFile
		 */
		$jatsDocument = new \JATSParser\Body\Document($submissionFile->getFilePath());
		// HTML DOM
		$htmlDocument = $this->htmlCreation($templateMgr, $jatsDocument, $embeddedXml);
		// assigning DOM as a string to Smarty

		$orcidImage = $this->getPluginPath() . '/templates/images/orcid.png';

		$templateMgr->assign(array(
			"htmlDocument" => $htmlDocument->getHmtlForGalley(),
			'jatsParserOrcidImage' => $orcidImage,
		));

		// Handling PDFs; don't do anything if article already has downloaded PDF
		if ($boolEmbeddedPdf || !$embeddedXml) return false;
		// The string for PDF generating requests

		$generatePdfUrl = $request->getCompleteUrl() . "?" . CREATE_PDF_QUERY;
		$templateMgr->assign("generatePdfUrl", $generatePdfUrl);

		if ($request->getQueryString() !== CREATE_PDF_QUERY) return false;
		$this->pdfCreation($articleArrays, $request, $htmlDocument, $issueArrays, $templateMgr);

	}

	/**
	 * @param $articleArrays PublishedArticle
	 * @param $request PKPRequest
	 * @param $htmlDocument HTMLDocument
	 * @param $issueArrays Issue
	 * @param $templateMgr TemplateManager
	 */
	private function pdfCreation($articleArrays, $request, $htmlDocument, $issueArrays, $templateMgr): void
	{
		$journal = $request->getJournal();

		// extends TCPDF object
		$pdfDocument = new \JATSParser\PDF\TCPDFDocument();

		$pdfDocument->setTitle($articleArrays->getLocalizedFullTitle());

		// get the logo

		$journal = $request->getContext();
		$pdfHeaderLogo = __DIR__ . "/JATSParser/logo/Opscidia_logo.png";

		$pdfDocument->SetCreator(PDF_CREATOR);
		$pdfDocument->SetAuthor($articleArrays->getAuthorString());
		$pdfDocument->SetSubject($articleArrays->getLocalizedSubject());


		$articleDataString = $issueArrays->getIssueIdentification();
		if ($articleArrays->getPages()) {
			$articleDataString .= ", ". $articleArrays->getPages();
		}

		if ($articleArrays->getSectionTitle()) {
			$articleDataString .= "\n" . $articleArrays->getSectionTitle();
		}

//		$pdfDocument->SetHeaderData($pdfHeaderLogo, PDF_HEADER_LOGO_WIDTH, $journal->getLocalizedName(), $articleDataString);
		$pdfDocument->SetHeaderData($pdfHeaderLogo, 150, $journal->getLocalizedName(), $articleDataString);
		
		$pdfDocument->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//		$pdfDocument->setHeaderFont(Array('playfair', '', PDF_FONT_SIZE_MAIN));
		$pdfDocument->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		$pdfDocument->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdfDocument->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdfDocument->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdfDocument->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdfDocument->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdfDocument->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdfDocument->AddPage();

		/* An example of using translations inside PHP */
		//$translate = $templateMgr->smartyTranslate(array('key' =>'common.abstract'), $templateMgr);

		// Article title

		$pdfDocument->SetFillColor(255, 255, 255);
		$pdfDocument->SetFont('dejavuserif', 'B', 20);
		$pdfDocument->MultiCell('', '', $articleArrays->getLocalizedFullTitle(), 0, 'L', 1, 1, '' ,'', true);
		$pdfDocument->Ln(6);

		// Article's authors
		if (count($articleArrays->getAuthors()) > 0) {
			/* @var $author Author */
			foreach ($articleArrays->getAuthors() as $author) {
				$pdfDocument->SetFont('dejavuserif', 'I', 10);

				// Calculating the line height for author name and affiliation

				$authorLineWidth = 60;
				$authorNameStringHeight = $pdfDocument->getStringHeight($authorLineWidth, htmlspecialchars($author->getFullName()));

				$affiliationLineWidth = 110;
				$afilliationStringHeight = $pdfDocument->getStringHeight(110, htmlspecialchars($author->getLocalizedAffiliation()));

				$authorNameStringHeight > $afilliationStringHeight ? $cellHeight = $authorNameStringHeight : $cellHeight = $afilliationStringHeight;

				// Writing affiliations into cells
				$pdfDocument->MultiCell($authorLineWidth, 0, htmlspecialchars($author->getFullName()), 0, 'L', 1, 0, 19, '', true, 0, false, true, 0, "T", true);
				$pdfDocument->SetFont('dejavuserif', '', 10);
				$pdfDocument->MultiCell($affiliationLineWidth, $cellHeight, htmlspecialchars($author->getLocalizedAffiliation()), 0, 'L', 1, 1, '', '', true, 0, false, true, 0, "T", true);
			}
			$pdfDocument->Ln(6);
		}

		// Abstract
		if ($articleArrays->getLocalizedAbstract()) {
			$pdfDocument->setCellPaddings(5, 5, 5, 5);
			$pdfDocument->SetFillColor(248, 248, 255);
			$pdfDocument->SetFont('dejavuserif', '', 10);
			$pdfDocument->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 140, 0)));
			$pdfDocument->writeHTMLCell('', '', '', '', $articleArrays->getLocalizedAbstract(), 'B', 1, 1, true, 'J', true);
			$pdfDocument->Ln(4);
		}

		// Text (goes from JATSParser
		$pdfDocument->setCellPaddings(0, 0, 0, 0);
		$pdfDocument->SetFont('dejavuserif', '', 10);

		$htmlString = $htmlDocument->getHtmlForTCPDF();
		$pdfDocument->writeHTML($htmlString, true, false, true, false, '');

		$pdfDocument->Output('article.pdf', 'I');
	}

	/**
	 * @param $jatsDocument Document
	 * @param $templateMgr TemplateManager
	 * @return HTMLDocument HTMLDocument
	 */
	private function htmlCreation($templateMgr, $jatsDocument, $embeddedXml): \JATSParser\HTML\Document
	{
		// HTML DOM
		$htmlDocument = new \JATSParser\HTML\Document($jatsDocument);

		// Add the link to images

		$submissionFile = $embeddedXml->getFile();
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		import('lib.pkp.classes.submission.SubmissionFile'); // Constants
		$embeddableFiles = array_merge(
			$submissionFileDao->getLatestRevisions($submissionFile->getSubmissionId(), SUBMISSION_FILE_PROOF),
			$submissionFileDao->getLatestRevisionsByAssocId(ASSOC_TYPE_SUBMISSION_FILE, $submissionFile->getFileId(), $submissionFile->getSubmissionId(), SUBMISSION_FILE_DEPENDENT)
		);
		$referredArticle = null;
		$articleDao = DAORegistry::getDAO('ArticleDAO');
		$imageUrlArray = array();
		foreach ($embeddableFiles as $embeddableFile) {
			$params = array();
			if ($embeddableFile->getFileType() == 'image/png' || $embeddableFile->getFileType() == 'image/jpeg') {
				// Ensure that the $referredArticle object refers to the article we want
				if (!$referredArticle || $referredArticle->getId() != $embeddedXml->getSubmissionId()) {
					$referredArticle = $articleDao->getById($embeddedXml->getSubmissionId());
				}
				$fileUrl = Application::getRequest()->url(null, 'article', 'download', array($referredArticle->getBestArticleId(), $embeddedXml->getBestGalleyId(), $embeddableFile->getFileId()), $params);
				$imageUrlArray[$embeddableFile->getOriginalFileName()] = $fileUrl;
			}
		}

		// Replace link with actual path
		$xpath = new \DOMXPath($htmlDocument);
		$imageLinks = $xpath->evaluate("//img");
		foreach ($imageLinks as $imageLink) {
			if ($imageLink->hasAttribute("src")) {
				array_key_exists($imageLink->getAttribute("src"), $imageUrlArray);
				$imageLink->setAttribute("src", $imageUrlArray[$imageLink->getAttribute("src")]);
			}
		}

		// Localization of reference list title
		$referenceTitles = $xpath->evaluate("//h2[@id='reference-title']");
		$translateReference = $templateMgr->smartyTranslate(array('key' =>'submission.citations'), $templateMgr);
		if ($referenceTitles->length > 0) {
			foreach ($referenceTitles as $referenceTitle) {
				$referenceTitle->nodeValue = $translateReference;
			}
		}

		// Special treatment for table head
		$tableHeadRows = $xpath->evaluate("//thead/tr");
		foreach ($tableHeadRows as $tableHeadRow) {
			$tableHeadRow->setAttribute("align", "center");
			$tableHeadRow->setAttribute("style", "background-color:#f2e6ff;");
		}

		return $htmlDocument;
	}

	/* For retrieving articles from the database */
	public function browseLatest($hookName, $args)
	{
		$smarty = $args[0];
		$template = $args[1];
		if ($template != 'frontend/pages/indexJournal.tpl') return false;
		/* get number of latest article to display from user input; if there was none - use default */
		$latestArticles = $this->getOption("latestArticlesNumber");
		if (is_null($latestArticles)) {
			$latestArticles = OLDGREGG_LATEST_ARTICLES_DEFAULT;
		} else {
			$latestArticles = intval($latestArticles);
		}
		$rangeArticles = new DBResultRange($latestArticles, 1);
		$publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
		/* retrieve current journal id from the request */
		$request = $this->getRequest();
		$journal = $request->getJournal();
		$journalId = $journal->getId();
		/* retrieve latest articles */
		$publishedArticleObjects = $publishedArticleDao->getPublishedArticlesByJournalId($journalId, $rangeArticles, $reverse = true);
		$publishedArticles = array();
		while ($publishedArticle = $publishedArticleObjects->next()) {
			$publishedArticles[] = $publishedArticle;
		}
		$smarty->assign('publishedArticles', $publishedArticles);
	}

	public function latestIssuesSlider($hookName, $args) {
		$smarty = $args[0];
		$template = $args[1];

		if ($template != 'frontend/pages/indexJournal.tpl') return false;

		$latestIssuesInput = $this->getOption("displayIssuesSlider");
		if (is_null($latestIssuesInput)) {
			$latestIssuesInput = OLDGREGG_LATEST_ISSUES_DEFAULT;
		} elseif (intval($latestIssuesInput) === 0) {
			return false;
		} else {
			$latestIssuesInput = intval($latestIssuesInput);
		}

		$request = $this->getRequest();
		$journal = $request->getJournal();
		$journalId = $journal->getId();

		$issueDao = DAORegistry::getDAO('IssueDAO');
		$rangeIssues = new DBResultRange($latestIssuesInput, 1);
		$latestIssuesObjects = $issueDao->getPublishedIssues($journalId, $rangeIssues);

		$latestIssues = array();
		while ($latestIssue = $latestIssuesObjects->next()) {
			$latestIssues[] = $latestIssue;
		}

		$defaultCoverImageUrl = "/" . $this->getPluginPath() . "/" . OLDGREGG_ISSUE_COVER_RELATIVE_URL;

		$smarty->assign('latestIssues', $latestIssues);
		$smarty->assign('defaultCoverImageUrl', $defaultCoverImageUrl);
	}

	public function journalDescription ($hookName, $args) {
		$smarty = $args[0];

		$showSummaryData = $this->getOption("journalSummary");

		$showSummary = false;
		if (!is_null($showSummaryData) && ($showSummaryData == "true")) {
			$showSummary = true;
		}

		$smarty->assign('showSummary', $showSummary);
	}

}

?>

