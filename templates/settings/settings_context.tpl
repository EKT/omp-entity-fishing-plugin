{**
 * @file plugins/generic/entitiFishing/templates/settings/settings_context.tpl
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
 * The basic setting tab for the entityFishing plugin for pspecific context.
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

	{fbvFormArea id="entitiFishingNerd" title="plugins.generic.entityFishing.settings.context.title" class="border"}
	{fbvFormSection for="entityFishingNerd" list="true"}
		<br/>
	{fbvElement type="checkbox" id="efTagCloudEnabled" label="plugins.generic.entityFishing.settings.context.tagcloud.enabled" maxlength="40" checked=$efTagCloudEnabled|compare:true}
		<br>
	{fbvElement type="checkbox" id="efAbstractEnabled" label="plugins.generic.entityFishing.settings.context.abstract.enabled" maxlength="40" checked=$efAbstractEnabled|compare:true}
		<br/>
	{fbvElement type="checkbox" id="efLandingEnabled" label="plugins.generic.entityFishing.settings.context.landingpage.enabled" maxlength="40" checked=$efLandingEnabled|compare:true}

	{/fbvFormSection}

	{/fbvFormArea}

	{fbvFormButtons submitText="common.save"}
</form>
