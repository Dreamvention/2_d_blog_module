var gulp = require('gulp'),
	sass = require('gulp-sass'),
	browserSync = require('browser-sync'),
	concat = require("gulp-concat"),
	uglify = require("gulp-uglify"),
	cleanCSS = require('gulp-clean-css'),
	sourcemaps = require('gulp-sourcemaps'),
	autoprefixer = require('gulp-autoprefixer');
var path = require('path');
var fs = require('fs');
var baseDir = path.resolve(__dirname, "../../../");
var themeDir = path.join(baseDir, 'view/theme/default');
var sassDir = path.join(themeDir, 'stylesheet/d_blog_module');
var skinDir = path.join(sassDir, 'theme');

function getFolders(dir) {
	return fs.readdirSync(dir)
		.filter(function (file) {
			return fs.statSync(path.join(dir, file)).isDirectory();
		});
}
gulp.task('browser-sync', function () {
	browserSync({
		proxy: 'http://localhost/302/2_d_blog_module/'
	});
});

gulp.task('sass-bm-bootsrap', function () {
	return gulp.src(sassDir+'/bootstrap.scss')
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(autoprefixer(['last 15 versions']))
		.pipe(sourcemaps.write(''))
		.pipe(gulp.dest(sassDir))
		.pipe(browserSync.stream({match: '**/*.css'}));
});
gulp.task('sass_multi', function () {
	var folders = getFolders(skinDir);
	var tasks = folders.map(function (folder) {
		return gulp.src(path.join(skinDir, folder, folder + '.s*ss'))
			.pipe(sourcemaps.init())
			.pipe(sass().on('error', sass.logError))
			.pipe(autoprefixer(['last 15 versions']))
			.pipe(sourcemaps.write('./'))
			.pipe(gulp.dest(path.join(sassDir, 'theme')))
			.pipe(browserSync.stream({match: '**/*.css'}));
	});
	return tasks;
});
gulp.task('sass', function () {
	console.log(sassDir+'/d_blog_module.scss')
	return gulp.src(sassDir+'/d_blog_module.scss')
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(autoprefixer(['last 15 versions']))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest(sassDir))
		.pipe(browserSync.stream({match: '**/*.css'}));
});
gulp.task('watch', ['browser-sync', 'sass'], function () {
	gulp.watch('catalog/view/theme/default/stylesheet/bootstrap.scss', ['sass-bm-bootsrap']);
	gulp.watch('catalog/view/theme/default/stylesheet/d_blog_module.scss', ['sass']);
	gulp.watch('catalog/theme/**/template/**/*.**', browserSync.reload);
	gulp.watch('catalog/controller/**/*.**', browserSync.reload);
	gulp.watch('catalog/controller/**/**/*.**', browserSync.reload);
});

gulp.task('default', ['watch']);