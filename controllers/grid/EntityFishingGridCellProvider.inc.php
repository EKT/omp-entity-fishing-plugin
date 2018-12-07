<?php

/**
 * @file plugins/generic/entityFishing/classes/controllers/grid/EntityFishingGridCellProvider.inc.php
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
 * @class EntityFishingGridHandler
 *
 * @brief Base class for a cell provider that can retrieve labels for entity fishing publication formats
 */

import('lib.pkp.classes.controllers.grid.DataObjectGridCellProvider');

// Import class which contains the SUBMISSION_FILE_* constants.
import('lib.pkp.classes.submission.SubmissionFile');
import ('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');

class EntityFishingGridCellProvider extends DataObjectGridCellProvider {

	/** @var int Submission ID */
	var $_submissionId;

	/** @var boolean */
	protected $_canManage;

	/**
	 * Constructor
	 * @param $submissionId int Submission ID
	 * @param $canManage boolean
	 */
	function __construct($submissionId, $canManage) {
		parent::__construct();
		$this->_submissionId = $submissionId;
		$this->_canManage = $canManage;
	}


	//
	// Getters and setters.
	//
	/**
	 * Get submission ID.
	 * @return int
	 */
	function getSubmissionId() {
		return $this->_submissionId;
	}


	//
	// Template methods from GridCellProvider
	//
	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$data = $row->getData();
		if (is_a($data, 'Representation')) switch ($column->getId()) {
			case 'indent': return array();
			case 'name':
				$remoteURL = $data->getRemoteURL();
				if ($remoteURL) {
					return array('label' => '<a href="'.htmlspecialchars($remoteURL).'" target="_blank">'.htmlspecialchars($data->getLocalizedName()).'</a>' . '<span class="onix_code">' . $data->getNameForONIXCode() . '</span>');
				}
				return array('label' => htmlspecialchars($data->getLocalizedName()) . '<span class="onix_code">' . $data->getNameForONIXCode() . '</span>');
			case 'isAvailable':
				return array('status' => $data->getIsAvailable()?'completed':'new');
		} else {
			assert(is_array($data) && isset($data['submissionFile']));
			$proofFile = $data['submissionFile'];
			switch ($column->getId()) {
				case 'isAvailable':
					return array('status' => ($proofFile->getSalesType() != null && $proofFile->getDirectSalesPrice() != null)?'completed':'new');
				case 'name':
					import('lib.pkp.controllers.grid.files.FileNameGridColumn');
					$fileNameGridColumn = new FileNameGridColumn(true, WORKFLOW_STAGE_ID_PRODUCTION);
					return $fileNameGridColumn->getTemplateVarsFromRow($row);
			}
		}
		return parent::getTemplateVarsFromRowColumn($row, $column);
	}

	/**
	 * @see GridCellProvider::getCellActions()
	 */
	function getCellActions($request, $row, $column) {
		$data = $row->getData();
		$router = $request->getRouter();
		if (is_a($data, 'Representation')) {
			switch ($column->getId()) {
				case 'isAvailable':
					return array();
				case 'name':
					return array();
			}
		} else {
			assert(is_array($data) && isset($data['submissionFile']));
			$submissionFile = $data['submissionFile'];

			$request =& Registry::get('request');
			$context = $request->getContext();
			require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');
			$indexed = (new SolrIndex())->checkIfSubmissionIsIndexed($context, $submissionFile);

			switch ($column->getId()) {
				case 'isComplete':
					$salesType = preg_replace('/[^\da-z]/i', '', $submissionFile->getSalesType());
					$salesTypeString = 'editor.monograph.approvedProofs.edit.linkTitle';
					if ($salesType == 'openAccess') {
						$salesTypeString = 'payment.directSales.openAccess';
					} elseif ($salesType == 'directSales') {
						$salesTypeString = 'payment.directSales.directSales';
					} elseif ($salesType == 'notAvailable') {
						$salesTypeString = 'payment.directSales.notAvailable';
					}
					return array(new LinkAction(
						'editApprovedProof',
						new AjaxModal(
							$router->url($request, null, null, 'editApprovedProof', null, array(
								'fileId' => $submissionFile->getFileId() . '-' . $submissionFile->getRevision(),
								'submissionId' => $submissionFile->getSubmissionId(),
								'representationId' => $submissionFile->getAssocId(),
							)),
							__('editor.monograph.approvedProofs.edit'),
							'edit'
						),
						__($salesTypeString),
						$salesType
					));
				case 'name':
					import('lib.pkp.controllers.grid.files.FileNameGridColumn');
					$fileNameColumn = new FileNameGridColumn(true, WORKFLOW_STAGE_ID_PRODUCTION, true);
					return $fileNameColumn->getCellActions($request, $row);
				case 'isAvailable':
					AppLocale::requireComponents(LOCALE_COMPONENT_PKP_EDITOR);
					import('lib.pkp.classes.linkAction.request.AjaxModal');
					$title = __($indexed?'plugins.generic.entityFishing.publicationformats.status.indexed':'plugins.generic.entityFishing.publicationformats.status.nonindexed');
					return array(new LinkAction(
						$indexed?'approved':'not_approved',
						new AjaxModal(
							$router->url(
								$request, null, 'plugins.generic.entityFishing.controllers.grid.EntityFishingGridHandler', 'setIndexFileCompletion',
								null,
								array(
									'submissionId' => $submissionFile->getSubmissionId(),
									'fileId' => $submissionFile->getFileId(),
									'revision' => $submissionFile->getRevision(),
									'indexed' => $indexed,
								)
							),
							$title,
							'modal_approve'
						),
						$indexed?__('plugins.generic.entityFishing.publicationformats.status.indexed'):__('plugins.generic.entityFishing.publicationformats.status.nonindexed'),
						$indexed?'complete':'incomplete',
						__('grid.action.setApproval')
					));
			}
		}
		return parent::getCellActions($request, $row, $column);
	}
}

?>
