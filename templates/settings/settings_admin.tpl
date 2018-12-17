{**
 * @file plugins/generic/entitiFishing/templates/settings/settings_admin.tpl
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
 * The basic setting tab for the entityFishing plugin.
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#entitiFishingPluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="entitiFishingPluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="showTab" tab="basic" save="true"}">
	{csrf}
	<input type="hidden" name="tab" value="settings" />

	{fbvFormArea id="entitiFishingNerd" title="plugins.generic.entityFishing.settings.nerd" class="border"}
		{fbvFormSection for="entityFishingNerd"}
			{fbvElement type="text" label="plugins.generic.entityFishing.settings.form.nerdurl" id="entityFishingNerdUrl" value=$entityFishingNerdUrl size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

	{/fbvFormArea}

	{fbvFormArea id="solr" title="plugins.generic.entityFishing.settings.solr" class="border"}

		{fbvFormSection for="solr"}
			{fbvElement type="text" label="plugins.generic.entityFishing.settings.form.solrurl" id="solrUrl" value=$solrUrl size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.generic.entityFishing.settings.form.solrport" id="solrPort" value=$solrPort size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.generic.entityFishing.settings.form.solrpath" id="solrPath" value=$solrPath size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

	{/fbvFormArea}

	{fbvFormButtons submitText="common.save"}

</form>
