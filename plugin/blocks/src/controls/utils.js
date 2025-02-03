export function updateAttribute(setAttribute) {
	return function (key, transform = (v) => v) {
		return function (value) {
			setAttribute({ [key]: transform(value) });
		};
	};
}