#!/usr/bin/env node
const fs = require('fs');
const { join } = require('path');
const postcss = require('postcss')
const postcssNested = require('postcss-nested')
const postcssExpandSelectors = require('postcss-expand-selectors')

const theme = require('../theme.json');

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

async function main() {
	let blocks = theme?.styles?.blocks;
	if (!blocks) {
		console.log('No blocks found in theme.json');
		exit(1);
	}

	// Apply any passed in filters
	let verbose = false;
	if (process.argv.includes('-V') || process.argv.includes('--verbose')) {
		verbose = true;
	}
	const filters = process.argv.slice(2).filter(arg => !arg.startsWith('-'));
	if (filters.length > 0) {
		blocks = Object.keys(blocks).filter(block => filters.find(filter => block.startsWith(filter)));
	}

	for (const block of blocks) {
		const cssFile = join(__dirname, '..', 'assets', 'blocks', 'src', `${block.replace('/', '-')}.css`);
		if (!fs.existsSync(cssFile)) {
			// if the block _had_ a style sheet then remove the css key
			if (theme.styles.blocks[block].css) {
				delete theme.styles.blocks[block].css;
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

	fs.writeFileSync(join(__dirname, '..', 'theme.json'), JSON.stringify(theme, null, 2));
}

main();