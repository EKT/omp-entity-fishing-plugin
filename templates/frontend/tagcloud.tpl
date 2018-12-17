{**
 * @file plugins/generic/entitiFishing/templates/frontend/tagcloud.tpl
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
 * @brief Display tag cloud with entities.
 *
 * @uses $tagcloud_html string The tagcloud html with specified class names in html elements
 *}
{include file="frontend/components/header.tpl" pageTitle="navigation.catalog"}

{* Breadcrumb *}
{include file="frontend/components/breadcrumbs.tpl" type="tagcloud" currentTitleKey="plugins.generic.entityFishing.breadcrumb.tagcloud"}

{if $problem}
	<div class="search_results" style="color:red">{translate key=$problem}</div>
{else}
	<div class="nerd-tag-cloud">
		{$tagcloud_html}
	</div>
{/if}

{include file="frontend/components/footer.tpl"}
