
const updateVersions = require('./tools/version.js');

const incrementType = process.argv[2] || 'patch'; // Default to 'patch'
const remote = process.argv[3] === '--remote';
updateVersions(incrementType, remote);