<?php

/**
 * @defgroup plugins_generic_entityFishing EntityFishing plugin
 */

/**
 * @file plugins/generic/entityFishing/index.php
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
 * @ingroup plugins_generic_entityFishing
 * @brief Wrapper for the addThis social media plugin.
 *
 */


require_once('EntityFishingPlugin.inc.php');

return new EntityFishingPlugin();

?>
