{**
 * @file plugins/generic/entitiFishing/templates/frontend/abstract.tpl
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
 * The template to display results when an entity is clickes in the tag cloud.
 *}

{literal}
	<script>
		document.addEventListener("DOMContentLoaded", function(event) {
			$('.abstract .value').html("{/literal}{$abstract}{literal}");
		});
	</script>
{/literal}

{literal}
<script>
	document.addEventListener("DOMContentLoaded", function(event) {
		{/literal}
		{foreach from=$nerd_entities item=entity}
		{literal}
		$( ".nerd_{/literal}{$entity->offset_start}{literal}" ).click(function() {
			$('#myModal_{/literal}{$entity->offset_start}{literal}').modal('show');
		});

		$( ".myModal_title_{/literal}{$entity->offset_start}{literal}" ).html(
				$( ".nerd_{/literal}{$entity->offset_start}{literal}" ).text()
		);
		{/literal}
		{/foreach}
		{literal}
	});
</script>
{/literal}

{foreach from=$nerd_entities item=entity}
	<!-- Modal -->
	<div id="myModal_{$entity->offset_start}" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title myModal_title_{$entity->offset_start}">{$entity->raw_name}</h4>
				</div>
				<div class="modal-body">
					<p>
						{if isset($entity->type) }
							Type: <span>{$entity->type}</span><br/>
						{/if}
						Normalized: <span>{$entity->raw_name}</span><br/>
						Domains: <span>{$entity->getDomainsText(', ')}</span><br/>
						Conf: <span>{$entity->nerd_score}</span><br/>
						Wikidata id: <span><a target="_blank" href="https://www.wikidata.org/wiki/{$entity->wikidata_id}">{$entity->wikidata_id}</a></span><br/>
						Wikipedia id: <span><a target="_blank" href="https://en.wikipedia.org/wiki?curid={$entity->wikipedia_ext_ref}">{$entity->wikipedia_ext_ref}</a></span><br/>
						{if null != $entity->getDefinition() }
							<br/>
							<b>Definition:</b><br/>
							{$entity->getDefinition()}
						{/if}
					</p>

					<p>
						<b>Multilingual:</b>
						{assign var="nerd_multilingual" value=$entity->concept_response->multilingual}
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
						{assign var="nerd_statements" value=$entity->concept_response->statements}
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
{/foreach}
