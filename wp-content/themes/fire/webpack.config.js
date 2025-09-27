const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const FireConfig = require('./fire.config');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

const isProduction = process.env.NODE_ENV === 'production';

const aliases = {
  '@': path.resolve(__dirname, '.'),
  '@component': path.resolve(__dirname, 'theme/assets/scripts/components'),
  '@utility': path.resolve(__dirname, 'theme/assets/scripts/utilities'),
  '@font': path.resolve(__dirname, 'theme/assets/fonts'),
  '@template': path.resolve(__dirname, 'templates'),
};

// prettier-ignore
const styleLoaders = [
  {
    test: /\.css$/i,
    use: [
      MiniCssExtractPlugin.loader,
      { loader: 'css-loader', options: { importLoaders: 1 } },
      'postcss-loader',
    ],
  },
];

const scriptLoaders = [
  {
    test: /\.m?js$/,
    use: {
      loader: 'esbuild-loader',
      options: {
        target: 'es2017',
      },
    },
  },
];

const fileLoader = [
  {
    test: /\.(png|jpe?g|gif)$/i,
    use: [
      {
        loader: 'file-loader',
      },
    ],
  },
];

const fontLoaders = [
  {
    test: /\.(ttf|otf|eot|woff|woff2)$/,
    type: 'asset/resource',
  },
];

const svgLoader = [
  {
    test: /\.svg$/,
    use: [
      {
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: 'assets/media/svgs/',
        },
      },
    ],
  },
];

module.exports = {
  devServer: {
    open: false,
  },
  mode: isProduction === true ? 'production' : 'development',
  entry: {
    styles: path.resolve(__dirname, './theme/main.css'),
    admin: path.resolve(__dirname, './theme/admin.css'),
    scripts: path.resolve(__dirname, './theme/main.js'),
  },
  output: { path: FireConfig.DESTINATION_PATH },
  module: {
    rules: [...scriptLoaders, ...styleLoaders, ...fontLoaders, ...fileLoader, ...svgLoader],
  },
  resolve: { alias: aliases },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
    new CleanWebpackPlugin({
      verbose: true,
      cleanAfterEveryBuildPatterns: ['!fonts/*'],
    }),
    new BrowserSyncPlugin(
      {
        proxy: FireConfig.PROXY_URL,
        port: process.env.PORT || FireConfig.DEFAULT_PORT,
        files: FireConfig.WATCHED_FILES,
        ghostMode: false,
        open: false,
      },
      { injectCss: true }
    ),
  ],
  optimization: {
    minimize: isProduction,
    minimizer: [new TerserPlugin(), new CssMinimizerPlugin()],
  },
};
