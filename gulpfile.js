
    'use strict';

    const browserSync = require('browser-sync').create(),
        reload = browserSync.reload,
        gulp = require('gulp'),
        sass = require('gulp-dart-sass'),
        sourcemaps = require('gulp-sourcemaps'),
        csso = require('gulp-csso'),
        pump = require('pump'),
        uglify = require('gulp-uglify'),
        plumber = require('gulp-plumber'),
        autoprefixer = require('autoprefixer'),
        postcss = require('gulp-postcss'),
        cached = require("gulp-cached"),
        watch = require('gulp-watch');


    const changed = require('gulp-changed');

    var i;

    gulp.task('browserSync-Local', () => {

        browserSync.init({
            logPrefix: "mentora-lui",
            open: false,

            notify: true,
            injectChanges: true,
            proxy: "mentora.dev",
            files: ['dist/styles/**'],
            port: 3025,
            serveStatic: ["assets/css"],
            files: "assets/css/cursos_estilos.css",
            snippetOptions: {
                rule: {
                    match: /<\/head>/i,
                    fn: function (snippet, match) {
                        return '<link rel="stylesheet" type="text/css" href="/cursos_estilos.css"/>' + snippet + match;
                    }
                }
            }

        });

    });

    gulp.task('browserSync-Server', () => {

        browserSync.init({
            logPrefix: "cursos-lui",
            open: true,
            https: true,
            online: true,
            notify: true,
            injectChanges: true,
            proxy: "https://mentora.luiexplica.com/",

            serveStatic: ["dist/styles"],
            files: "dist/styles/global.css",
            snippetOptions: {
                rule: {
                    match: /<\/head>/i,
                    fn: function (snippet, match) {
                        return '<link rel="stylesheet" type="text/css" href="/cursos_estilos.css"/>' + snippet + match;
                    }
                }
            }

        });
    });

    gulp.task('sass', () => {
        return gulp.src('./resources/assets/styles/**/*.sass')

            .pipe(sourcemaps.init())
            .pipe(sass({
                outputStyle: 'compressed',
                sourceMap: true,

                maxConcurrency: 4,
                parallel: true
            }).on('error', sass.logError))
            .pipe(sourcemaps.write())
            .pipe(postcss([autoprefixer()]))
            .pipe(csso())
            .pipe(gulp.dest('./dist/styles'))
            .pipe(browserSync.stream());
    });

    gulp.task('sassGeneral', () => {
        return gulp.src('./resources/assets/styles/**/*.sass')

            .pipe(sourcemaps.init())
            .pipe(sass({
                outputStyle: 'compressed',
                sourceMap: true,

                maxConcurrency: 4,
                parallel: true
            }).on('error', sass.logError))
            .pipe(sourcemaps.write())
            .pipe(postcss([autoprefixer()]))
            .pipe(csso())
            .pipe(gulp.dest('./dist/styles'))
            .pipe(browserSync.stream());
    });

    gulp.task('js', () => {
        return gulp.src('./resources/assets/scripts/**/*.js')
            .pipe(watch('./resources/assets/scripts/**/*.js'))
            .pipe(plumber(
                // {errorHandler: errorScripts},
                function (error) {
                    console.log(error);
                    this.emit('end');
                }
            ))
            .pipe(uglify())
            .pipe(gulp.dest('./dist/scripts/'))
            .pipe(browserSync.stream());
    });

    gulp.task('compile-init', gulp.series(gulp.parallel('sassGeneral', 'js')));
    gulp.task('watch', () => {

        gulp.watch("./resources/assets/styles/**/*.sass", gulp.series('sass'));

        gulp.watch("./resources/assets/scripts/**/*.js", gulp.series('js'));

    });

    gulp.task('local', gulp.series(gulp.parallel(
        'compile-init',
        'watch',
        'browserSync-Local'
    )));