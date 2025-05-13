const { execSync } = require('child_process');
const updateVersions = require('./tools/version.js');
const createRelease = require('./tools/release.js');

// Configuration
const POLL_INTERVAL = 10000; // Check every 10 seconds

// Function to check PR mergeability and test status
async function isPRReadyToMerge(prNumber) {
	try {
		// Execute the `gh` command to get PR details including mergeable status and status checks
		const output = execSync(`gh pr view ${prNumber} --json mergeable,statusCheckRollup`, { encoding: 'utf8' })

		// Parse the JSON output
		const prData = JSON.parse(output);

		// Check if PR is mergeable
		const isMergeable = prData.mergeable === 'MERGEABLE';

		// Check if all required status checks have passed
		let allTestsPassed = true;
		if (prData.statusCheckRollup && prData.statusCheckRollup.length > 0) {
			for (const check of prData.statusCheckRollup) {
				// Consider only required checks or all checks if none are explicitly required
				if (check.state !== 'SUCCESS') {
					allTestsPassed = false;
					console.log(`Status check "${check.name}" is in state "${check.state}"`);
					break;
				}
			}
		} else {
			console.log('No status checks found for this PR');
		}

		return {
			mergeable: isMergeable,
			testsPassed: allTestsPassed,
			readyToMerge: isMergeable && allTestsPassed
		};
	} catch (error) {
		console.error(`Error fetching PR details: ${error.message}`);
		return {
			mergeable: null,
			testsPassed: null,
			readyToMerge: false
		};
	}
}

// Function to wait for PR to be mergeable and tests to pass
async function waitForPRReadyToMerge(prNumber) {
	console.log(`Waiting for PR #${prNumber} to become mergeable and tests to pass...`);

	while (true) {
		try {
			const status = await isPRReadyToMerge(prNumber);

			if (status.readyToMerge) {
				console.log(`PR #${prNumber} is mergeable and all tests have passed!`);
				break;
			} else {
				if (!status.mergeable) {
					console.log(`PR #${prNumber} is not mergeable. Retrying...`);
				} else if (!status.testsPassed) {
					console.log(`PR #${prNumber} is mergeable but tests have not passed. Retrying...`);
				} else {
					console.log(`PR #${prNumber} status is unknown. Retrying...`);
				}
			}
		} catch (error) {
			console.error(`Error checking PR status: ${error.message}`);
		}

		// Wait before retrying
		await new Promise((resolve) => setTimeout(resolve, POLL_INTERVAL));
	}
}

async function main() {
	// Update the versions
	const incrementType = process.argv[2] || 'patch'; // Default to 'patch'
	const dist = process.argv[3] || './dist'; // Default to './dist'
	const prNum = await updateVersions(incrementType);

	if (incrementType === 'test') {
		console.log(`Zipping software to ${dist}`);
		execSync(`npm run package -- plugin ${dist}`);
		execSync(`npm run package -- theme ${dist}`);
		execSync(`npm run package -- theme-pico ${dist}`);
		execSync(`git switch -`);
		return;
	}

	// Start the script
	await waitForPRReadyToMerge(prNum);

	execSync(`gh pr merge ${prNum} --admin --squash`);
	createRelease();
}

// Run the script
main();
