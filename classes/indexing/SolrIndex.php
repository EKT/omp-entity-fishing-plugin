<?php
/**
 * @file plugins/generic/entityFishing/classes/indexing/SolrIndex.php
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
 * @class SolrIndex
 *
 * @brief Handle SOLR requests.
 */

final class SolrIndex
{
	const EF_TYPE_MONOGRAPH = 0;
	const EF_TYPE_PUBLICATION_FILE = 1;

	private $hostname=null;
	private $port=null;
	private $path=null;

	//make constructor private so no one create object using new Keyword
	function __construct(){
		$request =& Registry::get('request');
		$context = $request->getContext();
		$plugin = PluginRegistry::getPlugin('generic', 'entityfishingplugin');

		$this->hostname = $plugin->getSetting(null, "solrUrl");
		$this->port = $plugin->getSetting(null, "solrPort");
		$this->path = $plugin->getSetting(null, "solrPath");
	}

    public function indexNerdResponse($context, $submission, $submissionFile, $nerd_response) {
        $options = array
        (
			'hostname' => $this->hostname,
			'path' => $this->path,
			'port'     => $this->port
        );

        $client = new SolrClient($options);
		$doc = new SolrInputDocument();
        $doc->addField('id', $submissionFile->getFileId());

        for ($x = 0; $x <= count($nerd_response->entities); $x++) {
			if ($nerd_response->entities[$x]->nerd_selection_score >= 0.8) {
				$doc->addField('entity_value', $nerd_response->entities[$x]->raw_name);
			}
        }
		$doc->addField('monographID', $submission->getID());
		$doc->addField('context', $context->getPath());
		$doc->addField('json', $nerd_response->jsonstring);

        //$doc->addField('author', $monograph->getAuthorString());

        $updateResponse = $client->addDocument($doc);
        $client->commit();
    }

	public function removeFromIndex($context, $submission, $submissionFile) {
		$options = array
		(
			'hostname' => $this->hostname,
			'path' => $this->path,
			'port'     => $this->port
		);

		$client = new SolrClient($options);

		$query = '';
		$query .= "context:" . $context->getPath();
		$query .= "AND monographID:" . $submissionFile->getData("submissionId");
		$query .= "AND id:" . $submissionFile->getFileId();
		$client->deleteByQuery($query);
		$client->commit();
	}

	public function checkIfSubmissionIsIndexed($context, $submissionFile) {
		$options = array
		(
			'hostname' => $this->hostname,
			'path' => $this->path,
			'port'     => $this->port
		);

		try {
			$client = new SolrClient($options);

			$query = new SolrQuery();

			$query->setStart(0);

			$query->setQuery("*:*");

			$query->addFilterQuery("context:\"" . $context->getPath(). "\"");
			$query->addFilterQuery("monographID:\"" . $submissionFile->getData("submissionId"). "\"");
			$query->addFilterQuery("id:\"" . $submissionFile->getFileId(). "\"");

			$query_response = $client->query($query);

			$response = $query_response->getResponse();

			$noofdocs = $response['response']['numFound'];

			return $noofdocs>0;
		} catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
			return false;
		}
	}

    public function getFacets($context, $submission=null) {
        $options = array
        (
			'hostname' => $this->hostname,
			'path' => $this->path,
			'port'     => $this->port
        );

		try {
			$client = new SolrClient($options);

			$query = new SolrQuery();

			$query->setStart(0);

			if ($submission) {
				$query->setQuery("monographID:".$submission->getID());
			}
			else {
				$query->setFacet(true);
				$query->addFacetField("entity_value");
				$query->setFacetLimit(-1, "entity_value");
				$query->setFacetSort(SolrQuery::FACET_SORT_COUNT, "entity_value");
				$query->setQuery("*:*");
				$query->setRows(0);

				$query->setGroup(true);
				$query->addGroupField("monographID");
				$query->setGroupFacet(true);

				if (isset($context)) {
					$query->addFilterQuery("context:" . $context->getPath());
				}
			}

			$query_response = $client->query($query);
			$response = $query_response->getResponse();

			if ($submission) {
				$result = array();
				foreach ($response['response']['docs'] as $doc) {
					foreach ($doc['entity_value'] as $entity) {
						array_push($result, $entity);
					}
				}
			}
			else {
				$result = $response['facet_counts']['facet_fields']['entity_value'];//->getPropertyNames();
			}


			return $result;

		} catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
			return null;
		}
	}

    public function searchByEntity($context, $entity){
		$options = array
		(
			'hostname' => $this->hostname,
			'path' => $this->path,
			'port'     => $this->port
		);

		try {
			$client = new SolrClient($options);

			$query = new SolrQuery();

			$query->setStart(0);

			$query->setQuery("*:*");
			if (isset($context)) {
				$query->addFilterQuery("context:" . $context->getPath());
			}
			$query->addFilterQuery("entity_value:\"" . $entity. "\"");

			$query_response = $client->query($query);

			$response = $query_response->getResponse();

			$docs = $response['response']['docs'];

			return $docs;
		} catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
			return null;
		}
	}
}
