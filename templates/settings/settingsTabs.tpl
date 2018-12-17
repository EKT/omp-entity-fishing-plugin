{**
 * @file plugins/generic/entitiFishing/templates/settings/settingsTabs.tpl
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
 * The setting tabs for the entityFishing plugin.
 *}

<script type="text/javascript">
	// Attach the JS file tab handler.
	$(function() {ldelim}
		$('#entityFishingSettingsTabs').pkpHandler(
				'$.pkp.controllers.TabHandler');
	{rdelim});
</script>
<div id="entityFishingSettingsTabs" class="pkp_controllers_tab">
	<ul>
		<li><a href="{url router=$smarty.const.ROUTE_COMPONENT component="grid.settings.plugins.SettingsPluginGridHandler" op="manage" category="generic" plugin=$pluginName verb="showTab" tab="settings" escape=false}">{translate key="plugins.generic.entityFishing.settings.tab.title"}</a></li>
	</ul>
</div>

