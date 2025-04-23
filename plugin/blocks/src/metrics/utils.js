import { encode as msgpackEncode, decode as msgpackDecode } from 'msgpackr';
import base62 from 'base62/lib/ascii';

export function getFromNow(date) {
	const [match, now, dash, amount, unit] = date?.match(/(now)(.?)(?:(\d+)([smhdMy]+))?/) || [];

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

export function parseTime(input) {
	const iso8601WithoutTRegex = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})(\.\d+)?(Z|([+-]\d{2}:\d{2}))?$/;
	if (iso8601WithoutTRegex.test(input)) {
		return input;
	}

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

export function buildTree(element, apiPath) {
	const isGroup = element.classList.contains("wp-block-group");
	const isGraph = element.classList.contains("wp-block-wpcloud-graph");

	const classNames = Array.from(element.classList)
	const style = styleToObject(element);

	if ( isGraph ) {
		return {
			type: "graph",
			attributes: JSON.parse(element.dataset.graphAttributes),
			classNames,
			apiPath,
			style,
		};
	}
	if (isGroup) {
		return {
			type: "group",
			classNames,
			style,
			children: Array.from(element.children)
				.map((child) => buildTree(child, apiPath))
				.filter(Boolean),
		};
	}
	return null;
}

export function styleToObject(element) {
	const style = element.getAttribute("style");
	if (!style) {
		return {};
	}
	const styleObject = {};
	const styles = style.split(";").filter(Boolean);
	styles.forEach((style) => {
		const [key, value] = style.split(":").map((s) => s.trim());
		const camelCased = key.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
		styleObject[camelCased] = value;
	});
	return styleObject;
}

const base62chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

function bufferToBase62(buffer) {
	let value = 0n;
	for (const byte of buffer) {
		value = (value << 8n) + BigInt(byte);
	}

	let result = '';
	while (value > 0) {
		result = base62chars[value % 62n] + result;
		value = value / 62n;
	}

	return result || '0';
}

function base62ToBuffer(base62) {
	let value = 0n;
	for (const char of base62) {
		value = value * 62n + BigInt(base62chars.indexOf(char));
	}

	const bytes = [];
	while (value > 0) {
		bytes.unshift(Number(value & 255n));
		value >>= 8n;
	}

	return new Uint8Array(bytes);
}

export function encodeFilter(obj) {
  const json = JSON.stringify(obj);
  const buffer = new TextEncoder().encode(json);
  return bufferToBase62(buffer);
}

export function decodeFilter(base62) {
  const buffer = base62ToBuffer(base62);
  const json = new TextDecoder().decode(buffer);
  return JSON.parse(json);
}