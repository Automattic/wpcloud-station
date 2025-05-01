import uPlot from 'uplot';

/**
 * Local dependencies.
 */
import Quadtree, { pointWithin } from './quadtree.js';
import { distr, SPACE_BETWEEN } from './distr.js';

/**
 * Plugin that customizes legend clicks
 * When a legend item is clicked, it shows only that series and hides all others
 */
export function isolateStackedPlugin(originalData) {
	let activeSeriesIdx = null; // Track which series is currently active
	let stackedData = null; // Store the stacked data

	return {
		hooks: {
			ready: (u) => {

				// Store the stacked data that's currently in the chart
				if (u.data && Array.isArray(u.data) && u.data.length > 0) {
					stackedData = [...u.data];
				}

				// Find the legend element
				const legendEl = u.root.querySelector(".u-legend");

				if (legendEl) {
					// Add capture phase event listener to intercept clicks before they reach uPlot's handlers
					legendEl.addEventListener("click", (e) => {
						// Stop propagation and prevent default to block uPlot's default behavior
						e.stopPropagation();
						e.preventDefault();

						// Find the closest legend item (tr element)
						const target = e.target;
						const legendItem = target.closest("tr");

						if (!legendItem) {
							return false;
						}

						// Find all legend items (tr elements)
						const legendItems = legendEl.querySelectorAll("tr");

						// Get the index of the clicked legend item
						let clickedIdx = Array.from(legendItems).indexOf(legendItem);

						// If the same item is clicked again, show all series
						if (activeSeriesIdx === clickedIdx) {
							console.log("Showing all series");
							// Remove u-off class from all legend items
							legendItems.forEach(item => {
								item.classList.remove("u-off");
							});
							activeSeriesIdx = null;

							// Restore the stacked data
							if (stackedData) {
								u.setData(stackedData);
							}
						} else {
							console.log("Showing only series", clickedIdx);
							// Apply u-off class to all legend items except the clicked one
							legendItems.forEach((item, idx) => {
								if (idx !== clickedIdx) {
									item.classList.add("u-off");
								} else {
									item.classList.remove("u-off");
								}
							});
							activeSeriesIdx = clickedIdx;

							// Replace the data with original data for the clicked series
							if (originalData && Array.isArray(originalData) && originalData.length > clickedIdx) {
								++clickedIdx; // Adjust for the x-axis series

								// Create a new data array with just the x-axis values and the clicked series
								const newData = stackedData.map((series, idx) => {
									if (idx === 0) {
										// Keep x-axis values
										return series;
									} else if (idx === clickedIdx) {
										// Use the original unstacked data for this series
										return originalData[idx];
									} else {
										// For other series, create an array of the same length as the x-axis but with null values
										return Array(stackedData[0].length).fill(null);
									}
								});

								// Update the graph with the new data
								u.setData(newData);
							}
						}

						return false;
					}, true); // true for capture phase
				} else {
					console.warn("Legend element not found");
				}
			}
		}
	};
}

/**
 * Plugin that prevents legend clicks for non-stacked bar charts
 */
export function preventLegendClickPlugin() {
	return {
		hooks: {
			ready: (u) => {
				// Find the legend element
				const legendEl = u.root.querySelector(".u-legend");

				if (legendEl) {
					// Add capture phase event listener to intercept clicks before they reach uPlot's handlers
					legendEl.addEventListener("click", (e) => {
						// Stop propagation and prevent default to block uPlot's default behavior
						e.stopPropagation();
						e.preventDefault();
						return false;
					}, true); // true for capture phase
				}
			}
		}
	};
}

/**
 * This from https://github.com/leeoniya/uPlot/blob/4315544c319a2c9d561ebd29f54d06a034fb16f6/demos/grouped-bars.js
 */
