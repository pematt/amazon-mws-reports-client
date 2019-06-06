<?php
/**
 * NOTICE
 *
 * Marketplace Web Service PHP Library
 * Copyright 2009 Amazon Technologies, Inc
 *
 * This product includes software developed by
 * Amazon Technologies, Inc (http://www.amazon.com/).
*/
/**
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebService
 *  @copyright   Copyright 2009 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     0.0.7
 */
/*******************************************************************************

 *  Marketplace Web Service PHP5 Library
 *  Generated: Thu May 07 13:07:36 PDT 2009
 *  Modified 2019-05-22 (YYYY-MM-DD)
 */

include_once ('.config.inc.php');

// echo('argc: ' . $argc);
// echo('argv: ' . serialize($argv));

// check for the existence of parameters
if ($argc < 2) {
  echo ERROR_PREFIX . "E1101: No parameters received. Aborting.";
  exit(1101);
}

// we expect the parameters to be the first command line argument
// all other command line arguments are ignored
if ($argc > 2) {
  echo WARNING_PREFIX . "W1002: More than one parameter received, only the first parameter is used.";
}

// create php object from command line json parameters
$parameters = json_decode(utf8_encode($argv[1]));

if (!$parameters) {
  echo ERROR_PREFIX . "E1010: The parameters could not be parsed. Aborting.";
  exit(1010);
}

logDebug("parameters: " . serialize($parameters));

// todo: check for existence of all mandatory parameters

// perhaps add some sleep to simulate longer execution time
if ($parameters->dev && $parameters->dev->sleep) {
  if (is_int($parameters->dev->sleep)) {
    logDebug('Now sleeping for ' . $parameters->dev->sleep . ' seconds.');
    sleep($parameters->dev->sleep);
  } else {
    echo WARNING_PREFIX . "1110: parameters.dev.sleep contains a non integer value. Not sleeping.";
  }
}

// set up the mws service call parameters
$service = null;
if ($parameters->dev && $parameters->dev->mock) {

  logDebug("we will not contact the mws service");

  $service = new MarketplaceWebService_Mock();

} else {

  logDebug("we are contacting the mws service");

  $config = array (
    'ServiceURL'    => $parameters->api->network->mwsServiceURL,
    'ProxyHost'     => $parameters->api->network->proxyHost,
    'ProxyPort'     => $parameters->api->network->proxyPort,
    'MaxErrorRetry' => $parameters->api->network->maxErrorRetry
  );

  $service = new MarketplaceWebService_Client(
    $parameters->api->developer->mwsAccessKeyId,
    $parameters->api->developer->mwsSecretAccessKey,
    $config,
    $parameters->api->developer->applicationName,
    $parameters->api->developer->applicationVersion
  );
}

// Constructing the MarketplaceId array which will be passed in as the the MarketplaceIdList
// parameter to the RequestReportRequest object.
//$marketplaceIdArray = array("Id" => array('<Marketplace_Id_1>','<Marketplace_Id_2>'));
$marketplaceIdArray = array("Id" => $parameters->api->query->parameters->mwsMarketplaceIds);

$requestParameters = array (
  'Merchant'          => $parameters->api->merchant->mwsMerchantId,
  'MarketplaceIdList' => $marketplaceIdArray,
  'ReportType'        => $parameters->api->query->operation,
  'ReportOptions'     => $parameters->api->query->parameters->reportOptions,
  'MWSAuthToken'      => $parameters->api->merchant->mwsAuthToken,
);

$request = new MarketplaceWebService_Model_RequestReportRequest($requestParameters);

// alternative
// $request = new MarketplaceWebService_Model_RequestReportRequest();
// $request->setMarketplaceIdList($marketplaceIdArray);
// $request->setMerchant(MERCHANT_ID);
// $request->setReportType('_GET_MERCHANT_LISTINGS_DATA_');
// $request->setMWSAuthToken('<MWS Auth Token>');
// $request->setReportOptions('ShowSalesChannel=true');

if (isDebug()) {
  logDebug('Invoking Request Report...');
  var_dump($service);
  var_dump($request);
}

