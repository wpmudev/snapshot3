'use strict';

// Project paths
var paths = {
    src: 'assets/sass/',
    dist: 'assets/css/'
};

// Load packages
var gulp = require('gulp'),
    watch = require('gulp-watch'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    notify = require('gulp-notify'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
	tildeImporter = require('node-sass-tilde-importer'),
    uglify = require('gulp-uglify');


// Styles task
gulp.task('styles', function() {
    gulp.src(paths.src + '**/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({importer: tildeImporter, outputStyle: 'compressed'})).on('error', function(err) {notify().write(err);})
        .pipe(autoprefixer('last 2 version', '> 1%', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        .pipe(sourcemaps.write("maps"))
        .pipe(gulp.dest(paths.dist))
		.pipe(notify({message: 'You\'re awesome! Changes are ready now.', onLast: true}));
});

// Watch task
gulp.task('watch', function() {
    gulp.watch(paths.src + '**/*.scss', ['styles']);
});

// Register tasks to 'gulp' command
gulp.task('default', ['styles', 'watch']);