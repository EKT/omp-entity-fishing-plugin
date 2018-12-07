<?php

/**
 * @file plugins/generic/entityFishing/EntityFishingPlugin.inc.php
 *
 * Copyright (c) 2018 Hirmeos EU Project (http://hirmeos.eu/)
 * Copyright (c) 2018 National Documentation Center (http://www.ekt.gr/)
 * Copyright (c) 2018 Kostas Stamatis
 *
 * This is a deliverable of the HIRMEOS project. The project has received funding from the European Union’s
 * Horizon 2020 research and innovation programme under grant agreement No 731102.
 *
 * Distributed under CC-BY-NC-SA. For full terms see: http://creativecommons.org/licenses/by-nc-sa/4.0/.
 *
 * @class EntityFishingPlugin
 *
 * @brief This plugin provides the entity fishing support via the nerd API (https://github.com/kermitt2/entity-fishing).
 */

import('lib.pkp.classes.plugins.GenericPlugin');
require_once('plugins/generic/entityFishing/vendor/lotsofcode/tag-cloud/src/lotsofcode/TagCloud/TagCloud.php');

class EntityFishingPlugin extends GenericPlugin {

	/**
	 * Register the plugin.
	 * @param $category string
	 * @param $path string
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {

			if ($this->getEnabled()) {

				$request =& Registry::get('request');
				$context = $request->getContext();

				HookRegistry::register('TemplateManager::display', array($this, 'callbackTemplateDisplay')); // OMP

				HookRegistry::register('Templates::Workflow::production', array($this, 'callbackTemplateFetch')); // OMP

				if ($context) {
					$tagCloudEnabled = $this->getSetting($context->getID(), "efTagCloudEnabled");
					if ($tagCloudEnabled) {
						HookRegistry::register('Templates::Common::Sidebar', array($this, 'callbackSidebarDisplay')); // OMP
					}

					$landingPageEnabled = $this->getSetting($context->getID(), "efLandingEnabled");
					if ($landingPageEnabled) {
						HookRegistry::register('Templates::Catalog::Book::Main', array($this, 'callbackBook')); // OMP
					}
				}

				// Register the components this plugin implements
				HookRegistry::register('LoadComponentHandler', array($this, 'setupHandler'));
				$this->_registerTemplateResource();
			}
			return true;
		}
		return false;
	}

	/*
	 * Enable plugin only if the global enabled by admin is true and the specific context is also true
	 */
	function getEnabled() {
		$globalEnabled = $this->getSetting(null, 'enabled');
		return parent::getEnabled() && $globalEnabled;
	}

	function isSitePlugin() {
		return !Application::getRequest()->getContext();
	}

