const path = require('path');

module.exports = {
  PROXY_URL: 'http://localhost:10003/',
  DEFAULT_PORT: 3000,

  ASSET_PATH: path.resolve(__dirname, './assets'),
  DESTINATION_PATH: path.resolve(__dirname, './dist'),
};
