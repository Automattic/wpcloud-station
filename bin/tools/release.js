const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Configuration
const pluginDir = path.join(__dirname, '../plugin'); // Path to the plugin directory
const pluginFile = path.join(pluginDir, 'wpcloud-station.php'); // Path to the plugin.php file
const githubRepo = 'automattic/wpcloud-station'; // Replace with your GitHub repo
const baseBranch = 'trunk'; // Branch to base the release on

// Function to extract the plugin version
function getPluginVersion(filePath) {
	if (!fs.existsSync(filePath)) {
			console.error(`File not found: ${filePath}`);
			process.exit(1);
	}

	const fileContent = fs.readFileSync(filePath, 'utf8');
	const versionMatch = fileContent.match(/Version:\s*(v?\d+\.\d+\.\d+(-beta\.\d+)?)/i);

	if (!versionMatch) {
			console.error('Version not found in plugin file.');
			process.exit(1);
	}

	return versionMatch[1];
}

// Function to check if a GitHub release already exists
function releaseExists(tag) {
	try {
			const releaseList = execSync(`gh release list --repo ${githubRepo}`);
			return releaseList.includes(tag);
	} catch (error) {
			console.error('Failed to fetch release list.');
			process.exit(1);
	}
}

// Function to get PRs between two tags
function getPRsBetweenTags(previousTag, currentTag) {
	if (!previousTag) {
			console.log('No previous tag found. Cannot list PRs.');
			return [];
	}

	console.log(`Fetching PRs between ${previousTag} and ${currentTag}...`);
	const prs = execSync(
			`gh pr list --search "merged:${previousTag}..${currentTag}" --json title,number --jq ".[] | \\\"#\\(.number) \\(.title)\\\""`
	);

	return prs ? prs.split('\n') : [];
}

// Function to get the previous tag
function getPreviousTag() {
	try {
			const tags = execSync('git tag --sort=-v:refname').split('\n');
			return tags[1] || null; // The second tag is the previous version
	} catch {
			console.log('No previous tags found.');
			return null;
	}
}

// Function to create a GitHub release
function createGitHubRelease(tag, releaseTitle, releaseBody) {
	console.log(`Creating GitHub release for tag: ${tag}`);

	const releaseCommand = `gh release create ${tag} --title "${releaseTitle}" --notes "${releaseBody}" --repo ${githubRepo}`;
	return execSync(releaseCommand);
}

// Main function
async function createRelease() {
	console.log('Checking out the trunk branch...');
	execSync(`git checkout ${baseBranch}`);

	console.log('Fetching the latest changes...');
	execSync(`git pull origin ${baseBranch}`);

	// Get the plugin version
	const version = getPluginVersion(pluginFile);
	const tag = `${version}`;
	const previousTag = getPreviousTag();

	// Check if the release already exists
	if (releaseExists(tag)) {
			console.error(`Release for tag ${tag} already exists. Exiting.`);
			process.exit(1);
	}

	// Check if the tag already exists
	try {
			execSync(`git rev-parse ${tag}`);
			console.error(`Tag ${tag} already exists. Exiting.`);
			process.exit(1);
	} catch {
			console.log(`Tag ${tag} does not exist. Proceeding...`);
	}

	// Create a new tag
	console.log(`Creating a new tag: ${tag}`);
	execSync(`git tag ${tag}`);
	execSync(`git push origin ${tag}`);

		// Get PRs between tags
	const prs = getPRsBetweenTags(previousTag, currentTag);
	const prList = prs.length > 0 ? prs.join('\n') : 'No pull requests in this release.';

	// Create the release
	const releaseTitle = `Release ${currentVersion}`;
	const releaseBody = `## Release ${currentVersion}\n\n### Changes:\n${prList}`;
	const releaseUrl = createGitHubRelease(currentTag, releaseTitle, releaseBody);

	console.log('GitHub release created successfully.');
	console.log(releaseUrl);
}

// Run the script
module.exports = createRelease;