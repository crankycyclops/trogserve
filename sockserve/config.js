require('dotenv').config({path: './config.env'});

module.exports = {

	// Websocket settings
	socket: {

		port: 'SOCKET_PORT' in process.env ? +process.env.SOCKET_PORT : 9000,
		https: 'SOCKET_HTTPS' in process.env ? +process.env.SOCKET_HTTPS : false
	},

	// Connection settings for trogdord
	trogdord: {

		host: 'TROGDORD_HOST' in process.env ? process.env.TROGDORD_HOST : 'localhost',
		port: 'TROGDORD_PORT' in process.env ? +process.env.TROGDORD_PORT : 1040
	},

	// Connection settings for redis
	redis: {

		output: {

			host: 'REDIS_OUTPUT_HOST' in process.env ? process.env.REDIS_OUTPUT_HOST : 'localhost',
			port: 'REDIS_OUTPUT_PORT' in process.env ? +process.env.REDIS_OUTPUT_PORT : 6379,
			channel: 'REDIS_OUTPUT_CHANNEL' in process.env ? process.env.REDIS_OUTPUT_CHANNEL : 'trogdord:out'
		}
	}
};
