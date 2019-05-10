const SCSS_WATCH = `./scss/**/*.scss`;
const SCSS_DEST = '../public';
const BABEL_SRC = `./js`;
const BABEL_DEST = `./jses5`;
const CONCAT_SRC = BABEL_DEST;
const CONCAT_DEST = `./jsconcat`;
const UGLIFY_SRC = CONCAT_DEST;
const UGLIFY_DEST = '../public';
const BABEL_WATCH = `${ BABEL_SRC }/**/*.js`;
const CONCAT_WATCH = `${ CONCAT_SRC }/*.js`;
const UGLIFY_WATCH = `${ UGLIFY_SRC }/*.js`;
const SCSS_SRC = `./scss/*.scss`;

const Gulp     = require( 'gulp' );
const Compass  = require( 'gulp-compass' );
const Plumber  = require( 'gulp-plumber' );
const Babel    = require( 'gulp-babel' );
const Concat   = require( 'gulp-concat' );
const Uglify   = require( 'gulp-uglify-es' ).default;
const Read = require( 'fs' ).readFileSync;

const JSFiles = JSON.parse( Read
(
	`${ __dirname }/js-files.json`,
	{ encoding: "utf8" }
) ).JSFiles;

const GetJSFiles = function( files )
{
	let list = [];

	for ( let i = 0; i < files.length; i++ )
	{
		list.push( `${ CONCAT_SRC }/${ files[ i ] }.js` );
	}
	return list;
};

const ConcatItem = function( Name, Files )
{
	return Gulp.src( GetJSFiles( Files ) )
		.pipe( Plumber() )
		.pipe( Concat( `${ Name }.js` ) )
		.pipe( Gulp.dest( CONCAT_DEST ) );
};

Gulp.task
(
	'compass',
	function()
	{
		return Gulp.src( SCSS_SRC ).pipe( Plumber() ).pipe( Compass
		({
			config_file: './config.rb',
			css: SCSS_DEST,
			sass: 'scss'
		}))
		.pipe( Plumber() )
		.pipe( Gulp.dest( './temp' ) );
	}
);

Gulp.task
(
	'babel',
	function()
	{
		return Gulp.src( BABEL_WATCH ).pipe( Plumber() ).pipe( Babel
		({
			plugins: [ '@babel/plugin-transform-arrow-functions',
			'@babel/plugin-transform-template-literals',
			'@babel/plugin-transform-for-of' ],
			presets: [ "@babel/env" ]
		}))
		.pipe( Gulp.dest( BABEL_DEST ) );
	}
);

Gulp.task
(
	'concat',
	function()
	{
		return ( function()
		{
			for ( const Item of JSFiles )
			{
				ConcatItem( Item.dist, Item.src );
			}
		})();
	}
);

Gulp.task
(
	'uglify',
	function()
	{
		return Gulp.src( UGLIFY_WATCH )
		.pipe( Plumber() )
		.pipe( Uglify() )
		.pipe( Gulp.dest( UGLIFY_DEST ) );
	}
);

Gulp.task
(
	'watch',
	function()
	{
		Gulp.watch( SCSS_WATCH, Gulp.series( 'compass' ) );
		Gulp.watch( BABEL_WATCH, Gulp.series( 'babel' ) );
		Gulp.watch( CONCAT_WATCH, Gulp.series( 'concat' ) );
		Gulp.watch( UGLIFY_WATCH, Gulp.series( 'uglify' ) );
	}
);
