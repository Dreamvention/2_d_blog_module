var gulp = require('gulp'),
	sass = require('gulp-sass'),
	browserSync = require('browser-sync'),
	concat = require("gulp-concat"),
	uglify = require("gulp-uglify"),
	cleanCSS = require('gulp-clean-css'),
	sourcemaps = require('gulp-sourcemaps'),
	autoprefixer = require('gulp-autoprefixer');

gulp.task('browser-sync', function () {
	browserSync({
		proxy: 'http://localhost/302/d_blog_module/'
	});
});
gulp.task('sass-bm-bootsrap', function () {
	return gulp.src('catalog/view/theme/default/stylesheet/d_blog_module/bootstrap.scss',{ base: 'catalog/view/theme/default/stylesheet/d_blog_module' })
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(autoprefixer(['last 15 versions']))
		.pipe(sourcemaps.write(''))
		.pipe(gulp.dest('./catalog/view/theme/default/stylesheet/d_blog_module'))
		.pipe(browserSync.stream({match: '**/*.css'}));
});

gulp.task('sass', function () {
	return gulp.src('catalog/view/theme/default/stylesheet/d_blog_module/d_blog_module.scss')
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(autoprefixer(['last 15 versions']))
		.pipe(sourcemaps.write(''))
		.pipe(gulp.dest('./catalog/view/theme/default/stylesheet/d_blog_module'))
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