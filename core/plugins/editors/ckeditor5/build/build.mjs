import * as esbuild from 'esbuild';

// Bundle CKEditor 5 + our config into a single self-contained script that
// injects its own CSS (so the plugin only has to load one JS file, as before).
await esbuild.build({
	entryPoints: ['src/ckeditor.js'],
	bundle: true,
	format: 'iife',
	minify: true,
	legalComments: 'none',
	target: ['es2020'],
	loader: { '.css': 'text' },
	outfile: '../assets/js/ckeditor.js',
});
console.log('built assets/js/ckeditor.js');
