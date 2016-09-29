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
			fontAwesome:
				src: '<%= dirs.bower %>/font-awesome'
				dest: '<%= dirs.public %>/fonts'
			theme:
				base: '<%= dirs.bower %>/AdminLTE'
				bootstrap: '<%= dirs.theme.base %>/bootstrap'
				dist: '<%= dirs.theme.base %>/dist'
				plugins: '<%= dirs.theme.base %>/plugins'
			themeCDN:
				base: '<%= dirs.asset %>/ThemeCDN'
				js: '<%= dirs.themeCDN.base %>/JavaScripts'
				css: '<%= dirs.themeCDN.base %>/Styles'

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
				src: '<%= dirs.fontAwesome.src %>/fonts/*'
				dest: '<%= dirs.fontAwesome.dest %>'

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
			jsTheme:
				src: [
					'<%= dirs.theme.plugins %>/jQuery/jquery-2.2.3.min.js',
					'<%= dirs.themeCDN.js %>/jquery-ui.min.js'
					'<%= dirs.theme.bootstrap %>/js/bootstrap.min.js'
					'<%= dirs.themeCDN.js %>raphael-min.js'
					'<%= dirs.theme.plugins %>/morris/morris.min.js'
					'<%= dirs.theme.plugins %>/sparkline/jquery.sparkline.min.js'
					'<%= dirs.theme.plugins %>/jvectormap/jquery-jvectormap-1.2.2.min.js'
					'<%= dirs.theme.plugins %>/jvectormap/jquery-jvectormap-world-mill-en.js'
					'<%= dirs.theme.plugins %>/knob/jquery.knob.js'
					'<%= dirs.themeCDN.js %>/moment.min.js'
					'<%= dirs.theme.plugins %>/daterangepicker/daterangepicker.js'
					'<%= dirs.theme.plugins %>/datepicker/bootstrap-datepicker.js'
					'<%= dirs.theme.plugins %>/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'
					'<%= dirs.theme.plugins %>/slimScroll/jquery.slimscroll.min.js'
					'<%= dirs.theme.plugins %>/fastclick/fastclick.js'
					'<%= dirs.theme.dist %>/js/app.min.js'
					'<%= dirs.theme.dist %>/js/demo.js'
					'<%= dirs.coffee.compiled %>/**/*.js'
				]
				dest: '<%= dirs.coffee.dest %>/<%= dirs.preferName %>.js'
				options:
					separator: ';'

			html5shiv:
				src: [
					'<%= dirs.themeCDN.js %>/html5shiv.min.js'
					'<%= dirs.themeCDN.js %>/respond.min.js'
				]
				dest: '<%= dirs.coffee.dest %>/html5shiv.js'
				options:
					separator: ';'

			cssTheme:
				src: [
					'<%= dirs.theme.bootstrap %>/css/bootstrap.min.css'
					'<%= dirs.fontAwesome.src %>/css/font-awesome.min.css'
					'<%= dirs.themeCDN.css %>/ionicons.min.css'
					'<%= dirs.theme.dist %>/css/AdminLTE.min.css'
					'<%= dirs.theme.dist %>/css/skins/_all-skins.min.css'
					'<%= dirs.theme.plugins %>/iCheck/flat/blue.css'
					'<%= dirs.theme.plugins %>/morris/morris.css'
					'<%= dirs.theme.plugins %>/jvectormap/jquery-jvectormap-1.2.2.css'
					'<%= dirs.theme.plugins %>/datepicker/datepicker3.css'
					'<%= dirs.theme.plugins %>/daterangepicker/daterangepicker.css'
					'<%= dirs.theme.plugins %>/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'
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
	grunt.registerTask 'script', ['clean:coffee', 'coffee', 'concat:jsTheme', 'concat:html5shiv', 'uglify']
	grunt.registerTask 'style', ['clean:scss', 'compass', 'concat:cssTheme', 'cssmin']
	grunt.registerTask 'image', ['clean:image', 'imagemin']
	grunt.registerTask 'build', ['copy', 'script', 'style', 'image']
