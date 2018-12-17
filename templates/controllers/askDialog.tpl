{**
 * @file plugins/generic/entitiFishing/templates/controllers/askDialog.tpl
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
 * The included template that is hooked into the front end display.
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#askEntityIndex').pkpHandler(
				'$.pkp.controllers.form.AskDialogAjaxFormHandler',
				{ldelim}
					trackFormChanges: true
					{rdelim}
		);
		{rdelim});
</script>


	<form class="pkp_form" id="askEntityIndex" method="post" action="{url component="plugins.generic.entityFishing.controllers.grid.EntityFishingGridHandler" op="setIndexed" submissionId=$submission->getId() fileId=$submissionFile->getFileId() revision=$submissionFile->getRevision()}">
		{csrf}
		{fbvFormArea id="confirmationText"}
			{if $indexed}
				{translate key="plugins.generic.entityFishing.publicationformats.dialog.indexed"}
			{else}
				{translate key="plugins.generic.entityFishing.publicationformats.dialog.nonindexed"}
			{/if}
		{/fbvFormArea}

		{fbvFormButtons id="assignPublicIdentifierForm" submitText="common.ok"}
	</form>
