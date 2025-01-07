
const { updateVersions } = require('./tools/version.js');

const incrementType = process.argv[2] || 'patch'; // Default to 'patch'
updateVersions(incrementType);