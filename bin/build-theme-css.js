#!/usr/bin/env node
const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const postcssNested = require('postcss-nested');
const postcssExpandSelectors = require('postcss-expand-selectors');

/**
 * Process CSS file with PostCSS plugins and clean up the output
 *
 * @param {string} file - Path to the CSS file
 * @returns {Promise<string>} - Processed CSS content
 */
async function processCss(file) {
	const cssIn = fs.readFileSync(file, 'utf8');
	const result = await postcss([postcssNested, postcssExpandSelectors]).process(cssIn, { from: undefined });

	// Clean up the tabs
	const pattern = /(?:(\t+)(&?)|(\n\t+}))/g;
	return result.css.replace().replace(pattern, (match, tabs, ampersand, bracket) => {
		if (bracket) {
			return "\n}";
		}
		return ampersand ? "&" : "\t";
	});
}

/**
 * Main function to process CSS files and update theme.json
 *
 * @param {string} themeDir - Path to the theme directory
 */
async function main() {
	// Get theme directory from command line arguments
	const args = process.argv.slice(2);
	let themeDir = '';
	let verbose = false;
	let filters = [];

	// Parse command line arguments
	for (let i = 0; i < args.length; i++) {
		const arg = args[i];
		if (arg === '-V' || arg === '--verbose') {
			verbose = true;
		} else if (arg === '-h' || arg === '--help') {
			console.log('Usage: node build-theme-css.js [options] <theme-directory> [block-filters...]');
			console.log('');
			console.log('Options:');
			console.log('  -V, --verbose    Show verbose output including processed CSS');
			console.log('  -h, --help       Show this help message');
			console.log('');
			console.log('Arguments:');
			console.log('  <theme-directory> Path to the theme directory (required)');
			console.log('  [block-filters]   Optional block names to filter (e.g., core/query wpcloud/button)');
			process.exit(0);
		} else if (!themeDir && !arg.startsWith('-')) {
			themeDir = arg;
		} else if (!arg.startsWith('-')) {
			filters.push(arg);
		}
	}

	// Validate theme directory
	if (!themeDir) {
		console.error('Error: Theme directory is required');
		console.log('Usage: node build-theme-css.js [options] <theme-directory> [block-filters...]');
		process.exit(1);
	}

	// Resolve theme directory path
	const themeDirPath = path.resolve(process.cwd(), themeDir);

	// Check if theme directory exists
	if (!fs.existsSync(themeDirPath)) {
		console.error(`Error: Theme directory '${themeDirPath}' does not exist`);
		process.exit(1);
	}

	// Check if theme.json exists
	const themeJsonPath = path.join(themeDirPath, 'theme.json');
	if (!fs.existsSync(themeJsonPath)) {
		console.error(`Error: theme.json not found in '${themeDirPath}'`);
		process.exit(1);
	}

	// Load theme.json
	let theme;
	try {
		theme = require(themeJsonPath);
	} catch (error) {
		console.error(`Error loading theme.json: ${error.message}`);
		process.exit(1);
	}

	// Check if blocks exist in theme.json
	let blocks = theme?.styles?.blocks;
	if (!blocks) {
		console.log('No blocks found in theme.json');
		process.exit(1);
	}

	// Get blocks to process
	if (filters.length > 0) {
		blocks = Object.keys(blocks).filter(block => filters.find(filter => block.startsWith(filter)));
	} else {
		blocks = Object.keys(blocks);
	}

	// Check if blocks/src directory exists
	const blocksSrcDir = path.join(themeDirPath, 'assets', 'blocks', 'src');
	console.log(`Looking for blocks/src directory at: ${blocksSrcDir}`);
	if (!fs.existsSync(blocksSrcDir)) {
		console.log(`No blocks/src directory found at '${blocksSrcDir}'`);
		process.exit(0);
	}

	console.log(`Processing CSS for theme: ${path.basename(themeDirPath)}`);
	console.log(`Found ${blocks.length} blocks to process`);

	// Process each block
	for (const block of blocks) {
		const cssFile = path.join(blocksSrcDir, `${block.replace('/', '-')}.css`);
		if (!fs.existsSync(cssFile)) {
			// if the block _had_ a style sheet then remove the css key
			if (theme.styles.blocks[block].css) {
				delete theme.styles.blocks[block].css;
				console.log(`Removed CSS for ${block} (file not found)`);
			}
			continue;
		}

		console.log(`Processing ${block} ...`);
		const cssString = await processCss(cssFile);
		if (verbose) {
			console.log("");
			console.log(cssString);
			console.log("\n\n");
		}
		theme.styles.blocks[block].css = cssString;
	}

	// Write updated theme.json
	fs.writeFileSync(themeJsonPath, JSON.stringify(theme, null, 2));
	console.log(`Updated theme.json in ${themeDirPath}`);
}

main().catch(error => {
	console.error(`Error: ${error.message}`);
	process.exit(1);
});
