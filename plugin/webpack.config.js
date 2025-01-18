const path = require('path');
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
    ...defaultConfig,
		resolve: {
			...defaultConfig.resolve,
			alias: {
				...defaultConfig.resolve.alias,
				'@wpcloud': path.resolve( __dirname, 'blocks/src' ),
			},
		},
};
