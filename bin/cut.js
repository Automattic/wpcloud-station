const { execSync } = require('child_process');
const { updateVersions } = require('./tools/version.js');
const { createRelease } = require('./tools/release.js');

// Configuration
const POLL_INTERVAL = 10000; // Check every 10 seconds

// Function to check PR mergeability
async function isMergeable(prNumber) {
	try {
		// Execute the `gh` command to get PR details
		const output = execSync(`gh pr view ${prNumber} --json mergeable`, { encoding: 'utf8' })
		// Parse the JSON output
		const prData = JSON.parse(output)
		// Return the mergeable status
		return prData.mergeable;
	} catch (error) {
		console.error(`Error fetching PR details: ${error.message}`);
		return null;
	}
}

// Function to wait for PR to be mergeable
async function waitForMergeable(prNumber) {
	console.log(`Waiting for PR #${prNumber} to become mergeable...`);

	while (true) {
		try {
			const mergeable = await isMergeable(prNumber);

			if (mergeable === true) {
				console.log(`PR #${prNumber} is mergeable!`);
				break;
			} else if (mergeable === false) {
				console.log(`PR #${prNumber} is not mergeable. Retrying...`);
			} else {
				console.log(`Mergeable status is unknown. Retrying...`);
			}
		} catch (error) {
			console.error(`Error checking mergeability: ${error.message}`);
		}

		// Wait before retrying
		await new Promise((resolve) => setTimeout(resolve, POLL_INTERVAL));
	}
}


async function main() {
	// Update the versions
	const incrementType = process.argv[2] || 'patch'; // Default to 'patch'
	const prNum = updateVersions(incrementType);
	// Start the script
	await waitForMergeable(prNum);

	execSync(`gh pr merge ${prNum} --admin --auto`);
	createRelease();
}


// Run the script
main();