const util = require('util');
const exec = util.promisify(require('child_process').exec); // https://nodejs.org/api/child_process.html#child_process_child_process_exec_command_options_callback
const path = require('path');

const PACKET_NAME = 'amazon-mws-reports-client';
const PACKET_NAME_HR = 'Amazon MWS Reports Client Exception';
const ERR_TYPE_QUOTE = 0;
const ERR_TYPE_MISSING = 1;

// https://mws.amazonservices.com/scratchpad/index.html

const parametersDefault = {
  api: { //* connection
    //countryCode: null, // mandatory [AE,AU,BR,CA,CN,DE,ES,FR,GB,IN,IT,JP,MX,NL,TR,US]
    mwsSection: null, // Mandatory [EasyShip,Feeds,Finances,FulfillmentInboundShipment,FulfillmentInventory,FulfillmentOutboundShipping,MerchantFulfillment,OffAmazonPayments,Orders,Products,Recommendations,Reports,Sellers,Subscriptions]
    developer: {
      applicationName: null, // mandatory
      applicationVersion: null, // mandatory
      mwsAccessKeyId: null, // mandatory
      mwsSecretAccessKey: null, // mandatory
    },
    merchant: {
      mwsMerchantId: null, // mandatory
      mwsAuthToken: null, // mandatory
    },
    query: {
      operation: null, // mandatory
      parameters: {
        mwsMarketplaceIds: ['ID1','ID2','ID3'], // array of marketplace ID's
        reportOptions: null,
      },
    },
    network: {
      mwsServiceURL: null, // mandatory
      maxErrorRetry: 3,   // mandatory
      proxyHost: null,   // mandatory, null if no proxy
      proxyPort: -1,    // mandatory, -1 if no proxy
    },
  },
  result: { // where to store the data fetched from mws
    destination: 'stdout', // file or stdout, optional
    stdout: { // optional, used only if destination = 'stdout'
      dataLinePrefix: '[Data Line]:\t', // prefix for all lines of data downloaded from mws
      format: 'json',
    },
    file: { // manadory if destination = 'file', otherwise ignored
      path: '$MWS_EXTRACT_DIR', // directory where to store the file
      name: 'file.xml', // filename. appended to path
      compression: 'gzip', // if destination 'file'; 'gzip' = gzip the file, https://nodejs.org/api/zlib.html
      namePostfix: '.gzip', // any filename postfix; '.gzip' = file.xml becomes file.xml.gzip
    },
    responsePrefix: null, // optional, prefix for the line containing the exit code indicating success or failure
  },
  exec: {  // optional, parameters to the exec command
    encoding: 'utf8',
    env: { // system environment variables available to the php script
      //MWS_EXTRACT_DIR: '.',
    },
    timeout: 30 * 1000, // ms
    maxBuffer: 1024 * 1024, // bytes
  },
  system: { // optional
    phpCommand: 'php',
  },
  dev: { // optional
    debug: true, // print debug messages
    debugLinePrefix: '[Debug Info]:\t', // used only if debug = true
    mock: false, // return a mock response, do not contact the mws service
    sleep: 1 * 1000, // ms
  }
}

mwsApiOperationParametersDefault = {
  requestReport: {
    required: {
      reportType: null,
    },
    optional: {
      mwsMarketplaceIdList: null, // mandatory * target.MwsMarketplaceId
      startTimestamp: null, // mandatory * ReportStart
      endTimestamp: null, // mandatory * ReportEnd
      reportOptions: null, // further report options, optional, eg 'ShowSalesChannel=true'
    }
  },

}


function ClientException(message, type = ERR_TYPE_QUOTE) {
  this.packet = PACKET_NAME_HR;
  this.file = path.basename(__filename);
  this.npmPacket = PACKET_NAME;

  switch (type) {
    case ERR_TYPE_MISSING:
      this.message = 'The input parameters object is missing the mandatory property "' + message + '".';
      break;
    default:
      this.message = message;
   }
}

module.exports = async function (parametersIn) {

  if (!(parametersIn)) {
    throw new ClientException('No parameters received.');
  }
  if (!(parametersIn.api.merchant)) {
    throw new ClientException('merchant', ERR_TYPE_MISSING);
  }
  // TODO: check all mandatory properties here

  // parametersIn's properties overwrites parametersDefault's properties with the same name
  const parameters = { ...parametersDefault, ...parametersIn }

  const exec = util.promisify(require('child_process').exec);
  const phpScript = path.dirname(__filename) + "/php/RequestReport.php";
  const debug = parameters.dev && parameters.dev.debug;

  const command = parameters.system.phpCommand + " " + phpScript + " '" + JSON.stringify(parameters) + "'"
  // + " | grep 'END:-_]%£j+:'"
  // + " | tail -n 1" // return only the last line
  ;

  if (debug) {
    console.log(PACKET_NAME, ': phpScript:', phpScript);
    console.log(PACKET_NAME, ': parameters:', parameters);
    console.log(PACKET_NAME, ': command:', command);
    console.log(PACKET_NAME, ': path.dirname:', path.dirname(__filename));
  }

  try {
    const { stdout, stderr } = await exec(command, parameters.exec);
    if (stderr) {
      console.error(PACKET_NAME, `: error: ${stderr}`);
    }
    if (debug) {
      console.log(PACKET_NAME, `: Response: ${stdout}`);
    }
    return stdout;
  }
  catch (err) {
    console.error(PACKET_NAME, err);
  }

  return null;
}
