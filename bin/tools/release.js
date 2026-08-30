const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Configuration
const pluginDir = path.join(__dirname, '/../../plugin'); // Path to the plugin directory
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

	// Get the commit range between the two tags
	const commitRange = `${previousTag}..${currentTag}`;

	// Get the list of commit SHAs in the range
	const commits = execSync(`git log --pretty=format:"%H" ${commitRange}`, { encoding: 'utf8' })
		.trim()
		.split('\n');

	if (!commits || commits.length === 0) {
		console.log('No commits found between tags.');
		return [];
	}

	// Create a query to find PRs that include these commits
	// We'll use the GitHub search syntax to find PRs that include any of these commits
	const prNumbers = new Set();
	const prDetails = [];

	// Process commits in batches to avoid command line length limits
	const batchSize = 10;
	for (let i = 0; i < commits.length; i += batchSize) {
		const batchCommits = commits.slice(i, i + batchSize);

		// For each commit, try to find the associated PR
		for (const commit of batchCommits) {
			try {
				// Use GitHub CLI to get the PR number for this commit
				const prInfo = execSync(`gh pr list --search "hash:${commit}" --json number,title --limit 1`,
					{ encoding: 'utf8' });

				if (prInfo && prInfo.trim()) {
					const prData = JSON.parse(prInfo);
					if (prData && prData.length > 0) {
						const { number, title } = prData[0];

						// Only add if we haven't seen this PR number before
						if (!prNumbers.has(number)) {
							prNumbers.add(number);
							prDetails.push(`#${number} ${title}`);
						}
					}
				}
			} catch (error) {
				console.log(`Could not find PR for commit ${commit.substring(0, 8)}`);
			}
		}
	}

	console.log(`Found ${prDetails.length} PRs between ${previousTag} and ${currentTag}`);
	return prDetails;
}

// Function to get the previous tag
function getPreviousReleaseTag() {
	try {
			return execSync('gh release view --json tagName -q .tagName', { encoding: 'utf8' }).trim();
	} catch {
			console.log('No previous tags found.');
			return null;
	}
}

// Function to create a GitHub release
function createGitHubRelease(tag, releaseTitle, releaseBody) {
	console.log(`Creating GitHub release for tag: ${tag}`);

	const releaseNotesPath = path.join(__dirname, "release-notes.md");
	fs.writeFileSync(releaseNotesPath, releaseBody);

	const releaseCommand = `gh release create ${tag} --title "${releaseTitle}" --notes-file "${releaseNotesPath}" --repo ${githubRepo}`;
	const result = execSync( releaseCommand, { stdio: "inherit" } );
	fs.unlinkSync(releaseNotesPath);
	return result;
}

// Main function
async function createRelease() {
	console.log('Checking out the trunk branch...');
	execSync(`git checkout ${baseBranch}`);

	console.log('Fetching the latest changes...');
	execSync(`git pull origin ${baseBranch}`);

	// Get the plugin version tag
	const tag = getPluginVersion(pluginFile);

	const previousTag = getPreviousReleaseTag();

	console.log(`Current version: ${previousTag}`);

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

	// Make sure we have the previous tag
	execSync(`git fetch origin tag ${previousTag}`);

	const prs = getPRsBetweenTags(previousTag, tag);
	// Create the release
	const releaseTitle = `Release ${tag}`;
	const releaseBody = `## Changelog
${prs.filter(i => i).join('\n')}
`;

createGitHubRelease(tag, releaseTitle, releaseBody);
console.log(`GitHub release ${tag} created successfully.`);
}

// Run the script
module.exports = createRelease;
