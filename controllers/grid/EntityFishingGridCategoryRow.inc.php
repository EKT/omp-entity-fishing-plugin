<?php

/**
 * @file plugins/generic/entityFishing/classes/controllers/grid/EntityFishingGridCategoryRow.inc.php
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
 * @brief Representations grid row definition for entity fishing plugin
 */

import('lib.pkp.classes.controllers.grid.GridCategoryRow');

class EntityFishingGridCategoryRow extends GridCategoryRow {

	/** @var Submission **/
	var $_submission;

	/** @var boolean */
	protected $_canManage;

	/**
	 * Constructor
	 * @param $submission Submission
	 * @param $cellProvider GridCellProvider
	 * @param $canManage boolean
	 */
	function __construct($submission, $cellProvider, $canManage) {
		$this->_submission = $submission;
		$this->_canManage = $canManage;
		parent::__construct();
		$this->setCellProvider($cellProvider);
	}

	//
	// Overridden methods from GridCategoryRow
	//
	/**
	 * @copydoc GridCategoryRow::getCategoryLabel()
	 */
	function getCategoryLabel() {
		return $this->getData()->getLocalizedName();
	}


	//
	// Overridden methods from GridRow
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		// Do the default initialization
		parent::initialize($request, $template);

		// Retrieve the submission from the request
		$submission = $this->getSubmission();

		// Is this a new row or an existing row?
		$representation = $this->getData();
		if ($representation && is_numeric($representation->getId()) && $this->_canManage) {
			$router = $request->getRouter();
			$actionArgs = array(
				'submissionId' => $submission->getId(),
				'representationId' => $representation->getId()
			);
		}
	}

	/**
	 * Get the submission for this row (already authorized)
	 * @return Submission
	 */
	function getSubmission() {
		return $this->_submission;
	}
}
?>
