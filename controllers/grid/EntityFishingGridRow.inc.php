<?php

/**
 * @file plugins/generic/entityFishing/classes/controllers/grid/EntityFishingGridRow.inc.php
 *
 * Copyright (c) 2018 Hirmeos EU Project (http://hirmeos.eu/)
 * Copyright (c) 2018 National Documentation Center (http://www.ekt.gr/)
 * Copyright (c) 2018 Kostas Stamatis
 *
 * This is a deliverable of the HIRMEOS project. The project has received funding from the European Union’s
 * Horizon 2020 research and innovation programme under grant agreement No 731102.
 *
 * Distributed under the GNU GPLv3. For full terms see: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @class EntityFishingGridHandler
 *
 * @brief Handle entity fishing publication format grid row requests
 */

import('lib.pkp.controllers.grid.files.SubmissionFilesGridRow');
import('lib.pkp.classes.controllers.grid.files.FilesGridCapabilities');

class EntityFishingGridRow extends SubmissionFilesGridRow {
	/** @var boolean */
	protected $_canManage;

	/**
	 * Constructor
	 * @param $canManage boolean
	 */
	function __construct($canManage) {
		$this->_canManage = $canManage;
		parent::__construct(
			new FilesGridCapabilities(
			),
			WORKFLOW_STAGE_ID_PRODUCTION
		);
	}

	//
	// Overridden template methods from GridRow
	//
	/**
	 * @copydoc PKPHandler::initialize()
	 */
	function initialize($request, $template = 'controllers/grid/gridRow.tpl') {
		parent::initialize($request, $template);
		$submissionFileData =& $this->getData();
		$submissionFile =& $submissionFileData['submissionFile']; /* @var $submissionFile SubmissionFile */
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$router = $request->getRouter();
	}
}

?>
