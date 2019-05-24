const util = require('util');
const exec = util.promisify(require('child_process').exec); // https://nodejs.org/api/child_process.html#child_process_child_process_exec_command_options_callback
const path = require('path');

const parametersDefaults = {
  connection: {
    MaxErrorRetry: 3, // mandatory
    ProxyHost: null,   // null if no proxy // mandatory
    ProxyPort: -1,     // -1 if no proxy
    MwsAuthToken: null // Optional
  },
  target: {
    MwsServiceURL: null, // mandatory
    MwsMarketplaceId: null // mandatory
  },
  developer: {
    ApplicationName: null, // mandatory
    ApplicationVersion: null // mandatory
  },
  merchant: {
    AwsMerchantId: null, // mandatory
    MwsAccessKeyId: null, // mandatory
    MwsSecretAccessKey: null // mandatory
  },
  query: {
    MwsReportType: null, // mandatory
    ReportStart: null, // mandatory
    ReportEnd: null, // mandatory
    ReportOptions: null // further report options, optional, eg 'ShowSalesChannel=true'
  },
  result: { // where to store the data fetched from mws
    destination: 'stdout', // file or stdout, optional
    file: { // manadory if destination = 'file', otherwise ignored
      path: '$MWS_EXTRACT_DIR', // directory where to store the file
      name: 'file.xml', // filename. appended to path
      compression: 'gzip', // if destination 'file'; 'gzip' = gzip the file, https://nodejs.org/api/zlib.html
      namePostfix: '.gzip' // any filename postfix; '.gzip' = file.xml becomes file.xml.gzip
    }
  },
  exec: {  // optional
    encoding: 'utf8',
    env: {
      MWS_EXTRACT_DIR: '.'
    },
    timeout: 30 * 1000, // ms
    maxBuffer: 1024 * 1024 // bytes
  },
  system: {
    phpCommand: 'php' // optional
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

module.exports = async function (parametersIn) {

  if (!(parametersIn)) {
    throw new ClientException('No parameters received');
  }
  if (!(parametersIn.merchant)) {
    throw new ClientException('Parameters missing mandatory property "merchant"');
  }

  // parametersIn's properties overwrites parametersDefault's properties with the same name
  const parameters = { ...parametersDefaults, ...parametersIn }

  const exec = util.promisify(require('child_process').exec);
  const phpScript = path.dirname(__filename) + "/php/RequestReport.php";
  const loc = 'amazon-mws-reports-client:';
  const debug = parameters.dev && parameters.dev.debug;

  const command = parameters.system.phpCommand + " " + phpScript + " '" + JSON.stringify(parameters) + "'"
  // + " | grep 'END:-_]%£j+:'"
  // + " | tail -n 1" // return only the last line
  ;

  if (debug) {
    console.log(loc, 'phpScript:', phpScript);
    console.log(loc, 'parameters:', parameters);
    console.log(loc, 'command:', command);
    console.log(loc, 'path.dirname:', path.dirname(__filename));
  }

  try {
    const { stdout, stderr } = await exec(command, parameters.exec);
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
