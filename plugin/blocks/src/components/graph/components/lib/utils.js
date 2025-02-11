
export function stack(data) {
	console.log("stack", data);
	if (data.length < 2) {
		return [];
	}
	let stackedData = [...data];

	for (let row = 2; row < data.length; row++) {
		stackedData[row] = stackedData[row].map((v, i) => v + stackedData[row - 1][i]);
	}


	// Th upper and lower bands are inverted since we are stacking along the y axis
	return {
		bands: data.slice(1).map((v, idx) => ({ series: [idx + 2, idx + 1] })),
		data: stackedData,
	};

}
