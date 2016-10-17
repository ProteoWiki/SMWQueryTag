<?php

class SMWQueryTagApi extends ApiBase {

	/**
	 * SMWQueryTagApi is a class that allows retreiving info
	 */

	public function execute() {

		$params = $this->extractRequestParams();

 		$output = null;

		// Enable further protection here for security reasons
 		
		switch ( $params['method'])  {
			case "property": # Process property
				$output = SMWQueryTag::returnProperty( $params['title'], $params['listprops'], $params['separator'], $params['template'] );
			break;
			case "list": # Process list
				$output = SMWQueryTag::returnList( $params['title'], $params['listprops'], $params['separator'], $params['mainlabel'], $params['limit'] );
			break;
		}

		$this->getResult()->addValue( null, $this->getModuleName(), array ( 'status' => "OK", 'content' => $output ) );

		return true;
	}

	public function getAllowedParams() {
		return array(
			'method' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'title' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'listprops' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'separator' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'template' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'mainlabel' => array(
				ApiBase::PARAM_TYPE => 'boolean',
				ApiBase::PARAM_REQUIRED => false
			),
			'limit' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false
			)
		);
	}

	public function getDescription() {
		return array(
			'API for retrieving some properties information'
		);
	}



	public function getParamDescription() {
		return array(
			'method' => 'Method used for retrieving ',
			'title' => 'Title of the page to consider',
			'listprops' => 'List of properties to retrieve',
			'separator' => 'Output separator of the properties',
			'template' => 'Template to process information',
			'mainlabel' => 'Whether show mainlabel or not',
			'limit' => 'Maximum number of elements'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': 1.1';
	}

}