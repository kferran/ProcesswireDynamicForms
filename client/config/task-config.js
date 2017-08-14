module.exports = {
	html        : false,
	images      : false,
	fonts       : false,
	static      : false,
	svgSprite   : false,
	ghPages     : false,
	stylesheets : true,

	javascripts: {
		entry: {
			// files paths are relative to
			// javascripts.dest in path-config.json
			app: ["./app.js"]
		},
		extensions: ['*', '.js', '.vue', '.json'],
		alias: {
			'vue$': 'vue/dist/vue.esm.js',
		},
		loaders: [
			{
				test: /\.vue$/,
				loader: 'vue-loader',
				query: {
					"presets": ["es2015", "stage-1"]
				}
			},
			{
				test: /\.css$/,
				loaders: [
					'css-loader?+sourceMap',
				],
			},
			{
				test: /\.scss$/,
				loaders: [
					'style-loader',
					'css-loader',
					'sass-loader'
				]
			},
			{
				test: /.*\.(gif|png|jpe?g|svg)$/i,
				loaders: [
					'file-loader',
					'image-webpack-loader?{optimizationLevel: 7, interlaced: false, pngquant:{quality: "65-90", speed: 4}, mozjpeg: { quality: 65 }}',
				],
			},
			{
				test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
				loader: "url-loader?limit=10000&minetype=application/font-woff"
			},
			{
				test: /\.(ttf|eot|svg|otf)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
				loader: "file-loader"
			},
			{
				test: /\.json$/,
				loader: "json-loader"
			}
		],
		provide: {
			$: 'jquery',
			jquery: 'jquery',
			'window.jQuery': 'jquery',
			jQuery: 'jquery',
		}
	},

	browserSync: {
		proxy: {
		  target: "22kill.dev/admin/setup/dynamicforms/"
		}
		// server: {
		// 	baseDir: 'public'
		// }
	},

	production: {
		rev: false
	}
}

