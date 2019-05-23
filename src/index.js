const util = require('util');
const exec = util.promisify(require('child_process').exec); // https://nodejs.org/api/child_process.html#child_process_child_process_exec_command_options_callback
const path = require('path');

// TODO: comment out the parametersExample
const parametersExample = {
  connection: {
    MaxErrorRetry: 3,
    ProxyHost: 'host', // null if no proxy
    ProxyPort: 'port'  // -1 if no proxy
  },
  target: {
    MwsServiceURL: 'url',
    MwsMarketplaceId: 'aws marketplace id'
  },
  developer: {
    ApplicationName: 'application name',
    ApplicationVersion: 'application version'
  },
  merchant: {
    AwsMerchantId: 'aws merchant id',
    MwsAccessKeyId: 'aws access key id',
    MwsSecretAccessKey: 'aws secret access key'
  },
  query: {
    MwsReportType: 'report type',
    ReportStart: 'timestamp',
    ReportEnd: 'timestamp',
    ReportParameters: 'other params'
  },
  result: { // where to store the data fetched from mws
    destination: 'file', // file/stdout
    file: {
      path: '$STORAGE_DIR/', // path where to store the file, including trailing slash
      name: 'filename.csv', // filename. if path is present then exclude path here
      compression: 'gzip', // if destination 'file': gzip the file, https://nodejs.org/api/zlib.html
      namePostfix: '.gzip' // any postfix to append to the filename (file.csv becomes file.csv.gzip)
    }
  },
  exec: {
    encoding: 'utf8',
    env: {
      KEY1: 'value1',
      KEY2: 'value2'
    },
    timeout: 30 * 1000, // ms
    maxBuffer: 1024 * 1024
  },
  system: {
    phpCommand: 'php' // in case there are more than one php binary on the system one can select a specific version here
  },
  dev: {
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
  const phpScript = "src/php/RequestReport.php";
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

  const command = systemOptions.phpCommand + " " + phpScript + " " + JSON.stringify(parameters)
  // + " | grep 'END:-_]%£j+:'"
  // + " | tail -n 1" // return only the last line
  ;

  if (debug) {
    console.log(loc, 'phpScript:', phpScript);
    console.log(loc, 'parameters:', parameters);
    console.log(loc, 'execOptions:', execOptions);
    console.log(loc, 'systemOptions:', systemOptions);
    console.log(loc, 'command:', command);
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
