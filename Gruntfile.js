/*global module, require */
module.exports = function(grunt) {
	require('load-grunt-tasks')(grunt);

	// Load shared tasks library
	var wpmudev = require('./shared-tasks/loader')(grunt);

	wpmudev.files.add('external', 'lib/WPMUDEV/Dashboard/**/*');
    wpmudev.files.add('external', 'assets/shared-ui/**/*');
    wpmudev.files.add('external', 'shared-tasks/**/*');
    wpmudev.files.add('dev', 'shared-tasks/**/*');
    wpmudev.loader.setup();

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		watch: {
			js: {
				files: 'assets/js/src/**/*.js',
				tasks: 'js'
			}
		},

		jshint: {
			gruntfile: ['Gruntfile.js'],
			dist: {
				src: ['assets/js/src/**/*.js']
			}
		},

		uglify: {
			dist: {
				options: {
					sourceMap: true
				},
				files: {
					'./assets/js/admin.min.js': ['assets/js/src/**/*.js']
				}
			}
		},

		makepot: {
			target: {
				options: {
					domainPath: 'languages/',
					type: 'wp-plugin',
					exclude: ['dist', 'node_modules', 'tests']
				}
			}
		},

		clean: {
			dist: ['dist', 'snapshot.*.zip']
		},

		copy: {
			dist: {
				files: [{
					expand: true,
					cwd: './',
					src: [
						'**/*',
						'!dist/**/*', '!*.zip', '!builds/**/*',
						'!bin/**/*', '!tests/**/*', '!bitbucket-pipelines.yml', '!phpunit.xml',
						'!.sass-cache/**/*', '!node_modules/**/*',
						'!assets/sass/**/*', '!assets/js/src/**/*', '!new_ui/**/*',
						'!Gruntfile.js', '!gulpfile.js', '!package.json', '!package-lock.json', '!README.md',
						'!.gitignore', '!.gitmodules'
					],
					dest: 'dist',
					filter: 'isFile'
				}]
			}
		},

		compress: {
			dist: {
				options: {
					archive: 'builds/<%= pkg.name %>-<%= pkg.version %>.zip',
					level: 9
				},
				expand: true,
				cwd: 'dist/',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		}
	});

	grunt.registerTask('postrelease', function () {
		grunt.task.run('clean:dist');
		var pkg = grunt.config.get('pkg'),
			version = (pkg || {}).version,
			name = (pkg || {}).name
		;
		grunt.log.subhead('Prepared a release package for ' + version);
		grunt.log.writeln('The releasable archive is in builds/' + name + '-' + version);
	});

	grunt.registerTask('live', ['watch']);
	grunt.registerTask('i18n', ['makepot']);
	grunt.registerTask('js', ['uglify']);
	grunt.registerTask('default', ['js']);
	grunt.registerTask('package', ['wpmudev_cleanup', 'wpmudev_copy:full', 'wpmudev_compress']);
	//grunt.registerTask('package', ['clean:dist', 'copy:dist', 'compress:dist']);
	//grunt.registerTask('release', ['i18n', 'js', 'package', 'postrelease']);

	grunt.registerTask('release', function (version) {
		grunt.config.set('wpmudev_release', {
			type: 'full',
			version: version,
			version_define: 'SNAPSHOT_VERSION',
			build: ['js', 'package'],
			cleanup: ['wpmudev_cleanup'],
		});
		grunt.task.run('wpmudev_release');
	});
};
