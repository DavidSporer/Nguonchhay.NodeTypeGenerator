'use strict'

module.exports = (grunt)->
	require('time-grunt')(grunt)
	grunt.initConfig
		dirs:
			bower: 'BowerComponents'
			base: 'Resources'
			private: '<%= dirs.base %>/Private'
			public: '<%= dirs.base%>/Public'
			preferName: 'nodeGenerator'
			asset: '<%= dirs.private %>/Assets'
			coffee:
				src: '<%= dirs.asset %>/CoffeeScripts'
				compiled: '<%= dirs.public %>/JavaScripts/Compiled'
				dest: '<%= dirs.public %>/JavaScripts'
			scss:
				src: '<%= dirs.asset %>/Styles',
				dest: '<%= dirs.public %>/Styles'
				complied: '<%= dirs.scss.dest %>/Compiled'
			image:
				src: '<%= dirs.asset %>/UnprocessImages'
				dest: '<%= dirs.public %>/Images'
			fontAwesome: '<%= dirs.bower %>/font-awesome'
			bootstrap:
				base: '<%= dirs.asset %>/Bootstrap'
				js: '<%= dirs.bootstrap.base %>/JavaScripts'
				css: '<%= dirs.bootstrap.base %>/Styles'

		clean:
			font:
				src: ['<%= dirs.fontAwesome.dest %>']
			coffee:
				src: ['<%= dirs.coffee.dest %>/**/*.js']
			scss:
				src: ['<%= dirs.scss.dest %>/**/*.css']
			image:
				src: ['<%= dirs.image.dest %>/**/*.{png,jpg,gif,ico}']

		copy:
			fontAwesome:
				expand: true
				flatten: true
				src: '<%= dirs.fontAwesome %>/fonts/*'
				dest: '<%= dirs.public %>/fonts'

		compass:
			custom:
				options:
					sassDir: '<%= dirs.scss.src %>'
					cssDir: '<%= dirs.scss.complied %>'
					outputStyle: 'expanded'

		coffee:
			app:
				expand: true
				flatten: true,
				src: ['<%= dirs.coffee.src %>/**/*.coffee']
				dest: '<%= dirs.coffee.compiled %>'
				ext: '.js'

		concat:
			js:
				src: [
					'<%= dirs.bootstrap.js %>/jquery.min.js',
					'<%= dirs.bootstrap.js %>/tether.min.js'
					'<%= dirs.bootstrap.js %>/bootstrap.min.js'
					'<%= dirs.coffee.compiled %>/**/*.js'
				]
				dest: '<%= dirs.coffee.dest %>/<%= dirs.preferName %>.js'
				options:
					separator: ';'

			html5shiv:
				src: [
					'<%= dirs.bootstrap.js %>/ie10-viewport-bug-workaround.js'
				]
				dest: '<%= dirs.coffee.dest %>/html5shiv.js'
				options:
					separator: ';'

			css:
				src: [
					'<%= dirs.bootstrap.css %>/bootstrap.min.css'
					'<%= dirs.scss.complied %>/<%= dirs.preferName %>.css'
				]
				dest: '<%= dirs.scss.dest %>/<%= dirs.preferName %>.css'
				options:
					separator: ';'

		uglify:
			js:
				src: '<%= dirs.coffee.dest %>/<%= dirs.preferName %>.js'
				dest: '<%= dirs.coffee.dest %>/<%= dirs.preferName %>.min.js'

			html5shiv:
				src: '<%= dirs.coffee.dest %>/html5shiv.js'
				dest: '<%= dirs.coffee.dest %>/html5shiv.min.js'

		cssmin:
			css:
				src: '<%= dirs.scss.dest %>/<%= dirs.preferName %>.css'
				dest: '<%= dirs.scss.dest %>/<%= dirs.preferName %>.min.css'

		imagemin:
			project:
				files: [{
					expand: true,
					cwd: '<%= dirs.image.src %>',
					src: ['**/*.{png,jpg,gif,ico}'],
					dest: '<%= dirs.image.dest %>'
				}]

		watch:
			grunt:
				files: ['Gruntfile.coffee']
				tasks: 'build'
			coffee:
				files: '<%= dirs.coffee.src %>/**/*.coffee'
				tasks: 'script'
			scss:
				files: [
					'<%= dirs.scss.src %>/**/*.scss'
				],
				tasks: 'style'
			imagemin:
				files: [
					'<%= dirs.image.src %>/**/*.{png,jpg,gif,ico}'
				]
				tasks: 'image'

	grunt.loadNpmTasks 'grunt-contrib-clean'
	grunt.loadNpmTasks 'grunt-contrib-copy'
	grunt.loadNpmTasks 'grunt-contrib-compass'
	grunt.loadNpmTasks 'grunt-contrib-concat'
	grunt.loadNpmTasks 'grunt-contrib-uglify'
	grunt.loadNpmTasks 'grunt-contrib-cssmin'
	grunt.loadNpmTasks 'grunt-contrib-imagemin'
	grunt.loadNpmTasks 'grunt-contrib-coffee'
	grunt.loadNpmTasks 'grunt-contrib-watch'

	grunt.registerTask 'default', ['build', 'watch']
	grunt.registerTask 'script', ['clean:coffee', 'coffee', 'concat:js', 'concat:html5shiv', 'uglify']
	grunt.registerTask 'style', ['clean:scss', 'compass', 'concat:css', 'cssmin']
	grunt.registerTask 'image', ['clean:image', 'imagemin']
	grunt.registerTask 'build', ['copy', 'script', 'style', 'image']
