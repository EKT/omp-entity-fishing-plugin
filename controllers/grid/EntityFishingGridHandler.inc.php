<?php

/**
 * @file plugins/generic/entityFishing/classes/controllers/grid/EntityFishingGridHandler.inc.php
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
 * @brief Handle entity fishing publication format grid requests.
 */

// import grid base classes
import('lib.pkp.classes.controllers.grid.CategoryGridHandler');

// import format grid specific classes
import('plugins.generic.entityFishing.controllers.grid.EntityFishingGridRow');
import('plugins.generic.entityFishing.controllers.grid.EntityFishingGridCategoryRow');
import('controllers.grid.catalogEntry.PublicationFormatCategoryGridDataProvider');
import('plugins.generic.entityFishing.controllers.grid.EntityFishingGridCategoryRow');

// Link action & modal classes
import('lib.pkp.classes.linkAction.request.AjaxModal');

class EntityFishingGridHandler extends CategoryGridHandler {
	/** @var PublicationFormatGridCellProvider */
	var $_cellProvider;

	/** @var Submission */
	var $_submission;

	/** @var boolean */
	protected $_canManage;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct(new PublicationFormatCategoryGridDataProvider($this));
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR),
			array(
				'setIndexed', 'setIndexFileCompletion'
			)
		);
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR),
			array(
				'fetchGrid', 'fetchRow', 'fetchCategory',
			)
		);
	}

	//
	// Getters/Setters
	//
	/**
	 * Get the submission associated with this publication format grid.
	 * @return Submission
	 */
	function getSubmission() {
		return $this->_submission;
	}

	/**
	 * Set the submission
	 * @param $submission Submission
	 */
	function setSubmission($submission) {
		$this->_submission = $submission;
	}


	//
	// Overridden methods from PKPHandler
	//
	/**
	 * Configure the grid
	 * @param $request PKPRequest
	 */
	function initialize($request) {
		parent::initialize($request);

		// Retrieve the authorized submission.
		$this->setSubmission($this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION));

		// Load submission-specific translations
		AppLocale::requireComponents(
			LOCALE_COMPONENT_PKP_SUBMISSION,
			LOCALE_COMPONENT_PKP_EDITOR,
			LOCALE_COMPONENT_PKP_USER,
			LOCALE_COMPONENT_PKP_DEFAULT
		);
		$this->setTitle('plugins.generic.entityFishing.publicationformats.title');

		// Load submission-specific translations
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_SUBMISSION,
			LOCALE_COMPONENT_APP_DEFAULT,
			LOCALE_COMPONENT_APP_EDITOR
		);

		// Grid actions
		$router = $request->getRouter();
		$actionArgs = $this->getRequestArgs();
		$userRoles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
		$this->_canManage = 0 != count(array_intersect($userRoles, array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT)));


		// Columns
		$submission = $this->getSubmission();
		import('plugins.generic.entityFishing.controllers.grid.EntityFishingGridCellProvider');
		$this->_cellProvider = new EntityFishingGridCellProvider($submission->getId(), $this->_canManage);
		$this->addColumn(
			new GridColumn(
				'name',
				'common.name',
				null,
				null,
				$this->_cellProvider,
				array('width' => 60, 'anyhtml' => true)
			)
		);
		if ($this->_canManage) {

			$this->addColumn(
				new GridColumn(
					'isAvailable',
					'plugins.generic.entityFishing.publicationformats.status.title',
					null,
					'controllers/grid/common/cell/statusCell.tpl',
					$this->_cellProvider,
					array('width' => 20)
				)
			);
		}
	}

	function requireSSL() {
		return false;
	}

	/**
	 * @see PKPHandler::authorize()
	 * @param $request PKPRequest
	 * @param $args array
	 * @param $roleAssignments array
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.SubmissionAccessPolicy');
		$this->addPolicy(new SubmissionAccessPolicy($request, $args, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}


	//
	// Overridden methods from GridHandler
	//
	/**
	 * @see GridHandler::getRowInstance()
	 * @return PublicationFormatGridCategoryRow
	 */
	function getCategoryRowInstance() {
		return new EntityFishingGridCategoryRow($this->getSubmission(), $this->_cellProvider, $this->_canManage);
	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	function getRowInstance() {
		return new EntityFishingGridRow($this->_canManage);
	}

	/**
	 * Get the arguments that will identify the data in the grid
	 * In this case, the submission.
	 * @return array
	 */
	function getRequestArgs() {
		return array(
			'submissionId' => $this->getSubmission()->getId(),
		);
	}

	/**
	 * Set the indexed status for a file.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function setIndexFileCompletion($args, $request) {
		$submission = $this->getSubmission();

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		import('lib.pkp.classes.submission.SubmissionFile'); // Constants
		$submissionFile = $submissionFileDao->getRevision(
			$request->getUserVar('fileId'),
			$request->getUserVar('revision'),
			SUBMISSION_FILE_PROOF,
			$submission->getId()
		);

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign("submission", $submission);
		$templateMgr->assign("submissionFile", $submissionFile);
		$templateMgr->assign("indexed", $args["indexed"]);
		array_push($templateMgr->template_dir, 'plugins/generic/entityFishing/templates');
		return new JSONMessage(true, $templateMgr->fetch('controllers/askDialog.tpl'));
	}

	/**
	 * Set a file's "indexed" state
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function setIndexed($args, $request) {
		$submission = $this->getSubmission();

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		import('lib.pkp.classes.submission.SubmissionFile'); // Constants
		$submissionFile = $submissionFileDao->getRevision(
			$request->getUserVar('fileId'),
			$request->getUserVar('revision'),
			SUBMISSION_FILE_PROOF,
			$submission->getId()
		);

		$context = $request->getContext();
		require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');
		$indexed = (new SolrIndex())->checkIfSubmissionIsIndexed($context, $submissionFile);

		if ($indexed){
			require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');
			(new SolrIndex())->removeFromIndex($request->getContext(), $submission, $submissionFile);

			import('lib.pkp.classes.core.JSONMessage');
			$json = new JSONMessage(true, '');
			return $json;
		}
		else {
			$path = $submissionFile->getFilePath();
			require_once('plugins/generic/entityFishing/vendor/hirmeos/entity-fishing-php-wrapper/EFWebServiceManager.php');

			$result = EFWebServiceManager::getInstance()->disambiguatePDF($path);

			require_once('plugins/generic/entityFishing/classes/indexing/SolrIndex.php');
			(new SolrIndex())->indexNerdResponse($request->getContext(), $submission, $submissionFile, $result);

			import('lib.pkp.classes.core.JSONMessage');
			$json = new JSONMessage(true, '');
			return $json;
		}
	}
}

?>
