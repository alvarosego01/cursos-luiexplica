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
    logPrefix: "mentora",
    open: false,

    notify: true,
    injectChanges: true,
       proxy: {
      target: "https://mentora.dev/",
      proxyOptions: {
        changeOrigin: true, // ✅ manda Host: mentora.dev
        secure: false       // ✅ no revienta si el cert local no es trusted
      }
    },
    port: 3025,
    https: true,
    serveStatic: [{
      route: "/",
      dir: "assets/css"
    }],
    files: ['dist/styles/**'],
    snippetOptions: {
      rule: {
        match: /<\/head>/i,
        fn: function (snippet, match) {
          return '<link rel="stylesheet" type="text/css" href="/mentoraStyles.css"/>' + snippet + match;
        }
      }
    }

  });

});

gulp.task('browserSync-Server', () => {

  browserSync.init({
    logPrefix: "mentora",
    open: true,
    https: true,
    online: true,
    notify: true,
    port: 3100,
    injectChanges: true,
    proxy: "https://mentora.luiexplica.com/",
    files: ['dist/styles/**'],
    serveStatic: ["dist/styles"],
    files: "assets/css/mentoraStyles.css",
    snippetOptions: {
      rule: {
        match: /<\/head>/i,
        fn: function (snippet, match) {
          return '<link rel="stylesheet" type="text/css" href="/mentoraStyles.css"/>' + snippet + match;
        }
      }
    }

  });
});

gulp.task('sass', () => {
  return gulp.src('./resources/assets/styles/**/*.scss')

    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      sourceMap: true,

      // maxConcurrency: 4,
      parallel: true
    }).on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(postcss([autoprefixer()]))
    .pipe(csso())
    .pipe(gulp.dest('./dist/styles'))
    .pipe(browserSync.stream());
});

gulp.task('sassGeneral', () => {
  return gulp.src('./resources/assets/styles/**/*.scss')

    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      sourceMap: true,

      // maxConcurrency: 4,
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

  gulp.watch("./resources/assets/styles/**/*.scss", gulp.series('sass'));

  gulp.watch("./resources/assets/scripts/**/*.js", gulp.series('js'));

});

gulp.task('local', gulp.series(gulp.parallel(
  'compile-init',
  'watch',
  'browserSync-Local'
)));

gulp.task('online', gulp.series(gulp.parallel(
  'compile-init',
  'watch',
  'browserSync-Server'
)));


