export function updateAttribute(setAttribute) {
	return function (key) {
		return function (value) {
			setAttribute({ [key]: value });
		};
	};
}