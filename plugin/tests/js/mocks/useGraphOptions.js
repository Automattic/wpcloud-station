// Mock implementation of useGraphOptions
const useGraphOptions = ({ title, data, series, meta, containerRef, showLegend, type }) => {
  return {
    data: data || [],
    width: 800,
    height: 500,
    title: title || 'Mock Graph',
    series: series || [],
    axes: [
      { label: 'X-Axis' },
      { label: 'Y-Axis' }
    ],
    scales: {
      x: { time: true },
      y: { auto: true }
    },
    legend: { show: !!showLegend }
  };
};

export default useGraphOptions;
