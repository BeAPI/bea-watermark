/*Load all plugin define in package.json*/
var gulp = require('gulp'),
    gulpLoadPlugins = require('gulp-load-plugins'),
    plugins = gulpLoadPlugins();

/*JS task*/
gulp.task('dist', function () {
    gulp.src([
        'assets/js/front.js'
    ])
        .pipe(plugins.uglify())
        .pipe(plugins.rename( { 'suffix' : '.min' } ))
        .pipe(gulp.dest('assets/js/'));

    gulp.src([
        'assets/css/style.css'
    ])
        .pipe(plugins.cssmin())
        .pipe(plugins.rename( { 'suffix' : '.min' } ))
        .pipe(gulp.dest('assets/css/'));
});

gulp.task('dev', function () {
    gulp.src([
        'assets/js/front.js'
    ])
        .pipe(plugins.jshint());
});

// On default task, just compile on demand
gulp.task('default', function() {
    gulp.watch( 'assets/js/front.js', [ 'dev' ] );
});