{**
 * @file plugins/generic/entitiFishing/templates/frontend/sidebar_block.tpl
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
 * The sidebar block in the public frontend regarding the entity fishing plugin.
 *}

<div class="pkp_block block_browse_entities">
	<span class="title">
		{translate key="plugins.generic.entityFishing.sidebar.title"}
	</span>

	<nav class="content" role="navigation" aria-label="Browse">
		<ul>
			<li>
				<a href="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.entityFishing.controllers.browse.tagcloudHandler" op="entities"}">
					{translate key="plugins.generic.entityFishing.sidebar.tagcloud"}
				</a>
			</li>
		</ul>
	</nav>
</div>
