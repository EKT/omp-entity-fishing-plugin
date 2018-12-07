<?php

/**
 * @file plugins/generic/entityFishing/classes/controllers/browse/TagCloudHandler.php
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
 * @class TagCloudHandler
 *
 * @brief Handle entity tagcloud functionality.
 */

import('classes.handler.Handler');

// import UI base classes
import('lib.pkp.classes.linkAction.LinkAction');
import('lib.pkp.classes.core.JSONMessage');
require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');
require_once('plugins/generic/entityFishing/vendor/lotsofcode/tag-cloud/src/lotsofcode/TagCloud/TagCloud.php');

class TagCloudHandler extends Handler {
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();

		//$plugin = PluginRegistry::getPlugin('generic', 'usagestatsplugin'); /* @var $plugin UsageStatsPlugin */

	}


	//
	// Overridden methods from Handler
	//
	/**
	 * @see PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.ContextRequiredPolicy');
		$this->addPolicy(new ContextRequiredPolicy($request));
		return parent::authorize($request, $args, $roleAssignments);
	}


	//
	// Public handler methods
	//
	/**
	 * Show the catalog home.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function entities($args, $request) {

		$context = $request->getContext();
		$templateMgr = TemplateManager::getManager($request);
		$this->setupTemplate($request);

		require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');

		if (isset($args['entity'])){
			$docs = (new SolrIndex())->searchByEntity($context, $args['entity']);

			if ($docs) {
				$publishedMonographs = array();

				$entity = null;
				foreach ($docs as $doc) {
					$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');
					$monogrpahId = (int)$doc['monographID'];
					$publishedMonograph = $publishedMonographDao->getById($monogrpahId);
					if ($publishedMonograph) {
						$publishedMonographs[$publishedMonograph->getId()] = $publishedMonograph;
					}

					require_once('plugins/generic/entityFishing/vendor/hirmeos/entity-fishing-php-wrapper/EFWebServiceManager.php');
					$nerd_response = new EFDisambiguatePDFWebResponse($request, $doc['json']);
					for ($x = 0; $x <= count($nerd_response->entities); $x++) {
						$entitytemp = $nerd_response->entities[$x];
						$rawname = $entitytemp->raw_name;
						if ($rawname == $args['entity']) {
							$entity = $entitytemp;
							$concept_response = EFWebServiceManager::getInstance()->concept($entity->wikidata_id, null);
						}
					}
				}

				$templateMgr->assign('entityObj', $entity);
				$templateMgr->assign('conceptResponse', $concept_response);
				$templateMgr->assign('publishedMonographs', $publishedMonographs);
			}
			else {
				$templateMgr->assign('problem', "plugins.generic.entityFishing.error.generic");
			}

			$templateMgr->assign('entity', $args['entity']);

			// Display
			array_push($templateMgr->template_dir, 'plugins/generic/entityFishing/templates/frontend');
			$templateMgr->display('search_results.tpl');
		}
		else {
			$facet_fields = (new SolrIndex())->getFacets($context);
			if ($facet_fields) {
				$facet_names = $facet_fields->getPropertyNames();

				$class = 'lotsofcode\TagCloud\TagCloud';
				$cloud = new $class();

				$baseUrl = "entities";

				$cloud->setHtmlizeTagFunction(function ($tag, $size) use ($baseUrl) {
					$link = '<a href="' . $baseUrl . '?entity=' . $tag['tag'] . '">' . $tag['tag'] . '</a>';
					return "<span class='tag size{$size}'>{$link}</span> ";
				});


				$cloud->setOption("transformation", null);
				$cloud->setOption("transliterate", false);
				$cloud->setLimit(100);

				for ($x = 0; $x < count($facet_names); $x++) {
					$facet_name = $facet_names[$x];
					$times = $facet_fields->offsetGet($facet_name);

					if ($times > 0) {
						$cloud->addTag(array('tag' => $facet_name, 'size' => $times));
					}
				}

				$html = $cloud->render();
				$templateMgr->assign('tagcloud_html', $html);
			}
			else {
				$problem = "plugins.generic.entityFishing.error.generic";
				$templateMgr->assign('problem', $problem);
			}

			// Display
			array_push($templateMgr->template_dir, 'plugins/generic/entityFishing/templates/frontend');
			$templateMgr->display('tagcloud.tpl');
		}
	}
}

?>
