const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Path to the versioned software
const pluginDir = path.join(__dirname, '/../../plugin', );
const pluginFile = path.join(pluginDir, 'wpcloud-station.php');

const themeDir = path.join(__dirname, '../theme');
const themeFile = path.join(themeDir, 'style.css');

const software = [pluginFile, themeFile];


// GitHub repository details
const baseBranch = 'trunk'; // Branch to base the pull request on

// Function to run shell commands
function runCommand(command, options = {}) {
	try {
		return execSync(command, { stdio: 'inherit', encoding: 'utf8', ...options });
	} catch (error) {
		console.error(`Error running command: ${command}`);
		process.exit(1);
	}
}

// Function to increment version
function incrementVersion(version, type = 'patch') {
	const betaMatch = version.match(/^v?(\d+)\.(\d+)\.(\d+)(-beta\.(\d+))?$/i);
	if (!betaMatch) {
		console.error(`Invalid version format: ${version}`);
		return version;
	}

	const [, major, minor, patch, betaSuffix, betaNumber] = betaMatch.map((v) =>
		isNaN(v) ? v : Number(v)
	);

	if (type === 'major') return `v${major + 1}.0.0`;
	if (type === 'minor') return `v${major}.${minor + 1}.0`;
	if (type === 'patch') return `v${major}.${minor}.${patch + 1}`;
	if (type === 'beta') {
		const newBeta = betaNumber !== undefined ? betaNumber + 1 : 1;
		return `v${major}.${minor}.${patch}-beta.${newBeta}`;
	}

	return version;
}

// Function to update version in a file
function updateVersionInFile(filePath, newVersion) {
	if (!fs.existsSync(filePath)) {
		console.error(`File not found: ${filePath}`);
		return false;
	}

	const fileContent = fs.readFileSync(filePath, 'utf8');
	const updatedContent = fileContent.replace(
		/(Version:\s*)(v?\d+\.\d+\.\d+(-beta\.\d+)?)/i,
		`$1${newVersion}`
	);

	fs.writeFileSync(filePath, updatedContent, 'utf8');
	console.log(`Updated version in ${filePath} to ${newVersion}`);
	return true;
}


// Main function
async function updateVersions(type = 'patch') {
	console.log('Checking out the trunk branch...');
	runCommand(`git checkout ${baseBranch}`);

	// get plugin version
	const pluginContent = fs.readFileSync(pluginFile, 'utf8');
	const versionMatch = pluginContent.match(/Version:\s*(v?\d+\.\d+\.\d+(-beta\.\d+)?)/i);

	if (!versionMatch) {
		console.error('Version not found in file.');
		return;
	}
	const currentVersion = versionMatch[1];
	const newVersion = incrementVersion(currentVersion, type);
	const branchName = `version-bump-${newVersion}`;

	console.log(`Creating a new branch: ${branchName}`);
	runCommand(`git checkout -b ${branchName}`);

	// update the software versions
	software.forEach((file) => {

		// Read the current version from the plugin file
		if (!fs.existsSync(file)) {
			console.error(`Software file not found: ${file}`);
			return;
		}

		const fileContent = fs.readFileSync(file, 'utf8');
		const versionMatch = fileContent.match(/Version:\s*(v?\d+\.\d+\.\d+(-beta\.\d+)?)/i);

		if (!versionMatch) {
			console.error('Version not found in file.');
			return;
		}

		// Update the version in the plugin file and readme file
		updateVersionInFile(file, newVersion);
		runCommand(`git add ${file}`);
	});

	// Commit the changes
	console.log('Committing the changes...');
	runCommand(`git commit -m "Version bump to ${newVersion}"`);

	// Push the branch
	console.log('Pushing the branch...');
	runCommand(`git push -u origin ${branchName}`);

	// Create a pull request
	console.log('Creating a pull request on GitHub...');
	const prTitle = `Version bump to ${newVersion}`;
	const prBody = `Update version to ${newVersion}.`;
	const prCommand = `gh pr create --title "${prTitle}" --body "${prBody}" --base ${baseBranch} --head ${branchName}`;

	const prOutput = execSync(prCommand, { encoding: 'utf8' });

	console.log('Pull request created successfully.');

	// return back to trunk branch
	runCommand(`git checkout ${baseBranch}`);
	runCommand(`git branch -D ${branchName}`);

	// Extract PR number from the output (assumes output includes a URL like https://github.com/owner/repo/pull/42)
	const prMatch = prOutput.match(/\/pull\/(\d+)/);
	const prNumber = prMatch ? prMatch[1] : null;

	if (!prNumber) {
		console.error('Failed to extract PR number from output.');
		process.exit(1);
	}

	console.log(`Pull request created: #${prNumber}`);
	return prNumber;
}

// Run the script with the desired increment type
//const incrementType = process.argv[2] || 'patch'; // Default to 'patch'
//updateVersions(incrementType);
module.exports = updateVersions;