export function seriesBarsPlugin(opts = {}) {
	let pxRatio;
	let font;

	let { ignore = [] } = opts;

	function setPxRatio() {
		pxRatio = devicePixelRatio;
		font = Math.round(10 * pxRatio) + "px Arial";
	}

	setPxRatio();

	window.addEventListener('dppxchange', setPxRatio);

	const { ori = 0, dir = 1, stacked = false } = opts;

	const groupWidth = 0.9;
	const groupDistr = SPACE_BETWEEN;

	const barWidth   = 1;
	const barDistr   = SPACE_BETWEEN;

	function distrTwo(groupCount, barCount, barSpread = true, _groupWidth = groupWidth) {
		let out = Array.from({length: barCount}, () => ({
			offs: Array(groupCount).fill(0),
			size: Array(groupCount).fill(0),
		}));

		distr(groupCount, _groupWidth, groupDistr, null, (groupIdx, groupOffPct, groupDimPct) => {
			distr(barCount, barWidth, barDistr, null, (barIdx, barOffPct, barDimPct) => {
				out[barIdx].offs[groupIdx] = groupOffPct + (barSpread ? (groupDimPct * barOffPct) : 0);
				out[barIdx].size[groupIdx] = groupDimPct * (barSpread ? barDimPct : 1);
			});
		});

		return out;
	}

	let barsPctLayout;
	let barsColors;

	let barsBuilder = uPlot.paths.bars({
		radius: 0,
		disp: {
			x0: {
				unit: 2,
			//	discr: false, (unary, discrete, continuous)
				values: (u, seriesIdx, idx0, idx1) => barsPctLayout[seriesIdx].offs,
			},
			size: {
				unit: 2,
			//	discr: true,
				values: (u, seriesIdx, idx0, idx1) => barsPctLayout[seriesIdx].size,
			},
			...opts.disp,
		/*
			// e.g. variable size via scale (will compute offsets from known values)
			x1: {
				units: 1,
				values: (u, seriesIdx, idx0, idx1) => bucketEnds[idx],
			},
		*/
		},
		each: (u, seriesIdx, dataIdx, lft, top, wid, hgt) => {
			// we get back raw canvas coords (included axes & padding). translate to the plotting area origin
			lft -= u.bbox.left;
			top -= u.bbox.top;
			qt.add({x: lft, y: top, w: wid, h: hgt, sidx: seriesIdx, didx: dataIdx});
		},
	});

	function drawPoints(u, sidx, i0, i1) {
		u.ctx.save();

		u.ctx.font         = font;
		u.ctx.fillStyle    = "black";

		uPlot.orient(u, sidx, (series, dataX, dataY, scaleX, scaleY, valToPosX, valToPosY, xOff, yOff, xDim, yDim, moveTo, lineTo, rect) => {
			const _dir = dir * (ori == 0 ? 1 : -1);

			const wid = Math.round(barsPctLayout[sidx].size[0] * xDim);

			barsPctLayout[sidx].offs.forEach((offs, ix) => {
				if (dataY[ix] != null) {
					let x0     = xDim * offs;
					let lft    = Math.round(xOff + (_dir == 1 ? x0 : xDim - x0 - wid));
					let barWid = Math.round(wid);

					let yPos = valToPosY(dataY[ix], scaleY, yDim, yOff);

					let x = ori == 0 ? Math.round(lft + barWid/2) : Math.round(yPos);
					let y = ori == 0 ? Math.round(yPos)           : Math.round(lft + barWid/2);

					/** use this to rotate the text */
					/*
					u.ctx.save();
					u.ctx.translate(x, y);
					u.ctx.rotate((-45 * Math.PI) / 180);
					u.ctx.textAlign    = ori == 0 ? "center" : dataY[ix] >= 0 ? "left" : "right";
					u.ctx.textBaseline = ori == 1 ? "middle" : dataY[ix] >= 0 ? "bottom" : "top";
					u.ctx.fillText(dataY[ix], 20, 0);
					u.ctx.restore();
					*/
					u.ctx.textAlign    = ori == 0 ? "center" : dataY[ix] >= 0 ? "left" : "right";
					u.ctx.textBaseline = ori == 1 ? "middle" : dataY[ix] >= 0 ? "bottom" : "top";

					const text = dataY[ix] || '';
					u.ctx.fillText(text, x, y);
				}
			});
		});

		u.ctx.restore();
	}

	function range(u, dataMin, dataMax) {
		let [min, max] = uPlot.rangeNum(0, dataMax, 0.05, true);
		return [0, max];
	}

	let qt;

	return {
		hooks: {
			drawClear: u => {
				qt = qt || new Quadtree(0, 0, u.bbox.width, u.bbox.height);

				qt.clear();

				// force-clear the path cache to cause drawBars() to rebuild new quadtree
				u.series.forEach(s => {
					s._paths = null;
				});

				// Make the quadtree available to other plugins
				u.qt = qt;

				barsPctLayout = [null].concat(distrTwo(u.data[0].length, u.series.length - 1 - ignore.length, !stacked, groupWidth));

				// TODOL only do on setData, not every redraw
				if (opts.disp?.fill != null) {
					barsColors = [null];

					for (let i = 1; i < u.data.length; i++) {
						barsColors.push({
							fill: opts.disp.fill.values(u, i),
							stroke: opts.disp.stroke.values(u, i),
						});
					}
				}
			}
		},
		opts: (u, opts) => {
			const yScaleOpts = {
				range,
				ori: ori == 0 ? 1 : 0,
			};

			// hovered
			let hRect;

			uPlot.assign(opts, {
				select: {show: false},
				cursor: {
					show: false, // Completely disable the cursor/hover effect
				},
				scales: {
					x: {
						time: false,
						distr: 2,
						ori,
						dir,
					//	auto: true,
						range: (u, min, max) => {
							min = 0;
							max = Math.max(1, u.data[0].length - 1);

							let pctOffset = 0;

							distr(u.data[0].length, groupWidth, groupDistr, 0, (di, lftPct, widPct) => {
								pctOffset = lftPct + widPct / 2;
							});

							let rn = max - min;

							if (pctOffset == 0.5)
								min -= rn;
							else {
								let upScale = 1 / (1 - pctOffset * 2);
								let offset = (upScale * rn - rn) / 2;

								min -= offset;
								max += offset;
							}

							return [min, max];
						}
					},
					rend:   yScaleOpts,
					size:   yScaleOpts,
					mem:    yScaleOpts,
					inter:  yScaleOpts,
					toggle: yScaleOpts,
				}
			});

			if (ori == 1) {
				opts.padding = [0, null, 0, null];
			}

			uPlot.assign(opts.axes[0], {
				splits: (u, axisIdx) => {
					const _dir = dir * (ori == 0 ? 1 : -1);
					const splits = u._data[0].slice();
					return _dir == 1 ? splits : splits.reverse();
				},
				values: (u, ticks) => {
					const N = 10;
					return ticks.map(( v, idx ) => {
						if (idx % N === 0) {
							const date = new Date( v * 1000 );
							return date.toLocaleTimeString();
						}
						return '';
					});
				},
				gap:        15,
				size:       ori == 0 ? 40 : 150,
				labelSize:  20,
				grid:       {show: false},
				ticks:      {show: false},

				side:       ori == 0 ? 2 : 3,
			});

			opts.series.forEach((s, i) => {

				if (i > 0 && !ignore.includes(i)) {
					uPlot.assign(s, {
						paths: barsBuilder,
						points: {
							// Don't show points (totals) for any bar graphs
							show: false
						}
					});
				}
			});
		}
	};
}
