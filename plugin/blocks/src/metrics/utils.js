export function getFromNow(date) {
	const [match, now, dash, amount, unit] = date.match(/(now)(.?)(?:(\d+)([smhdMy]+))?/) || [];

	if (!match) {
		return [];
	}
	// make sure the dash is a dash
	if (dash && dash !== '-') {

		return [];
	}
	// 'now-' is not valid
	if (dash && !amount) {
		return [];
	}
	// 'now' is valid
	if (!amount) {
		return [now];
	}
	// 'now-1' is not valid
	if (amount && !unit) {
		return [];
	}
	// units are one character
	if (unit.length !== 1) {
		return [];
	}
	// 'now-1s' is valid
	return [now, amount, unit];
}

export function isValidDate(date) {
	// try parsing the date
	const parsed = Date.parse(date);
	if (!isNaN(parsed)) {
		return true;
	}
	const nowDate = getFromNow(date);
	if (nowDate.length) {
		return true;
	}
	return false;
}

export function parseRelativeTime(input) {
	const [isNow, amount, unit] = getFromNow(input);
	if (!isNow) {
		return null
	}
	let now = new Date();

	switch (unit) {
		case "s": now.setSeconds(now.getSeconds() - amount); break;
		case "m": now.setMinutes(now.getMinutes() - amount); break;
		case "h": now.setHours(now.getHours() - amount); break;
		case "d": now.setDate(now.getDate() - amount); break;
		case "w": now.setDate(now.getDate() - amount * 7); break;
		case "M": now.setMonth(now.getMonth() - amount); break;
		case "y": now.setFullYear(now.getFullYear() - amount); break;
	}

	return now.toISOString().replace("T", " ").split(".")[0];
}