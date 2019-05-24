const util = require('util');
const exec = util.promisify(require('child_process').exec); // https://nodejs.org/api/child_process.html#child_process_child_process_exec_command_options_callback
const path = require('path');

// TODO: comment out the parametersExample
const parametersExample = {
  connection: {
    MaxErrorRetry: 3, // mandatory
    ProxyHost: null,   // null if no proxy // mandatory
    ProxyPort: -1,     // -1 if no proxy
    MwsAuthToken: null // Optional
  },
  target: {
    MwsServiceURL: 'url', // mandatory
    MwsMarketplaceId: 'aws marketplace id' // mandatory
  },
  developer: {
    ApplicationName: 'application name', // mandatory
    ApplicationVersion: 'application version' // mandatory
  },
  merchant: {
    AwsMerchantId: 'aws merchant id', // mandatory
    MwsAccessKeyId: 'aws access key id', // mandatory
    MwsSecretAccessKey: 'aws secret access key' // mandatory
  },
  query: {
    MwsReportType: '_GET_MERCHANT_LISTINGS_DATA_', // mandatory
    ReportStart: 'timestamp', // mandatory
    ReportEnd: 'timestamp', // mandatory
    ReportOptions: null // further report options, optional, eg 'ShowSalesChannel=true'
  },
  result: { // where to store the data fetched from mws
    destination: 'file', // file or stdout, optional
    file: { // manadory if destination = 'file', otherwise ignored
      path: '$MWS_EXTRACT_DIR', // directory where to store the file
      name: 'file.xml', // filename. appended to path
      compression: null, // if destination 'file'; 'gzip' = gzip the file, https://nodejs.org/api/zlib.html
      namePostfix: null // any filename postfix; '.gzip' = file.xml becomes file.xml.gzip
    }
  },
  exec: {  // optional
    encoding: 'utf8',
    env: {
      KEY1: 'value1',
      KEY2: 'value2'
    },
    timeout: 30 * 1000, // ms
    maxBuffer: 1024 * 1024 // bytes
  },
  system: {
    phpCommand: 'php' // in case there are more than one way to invoke php then one can select one here, optional
  },
  dev: { // optional
    debug: false,
    mock: false
  }
}

function ClientException(message) {
   this.message = message;
   this.name = 'Amazon MWS Reports Client Exception';
   this.file = path.basename(__filename);
   this.npmPacket = 'amazon-mws-reports-client';
}

module.exports = async function (parameters) {

  if (!(parameters)) {
    throw new ClientException('No parameters received');
  }
  if (!(parameters.merchant)) {
    throw new ClientException('Parameters missing mandatory property "merchant"');
  }

  const exec = util.promisify(require('child_process').exec);
  const phpScript = path.dirname(__filename) + "/php/RequestReport.php";
  const loc = 'amazon-mws-reports-client:';
  const debug = parameters.dev && parameters.dev.debug;
  const execDefaults = {
    encoding: 'utf8',
    maxBuffer: 1024 * 2048,
    timeout: 60 * 1000 // ms
  }
  const systemDefaults = {
    phpCommand: 'php'
  }

  // object spread
  // later properties overwrite earlier properties with the same name
  const execOptions = {...execDefaults, ...parameters.exec }
  const systemOptions = {...systemDefaults, ...parameters.system }

  const command = systemOptions.phpCommand + " " + phpScript + " '" + JSON.stringify(parameters) + "'"
  // + " | grep 'END:-_]%£j+:'"
  // + " | tail -n 1" // return only the last line
  ;

  if (debug) {
    console.log(loc, 'phpScript:', phpScript);
    console.log(loc, 'parameters:', parameters);
    console.log(loc, 'execOptions:', execOptions);
    console.log(loc, 'systemOptions:', systemOptions);
    console.log(loc, 'command:', command);
    console.log(loc, 'path.dirname:', path.dirname(__filename));
  }

  try {
    const { stdout, stderr } = await exec(command, execOptions);
    if (stderr) {
      console.error(loc, `error: ${stderr}`);
    }
    if (debug) {
      console.log(loc, `Response: ${stdout}`);
    }
    return stdout;
  }
  catch (err) {
    console.error(loc, err);
  }

  return null;
}
