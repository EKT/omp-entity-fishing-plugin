{**
 * @file plugins/generic/entitiFishing/templates/controllers/publicationFormats.tpl
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
 * The admin grid for the entityFishing plugin that allows the press manager to index/unindex files with entities.
 *}

{url|assign:representationsGridUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.entityFishing.controllers.grid.EntityFishingGridHandler" op="fetchGrid" submissionId=$submission->getId() escape=false}
{load_url_in_div id="formatsGridContainer"|uniqid url=$representationsGridUrl}