	function setupHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.entityFishing.controllers.browse.TagcloudHandler' || $component == 'plugins.generic.entityFishing.controllers.grid.EntityFishingGridHandler') {
			// Allow the static page grid handler to get the plugin object
			import($component);
			return true;
		}
		return false;
	}

	/**
	 * Get the name of the settings file to be installed on new press
	 * creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * @copydoc PKPPlugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.entityFishing.settings.displayName');
	}

	/**
	 * @copydoc PKPPlugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.entityFishing.settings.description');
	}

	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
    function getTemplatePath($inCore = false) {
		return $this->getTemplateResourceName() . ':templates/';
    }

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $actionArgs) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array_merge($actionArgs, array('verb' => 'settings'))),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			):array(),
			parent::getActions($request, $actionArgs)
		);
	}

	/**
	 * @copydoc PKPPlugin::manage()
	 */
	function manage($args, $request) {
		$context = $request->getContext();
		$templateMgr = TemplateManager::getManager($request);

		switch ($request->getUserVar('verb')) {
			case 'showTab':
				switch ($request->getUserVar('tab')) {
					case 'settings':
						$this->import('EntityFishingSettingsForm');
						$form = new EntityFishingSettingsForm($this, $context);
						if ($request->getUserVar('save')) {
							$form->readInputData();
							if ($form->validate()) {
								$form->execute();
								return new JSONMessage();
							}
						} else {
							$form->initData();
						}
						return new JSONMessage(true, $form->fetch($request));
					default: assert(false);
				}
			case 'settings':
				$templateMgr->assign('pluginName', $this->getName());
				return $templateMgr->fetchJson($this->getTemplatePath() . 'settings/settingsTabs.tpl');

		}
		return parent::manage($args, $request);
	}

	function callbackTemplateDisplay($hookName, $params)
	{
		$templateMgr = $params[0];
		$request = $this->getRequest();
		$context = $request->getContext();
		$template =& $params[1];

		switch ($template) {
			case 'frontend/pages/book.tpl':
				$abstractEnabled = $this->getSetting($context->getID(), "efAbstractEnabled");
				if ($abstractEnabled) {
					$templateMgr->register_outputfilter(array(&$this, 'abstractFilter'));
				}
				break;
			default: break;
		}

		$baseImportPath = Request::getBaseUrl() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $this->getPluginPath() . DIRECTORY_SEPARATOR;
		$templateMgr->addStyleSheet(
			'nerd',
			$baseImportPath . 'css/entity-fishing.css'
		);

		$templateMgr->addStyleSheet(
			'tagcloud',
			$baseImportPath . 'vendor/lotsofcode/tag-cloud/css/tagcloud.css'
		);

		$templateMgr->addStyleSheet(
			'bootstracss',
			'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'
		);

		$templateMgr->addJavascript(
			'bootstrapjs',
			'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'
		);

		$baseImportPath = Request::getBaseUrl() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $this->getPluginPath() . DIRECTORY_SEPARATOR;
		$templateMgr->addJavascript(
			'askDialogAjax',
			$baseImportPath . 'js/AskDialogAjaxFormHandler.js',
			array( "contexts" => "backend")
		);

		return false;
	}

	function callbackTemplateFetch($hookName, $params)
	{

		$templateMgr = $params[1];
		$output =& $params[2];

		$output .= $templateMgr->fetch($this->getTemplatePath() . '/controllers/publicationFormats.tpl');

		return false;
	}

	function callbackSidebarDisplay($hookName, $args)
	{
		$params =& $args[0];
		$templateMgr =& $args[1];
		$output =& $args[2];

		$request = $this->getRequest();
		$context = $request->getContext();

		if ($this->getEnabled() && isset($context)) {
			$output .= $templateMgr->fetch($this->getTemplatePath() . 'frontend/sidebar_block.tpl');
		}
		return false;
	}

	function callbackBook($hookName, $args)
	{
		$params =& $args[0];
		$templateMgr =& $args[1];
		$output =& $args[2];

		$publishedMonograph = $templateMgr->_tpl_vars["publishedMonograph"];
		$request = $this->getRequest();
		$context = $request->getContext();

		require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');
		$entities = (new SolrIndex())->getFacets($context, $publishedMonograph);

		$class = 'lotsofcode\TagCloud\TagCloud';
		$cloud = new $class();

		$baseUrl = "entities";

		$cloud->setOption("transformation", null);
		$cloud->setOption("transliterate", false);
		$cloud->setOrder("size", "DESC");
		$cloud->setLimit(100);

		for ($x = 0; $x < count($entities); $x++) {
			$cloud->addTag($entities[$x]);
		}

		$html = $cloud->render();
		$output .= $html;

		return false;
	}

	function getAbstractEntities($abstract) {
		//$abstract = $this->getLocalizedAbstract();
		require_once('plugins/generic/entityFishing/vendor/hirmeos/entity-fishing-php-wrapper/EFWebServiceManager.php');
		$theresponse = EFWebServiceManager::getInstance()->disambiguateText($abstract, "en");

		$nerd_entities = $theresponse->entities;

		if ($theresponse->has_error){
			return "[ERROR] " . $theresponse->error_msg;
		}
		else {
			for( $i = 0; $i<count($theresponse->entities); $i++ ) {
				$index = count($theresponse->entities)-$i-1;
				$entity = $theresponse->entities[$index];

				if ($entity->wikidata_id) {
					$entity->concept_response = EFWebServiceManager::getInstance()->concept($entity->wikidata_id);
				}

				$abstract_before = mb_substr($abstract,0, $entity->offset_start);
				$abstract_after = mb_substr($abstract,$entity->offset_end);
				$abstract_middle = mb_substr($abstract,$entity->offset_start,$entity->offset_end-$entity->offset_start);

				$custom_class = NULL;
				if (!is_null($entity->type)){
					$custom_class=$entity->type;
				}
				for( $j = 0; $j<count($entity->domains); $j++ ) {
					if (!is_null($custom_class)){
						$custom_class = $custom_class." ";
					}
					else {
						$custom_class = "";
					}
					$custom_class = $custom_class . $entity->domains[$j];
				}
				$custom_class = strtolower($custom_class);
				$abstract = $abstract_before . "<span class='nerd nerd_".$entity->offset_start." ".$custom_class."'>".$abstract_middle."</span>" . $abstract_after;
			}

			return array($abstract, $nerd_entities);
		}
	}

	function abstractFilter($output, &$templateMgr) {
		if (preg_match('/<div class="item abstract">/', $output, $matches, PREG_OFFSET_CAPTURE)) {
			$match = $matches[0][0];
			$offset = $matches[0][1];
			$context = Request::getContext();
			$contextId = ($context == null) ? 0 : $context->getId();

			$publishedMonograph = $templateMgr->_tpl_vars["publishedMonograph"];
			$abstract = $this->getAbstractEntities($publishedMonograph->getLocalizedAbstract());

			$templateMgr->assign(array(
				'abstract' => $abstract[0],
				'nerd_entities' => $abstract[1]
			));

			$newOutput = substr($output, 0, $offset+strlen($match));
			$newOutput .= $templateMgr->fetch($this->getTemplatePath() . 'frontend/abstract.tpl');
			$newOutput .= substr($output, $offset+strlen($match));
			$output = $newOutput;
		}
		$templateMgr->unregister_outputfilter('registrationFilter');
		return $output;
	}
}

?>