$mwsResponse = invokeRequestReport($service, $request);
$service = null; // unset
$request = null; // unset

logDebug('MWS Response: ' . $mwsResponse);

// todo: check response code

$response = null;
if ($parameters->result->destination = 'file') {
  // todo: save $mwsRresponse data part to file
  $response = $parameters->result->file->name;
}
if ($parameters->result->destination = 'stdout') {
  if ($parameters->result->stdout->format = 'json') {
    // todo: assign only data part from $mwsResponse to $response
    $response = json_encode(utf8_encode($mwsResponse));
  }
}
$mwsResponse = null; // unset

$exitPrefix = '';
if ($parameters->result && $parameters->result->responsePrefix) {
  $exitPrefix = $parameters->result->responsePrefix;
  $response = json_encode(utf8_encode($exitPrefix . $response));
}
$parameters = null;
echo $response;
exit(0);

/////////////////////////////////////////////////////////
// FUNCTION DEFINITIONS

function isDebug () {
  return true;
  // todo
  GLOBAL $parameters;
  if ($parameters->dev && $parameters->dev->debug) {
    return $parameters->dev->debug;
  }
  return false;
}

function getDebugLinePrefix () {
  GLOBAL $parameters;
  if ($parameters->dev && $parameters->dev->debugLinePrefix) {
    return $parameters->dev->debugLinePrefix;
  }
  return DEBUG_PREFIX;
}

function logDebug ($text) {
  if (isDebug()) {
    echo getDebugLinePrefix() . $text . PHP_EOL;
  }
}

/**
  * Get Report List Action Sample
  * returns a list of reports; by default the most recent ten reports,
  * regardless of their acknowledgement status
  *
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_GetReportList or array of parameters
  */
  function invokeRequestReport(MarketplaceWebService_Interface $service, $request)
  {
      try {
                $response = $service->requestReport($request);

                logDebug ("Service Response\n");
                logDebug ("=============================================================================\n");

                logDebug("        RequestReportResponse\n");
                if ($response->isSetRequestReportResult()) {
                    logDebug("            RequestReportResult\n");
                    $requestReportResult = $response->getRequestReportResult();

                    if ($requestReportResult->isSetReportRequestInfo()) {

                        $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                          logDebug("                ReportRequestInfo\n");
                          if ($reportRequestInfo->isSetReportRequestId())
                          {
                              logDebug("                    ReportRequestId\n");
                              logDebug("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                          }
                          if ($reportRequestInfo->isSetReportType())
                          {
                              logDebug("                    ReportType\n");
                              logDebug("                        " . $reportRequestInfo->getReportType() . "\n");
                          }
                          if ($reportRequestInfo->isSetStartDate())
                          {
                              logDebug("                    StartDate\n");
                              logDebug("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetEndDate())
                          {
                              logDebug("                    EndDate\n");
                              logDebug("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetSubmittedDate())
                          {
                              logDebug("                    SubmittedDate\n");
                              logDebug("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetReportProcessingStatus())
                          {
                              logDebug("                    ReportProcessingStatus\n");
                              logDebug("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                          }
                      }
                }
                if ($response->isSetResponseMetadata()) {
                    logDebug("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                        logDebug("                RequestId\n");
                        logDebug("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }

                logDebug("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

                return $response;

     } catch (MarketplaceWebService_Exception $ex) {
         echo(ERROR_PREFIX . "Caught Exception: " . $ex->getMessage() . "\n");
         echo(ERROR_PREFIX . "Response Status Code: " . $ex->getStatusCode() . "\n");
         echo(ERROR_PREFIX . "Error Code: " . $ex->getErrorCode() . "\n");
         echo(ERROR_PREFIX . "Error Type: " . $ex->getErrorType() . "\n");
         echo(ERROR_PREFIX . "Request ID: " . $ex->getRequestId() . "\n");
         echo(ERROR_PREFIX . "XML: " . $ex->getXML() . "\n");
         echo(ERROR_PREFIX . "ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

?>
