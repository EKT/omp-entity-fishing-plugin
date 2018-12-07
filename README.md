# entityFishing
OMP-PKP plugin that provides entity fishing support via the nerd API (https://github.com/kermitt2/entity-fishing).

This is a deliverable of the <a target="_blank" href="http://hirmeos.eu">**HIRMEOS**</a> project. The project has received funding from the European Union’s Horizon 2020 research and innovation programme under grant agreement No 731102.

### Features:

1) **Enable/Disable per admin or site level**
	
	Admin can disable the plugin for all the sites even if a site has the plugin enabled.

2) **Settings per admin or site level**
	
	There is an admin form with generic settings

	* NERD API url
	* SOLR hostname and core name

	There is also a site level form with specific settings

	* Enable/Disable tag cloud appearance at site level
	* Enable/Disable tag cloud appearance at monograph level
	* Enable/Disable entity recognition within the abstract of the monograph
	
3) **Admin / Press manager is able to index the files of monographs**

	In the Press Manager admin dashboard, under a submission's "production" tab, the list of files is presented to the user and he can select the ones to index (find entities using the NERD API and index them in SOLR)
	
3) **Tag cloud**
	
	A tag cloud is presented in the front end of the site in order for the users to find easily what subjects the specific press deals with. Clicking an entity in the tag cloud the user is presented with the monographs that include this entity
	
4) **Abstract with entities**

	The abstract of the monograph is coloured based on the entities it includes so as someone finds easily the subjects of the specific monograph
	
5) **Tag cloud within a monograph**

	Tag cloud support in the landing page of a monograph that helps user identify easily the subjects the specific monograph deals with.
	
##Installation Guide

###Prerequisites

1) PHP Multibyte String: http://php.net/manual/en/book.mbstring.php
2) PHP Apache Solr: http://php.net/manual/en/book.solr.php
3) Apache Solr: http://lucene.apache.org/solr/
4) [**Optional**] Nerd: https://github.com/kermitt2/entity-fishing (You can install your own NERD instance or you can use the public one here: http://nerd.huma-num.fr/nerd/)

###Installation

1) Download the plugin files from this repository

2) Place them inside your OMP installation in the followin path:

	`{base_omp_installation_dir}/plugins/generic`
	
3) Move to this path and run the following command:

	`./composer.phar install`
	
	The plugin uses the following third party libraries that the aforementioned command is going to install:
	
	1) lotsofcode/tag-cloud: https://packagist.org/packages/lotsofcode/tag-cloud
	2) hirmeos/entity-fishing-php-wrapper: https://packagist.org/packages/hirmeos/entity-fishing-php-wrapper
	
**IMPORTANT**: For the time, there is also a small hack that you need to make in order for the plugin to function well. You have to go to the file in the path:

`{base_omp_installation_dir}/templates/controllers/tab/workflow/production.tpl`

and add the following line before the last two `div`'s:

`{call_hook name="Templates::Workflow::production"}`

After these steps, you will be able to use the plugin!

## Compatibility
OMP 3.1 and newer

## License
Distributed under CC-BY-NC-SA. For full terms see: http://creativecommons.org/licenses/by-nc-sa/4.0/.

