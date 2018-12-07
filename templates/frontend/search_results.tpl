{**
 * @file plugins/generic/entitiFishing/templates/frontend/search_results.tpl
 *
 * Copyright (c) 2018 Hirmeos EU Project (http://hirmeos.eu/)
 * Copyright (c) 2018 National Documentation Center (http://www.ekt.gr/)
 * Copyright (c) 2018 Kostas Stamatis
 *
 * This is a deliverable of the HIRMEOS project. The project has received funding from the European Union’s
 * Horizon 2020 research and innovation programme under grant agreement No 731102.
 *
 * Distributed under CC-BY-NC-SA. For full terms see: http://creativecommons.org/licenses/by-nc-sa/4.0/.


 *}
{include file="frontend/components/header.tpl" pageTitle="common.search"}

<div class="page page_search">

	{* Breadcrumb *}
	{include file="frontend/components/breadcrumbs.tpl" type="tagcloud" currentTitleKey="plugins.generic.entityFishing.breadcrumb.tagcloud.browseby"} {$entity}

	{if $problem}
		<div class="search_results" style="color:red">{translate key=$problem}</div>
	{else}

		<div class="monograph_count">
			{translate key="catalog.browseTitles" numTitles=$publishedMonographs|@count}
		</div>

		{* No query - this may happen because of a screen reader, so don't show an
		   error, just leave them with the search form *}
		{if $entity == '' }

			{* No published titles *}
		{elseif !$publishedMonographs|@count}
			<div class="search_results">
				{translate key="catalog.noTitlesSearch" searchQuery=$entity|escape}
			</div>

			{* Monograph List *}
		{else}
			<div class="search_results">
				{if $publishedMonographs|@count > 1}
					{translate key="plugins.generic.entityFishing.tagcloud.foundTitlesSearch" searchQuery=$entity|escape number=$publishedMonographs|@count}
				{else}
					{translate key="plugins.generic.entityFishing.tagcloud.foundTitleSearch" searchQuery=$entity|escape}
				{/if}

				<div>

					{literal}
						<script>
							document.addEventListener("DOMContentLoaded", function(event) {
								$( ".entity" ).click(function() {
									$('#myModal').modal('show');
								});
								$( ".myModal" ).html(
										$( ".entity" ).text()
								);
							});
						</script>
					{/literal}

					<!-- Modal -->
					<div id="myModal" class="modal fade" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title myModal_title</h4>
								</div>
								<div class="modal-body">
									<p>
										{if isset($entityObj->type) }
											Type: <span>{$entityObj->type}</span><br/>
										{/if}
										Normalized: <span>{$entityObj->raw_name}</span><br/>
										Domains: <span>{$entityObj->getDomainsText(', ')}</span><br/>
										Conf: <span>{$entityObj->nerd_score}</span><br/>
										Wikidata id: <span><a target="_blank" href="https://www.wikidata.org/wiki/{$entityObj->wikidata_id}">{$entityObj->wikidata_id}</a></span><br/>
										Wikipedia id: <span><a target="_blank" href="https://en.wikipedia.org/wiki?curid={$entityObj->wikipedia_ext_ref}">{$entityObj->wikipedia_ext_ref}</a></span><br/>
											<br/>
											<b>Definition:</b><br/>
											{$conceptResponse->definitions[0]->definition}

									</p>
									<p>
										<b>Multilingual:</b>
										{assign var="nerd_multilingual" value=$conceptResponse->multilingual}
									<table>
										{foreach from=$nerd_multilingual item=multilignual}
											<tr>
												<td style="padding-left:5px; background-color: #dedede">{$multilignual->lang}</td>
												<td style="padding-left:10px">{$multilignual->term}</td>
											</tr>
										{/foreach}
									</table>
									</p>
									<p>
										<b>Statements:</b>
										{assign var="nerd_statements" value=$conceptResponse->statements}
									<table>
										{foreach from=$nerd_statements item=statement}
											<tr>
												<td style="padding-left:5px; background-color: #dedede">{$statement->property_name}</td>
												<td style="padding-left:10px">{$statement->value}</td>
											</tr>
										{/foreach}
									</table>
									</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
			{include file="frontend/components/monographList.tpl" monographs=$publishedMonographs}
		{/if}
	{/if}
</div><!-- .page -->

{include file="frontend/components/footer.tpl"}
