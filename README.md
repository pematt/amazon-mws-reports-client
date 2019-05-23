# amazon-mws-reports-client

Until this document has been updated, then see the tests for usage.

Example use:
```
const requestReport = require('amazon-mws-reports-client');

async function runTest () {

  console.log(__filename);

  const parametersMock = {
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
      ReportStart: 'timestammp',
      ReportEnd: 'timestamp',
      ReportParameters: 'other params'
    },
    exec: {
      encoding: 'utf8',
      env: {
        KEY1: 'value1',
        KEY2: 'value2'
      },
      timeout: 60 * 1000,
      maxBuffer: 1024 * 1024
    },
    dev: {
      debug: false,
      mock: true
    }
  }

   const response = await requestReport(parametersMock);

   console.log('MOCK TEST RESPONSE:', response);
}

runTest();
```

Parameters:
```
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
    phpCommand: 'php' // in case there are more than one way to invoce php then one can select one here
  },
  dev: {
    debug: false,
    mock: false
  }
}
```
