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
 *  @version     0.0.1
 */
/*******************************************************************************

 *  Marketplace Web Service PHP5 Library
 *  Generated: Thu May 07 13:07:36 PDT 2009
 *  Modified 2019-05-22 (YYYY-MM-DD)
 */

// log command line arguments
// used for testing v0.0.1 invocation
echo 'argc:', PHP_EOL;
var_dump($argc); // the number of arguments passed
echo 'argv:', PHP_EOL;
var_dump($argv); // the arguments passed

echo json_encode($argv[1]);

$sleepSeconds = 2;
echo PHP_EOL . 'Now sleeping for ' . $sleepSeconds . ' seconds.';
sleep($sleepSeconds);

$response = 'END:-_]%£j+: Processing successfully completed. Exit code: 1. Filename: /ksf/djkfs/jkdfjkas.csv';
echo PHP_EOL, $response;
// end test


include_once ('.config.inc.php');

$parameters = json_decode(utf8_encode($argv[1]));

function echo2($text) {
  GLOBAL $parameters;
  if ($parameters->dev->debug) {
    echo $text;
  }
}

echo2 PHP_EOL . PHP_EOL . "echo 2 here";

/************************************************************************
* Uncomment to configure the client instance. Configuration settings
* are:
*
* - MWS endpoint URL
* - Proxy host and port.
* - MaxErrorRetry.
***********************************************************************/
// IMPORTANT: Uncomment the approiate line for the country you wish to
// sell in:
// United States:
//$serviceUrl = "https://mws.amazonservices.com";
// United Kingdom
//$serviceUrl = "https://mws.amazonservices.co.uk";
// Germany
//$serviceUrl = "https://mws.amazonservices.de";
// France
//$serviceUrl = "https://mws.amazonservices.fr";
// Italy
//$serviceUrl = "https://mws.amazonservices.it";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn";
// Canada
//$serviceUrl = "https://mws.amazonservices.ca";
// India
//MwsAccessKeyId = "https://mws.amazonservices.in";

$config = array (
  'ServiceURL' => $parameters->target->MwsServiceURL,       // MwsAccessKeyId
  'ProxyHost' => $parameters->connection->ProxyHost,        // null
  'ProxyPort' => $parameters->connection->ProxyPort,        // -1
  'MaxErrorRetry' => $parameters->connection->MaxErrorRetry // 3
);

/************************************************************************
 * Instantiate Implementation of MarketplaceWebService
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
 $service = new MarketplaceWebService_Client(
     $parameters->merchant->MwsAccessKeyId,     // AWS_ACCESS_KEY_ID
     $parameters->merchant->MwsSecretAccessKey, // AWS_SECRET_ACCESS_KEY
     $config,
     $parameters->developer->ApplicationName,   // APPLICATION_NAME,
     $parameters->developer->ApplicationVersion // APPLICATION_VERSION
   );

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebService
 * responses without calling MarketplaceWebService service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebService/Mock tree
 *
 ***********************************************************************/
 // $service = new MarketplaceWebService_Mock();

if ($parameters->dev->mock) {
  $service = new MarketplaceWebService_Mock();
}

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for Report Action
 ***********************************************************************/
// Constructing the MarketplaceId array which will be passed in as the the MarketplaceIdList
// parameter to the RequestReportRequest object.
//$marketplaceIdArray = array("Id" => array('<Marketplace_Id_1>','<Marketplace_Id_2>'));
$marketplaceIdArray = array("Id" => array($parameters->target->MwsMarketplaceId));

 // @TODO: set request. Action can be passed as MarketplaceWebService_Model_ReportRequest
 // object or array of parameters

$requestParameters = array (
  'Merchant' => $parameters->merchant->AwsMerchantId, // MERCHANT_ID
  'MarketplaceIdList' => $marketplaceIdArray,
  'ReportType' => $parameters->query->MwsReportType, // '_GET_MERCHANT_LISTINGS_DATA_'
  'ReportOptions' => $parameters->query->ReportOptions // 'ShowSalesChannel=true',
  // 'MWSAuthToken' => '<MWS Auth Token>', // Optional
);

$request = new MarketplaceWebService_Model_RequestReportRequest($requestParameters);

if ($parameters->connection->MwsAuthToken) {
  $request->setMWSAuthToken($parameters->connection->MwsAuthToken); // Optional
}

// $request = new MarketplaceWebService_Model_RequestReportRequest();
// $request->setMarketplaceIdList($marketplaceIdArray);
// $request->setMerchant(MERCHANT_ID);
// $request->setReportType('_GET_MERCHANT_LISTINGS_DATA_');
// $request->setMWSAuthToken('<MWS Auth Token>'); // Optional

// Using ReportOptions
// $request->setReportOptions('ShowSalesChannel=true');

 invokeRequestReport($service, $request);

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

                echo2 ("Service Response\n");
                echo2 ("=============================================================================\n");

                echo2("        RequestReportResponse\n");
                if ($response->isSetRequestReportResult()) {
                    echo2("            RequestReportResult\n");
                    $requestReportResult = $response->getRequestReportResult();

                    if ($requestReportResult->isSetReportRequestInfo()) {

                        $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                          echo2("                ReportRequestInfo\n");
                          if ($reportRequestInfo->isSetReportRequestId())
                          {
                              echo2("                    ReportRequestId\n");
                              echo2("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                          }
                          if ($reportRequestInfo->isSetReportType())
                          {
                              echo2("                    ReportType\n");
                              echo2("                        " . $reportRequestInfo->getReportType() . "\n");
                          }
                          if ($reportRequestInfo->isSetStartDate())
                          {
                              echo2("                    StartDate\n");
                              echo2("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetEndDate())
                          {
                              echo2("                    EndDate\n");
                              echo2("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetSubmittedDate())
                          {
                              echo2("                    SubmittedDate\n");
                              echo2("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetReportProcessingStatus())
                          {
                              echo2("                    ReportProcessingStatus\n");
                              echo2("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                          }
                      }
                }
                if ($response->isSetResponseMetadata()) {
                    echo2("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                        echo2("                RequestId\n");
                        echo2("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }

                echo2("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

?>
