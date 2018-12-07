<?php

/**
 * @file plugins/generic/entityFishing/EntityFishingSettingsForm.inc.php
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
 * @class EntityFishingSettingsForm
 * @ingroup plugins_generic_entityFishing
 *
 * @brief Form for adding/editing the settings for the EntityFishing plugin
 */

import('lib.pkp.classes.form.Form');

class EntityFishingSettingsForm extends Form {
	/** @var Context The press associated with the plugin being edited */
	var $_context;

	/** @var EntityFishingBlockPlugin The plugin being edited */
	var $_plugin;

	/**
	 * Constructor.
	 * @param $plugin EntityFishingBlockPlugin
	 * @param $context Context
	 */
	function __construct($plugin, $context) {
		parent::__construct();
		$this->setContext($context);
		$this->setPlugin($plugin);

		// Validation checks for this form
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	//
	// Getters and Setters
	//
	/**
	 * Get the Context.
	 * @return Context
	 */
	function getContext() {
		return $this->_context;
	}

	/**
	 * Set the Context.
	 * @param Context
	 */
	function setContext($context) {
		$this->_context = $context;
	}

	/**
	 * Get the plugin.
	 * @return EntityFishingBlockPlugin
	 */
	function getPlugin() {
		return $this->_plugin;
	}

	/**
	 * Set the plugin.
	 * @param EntityFishingBlockPlugin $plugin
	 */
	function setPlugin($plugin) {
		$this->_plugin = $plugin;
	}

	//
	// Overridden template methods
	//
	/**
	 * Initialize form data from the plugin.
	 */
	function initData() {
		$plugin = $this->getPlugin();
		$context = $this->getContext();

		if (isset($plugin)) {
			$this->_data = array(
				'entityFishingNerdUrl' => $plugin->getSetting(isset($context)?$context->getId():null,'entityFishingNerdUrl'),
				'solrUrl' => $plugin->getSetting(isset($context)?$context->getId():null,'solrUrl'),
				'solrPort' => $plugin->getSetting(isset($context)?$context->getId():null,'solrPort'),
				'solrPath' => $plugin->getSetting(isset($context)?$context->getId():null,'solrPath'),
				'efTagCloudEnabled' => $plugin->getSetting(isset($context)?$context->getId():null,'efTagCloudEnabled'),
				'efAbstractEnabled' => $plugin->getSetting(isset($context)?$context->getId():null,'efAbstractEnabled'),
				'efLandingEnabled' => $plugin->getSetting(isset($context)?$context->getId():null,'efLandingEnabled')
			);
		}
	}

	/**
	 * Fetch the form.
	 * @see Form::fetch()
	 * @param $request PKPRequest
	 */
	function fetch($request) {
		$plugin = $this->getPlugin();
		$context = $this->getContext();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $plugin->getName());
		$templateMgr->assign('pluginBaseUrl', $request->getBaseUrl() . '/' . $plugin->getPluginPath());

		foreach ($this->_data as $key => $value) {
			$templateMgr->assign($key, $value);
		}

		return $templateMgr->fetch($plugin->getTemplatePath() . 'settings/settings_'.(isset($context)?'context':'admin').'.tpl');
	}

	/**
	 * Assign form data to user-submitted data.
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$context = $this->getContext();
		if (isset($context)) {
			$this->readUserVars(array(
				'efTagCloudEnabled',
				'efAbstractEnabled',
				'efLandingEnabled'
			));
		}
		else {
			$this->readUserVars(array(
				'entityFishingNerdUrl',
				'solrUrl',
				'solrPort',
				'solrPath'
			));
		}
	}

	/**
	 * Save the plugin's data.
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$context = $this->getContext();

		if (isset($context)) {
			$plugin->updateSetting($context->getId(),'efTagCloudEnabled', $this->getData('efTagCloudEnabled'), 'bool');
			$plugin->updateSetting($context->getId(),'efAbstractEnabled', $this->getData('efAbstractEnabled'), 'bool');
			$plugin->updateSetting($context->getId(),'efLandingEnabled', $this->getData('efLandingEnabled'), 'bool');
		}
		else {
			$plugin->updateSetting(null, 'entityFishingNerdUrl', trim($this->getData('entityFishingNerdUrl'), "\"\';"), 'string');
			$plugin->updateSetting(null, 'solrUrl', trim($this->getData('solrUrl'), "\"\';"), 'string');
			$plugin->updateSetting(null, 'solrPort', trim($this->getData('solrPort'), "\"\';"), 'string');
			$plugin->updateSetting(null, 'solrPath', trim($this->getData('solrPath'), "\"\';"), 'string');
		}
	}
}
?>
