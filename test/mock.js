const requestReport = require('../src/index.js');

async function runTest () {

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
      debug: true,
      mock: true
    }
  }

   const response = await requestReport(parametersMock);

   console.log('MOCK TEST RESPONSE:', response);
}

console.log('========[MOCK TEST START]========');

runTest();

console.log('========[MOCK TEST END]========');
