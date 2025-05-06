import { useRef } from 'react';

/**
 * Simple SVG-based Graph component that doesn't rely on UplotReact
 *
 * @param {Object} props Component properties
 * @param {string} props.type The type of graph to render (line, area, bar, stacked-bar)
 * @param {string} props.orientation The orientation of the graph (vertical, horizontal)
 * @param {boolean} props.showLegend Whether to show the legend
 * @param {string} props.legendPosition The position of the legend (bottom, right)
 * @returns {JSX.Element} The rendered graph component
 */
function SimpleGraph({
  type = 'line',
  orientation = 'vertical',
  showLegend = true,
  legendPosition = 'bottom'
}) {
  const containerRef = useRef(null);

  // Demo data for the graph
  const data = [
    { x: 0, y: 50 },
    { x: 10, y: 30 },
    { x: 20, y: 80 },
    { x: 30, y: 60 },
    { x: 40, y: 40 },
    { x: 50, y: 70 },
    { x: 60, y: 90 },
    { x: 70, y: 50 },
    { x: 80, y: 20 },
    { x: 90, y: 40 },
    { x: 100, y: 60 }
  ];

  // Data for a second line
  const data2 = [
    { x: 0, y: 20 },
    { x: 10, y: 40 },
    { x: 20, y: 10 },
    { x: 30, y: 30 },
    { x: 40, y: 70 },
    { x: 50, y: 50 },
    { x: 60, y: 40 },
    { x: 70, y: 80 },
    { x: 80, y: 60 },
    { x: 90, y: 30 },
    { x: 100, y: 50 }
  ];

  // SVG dimensions
  const width = 700;
  const height = 300;
  const padding = 40;

  // For horizontal orientation, keep the same width but adjust the height
  const isHorizontal = orientation === 'horizontal';
  const svgWidth = width; // Always use full width
  const svgHeight = height;

  // Calculate the viewBox
  const viewBox = `0 0 ${svgWidth} ${svgHeight}`;

  // Calculate the scales
  const xScale = (svgWidth - padding * 2) / 100;
  const yScale = (svgHeight - padding * 2) / 100;

  // Bar width for bar charts
  const barWidth = xScale * 6;

  // Generate the x-axis ticks
  const xTicks = [0, 20, 40, 60, 80, 100].map(value => {
    const x = padding + value * xScale;
    return (
      <g key={`x-tick-${value}`}>
        <line
          x1={x}
          y1={svgHeight - padding}
          x2={x}
          y2={svgHeight - padding + 5}
          stroke="#666"
        />
        <text
          x={x}
          y={svgHeight - padding + 20}
          textAnchor="middle"
          fontSize="12"
        >
          {value}
        </text>
      </g>
    );
  });

  // Generate the y-axis ticks
  const yTicks = [0, 20, 40, 60, 80, 100].map(value => {
    const y = svgHeight - padding - value * yScale;
    return (
      <g key={`y-tick-${value}`}>
        <line
          x1={padding - 5}
          y1={y}
          x2={padding}
          y2={y}
          stroke="#666"
        />
        <text
          x={padding - 10}
          y={y + 4}
          textAnchor="end"
          fontSize="12"
        >
          {value}
        </text>
      </g>
    );
  });

  // For horizontal orientation, swap x and y ticks
  const horizontalXTicks = [0, 20, 40, 60, 80, 100].map(value => {
    const y = padding + value * yScale;
    return (
      <g key={`x-tick-${value}`}>
        <line
          x1={padding - 5}
          y1={y}
          x2={padding}
          y2={y}
          stroke="#666"
        />
        <text
          x={padding - 10}
          y={y + 4}
          textAnchor="end"
          fontSize="12"
        >
          {value}
        </text>
      </g>
    );
  });

  const horizontalYTicks = [0, 20, 40, 60, 80, 100].map(value => {
    const x = padding + value * xScale;
    return (
      <g key={`y-tick-${value}`}>
        <line
          x1={x}
          y1={svgHeight - padding}
          x2={x}
          y2={svgHeight - padding + 5}
          stroke="#666"
        />
        <text
          x={x}
          y={svgHeight - padding + 20}
          textAnchor="middle"
          fontSize="12"
        >
          {value}
        </text>
      </g>
    );
  });

  // Render different graph types
  const renderGraph = () => {
    if (isHorizontal) {
      // Horizontal orientation
      switch (type) {
        case 'bar':
          return (
            <>
              {/* Horizontal bars for first series - sorted by value (longest at top) */}
              {[...data]
                .sort((a, b) => b.y - a.y) // Sort by y value in descending order
                .map((point, index) => {
                  // Adjust the y position to space bars evenly across the available height
                  const availableHeight = svgHeight - padding * 2;
                  const barSpacing = availableHeight / data.length;
                  // Significantly increase spacing between bars to prevent overlap
                  const barHeight = Math.min(barSpacing * 0.5, 15); // Limit bar height to 50% of spacing or 15px max
                  const y = padding + barSpacing * index + barSpacing / 2;

                  // Scale the width to use more of the available width
                  const width = point.y * (svgWidth - padding * 2) / 100;
                  return (
                    <rect
                      key={`bar1-${index}`}
                      x={padding}
                      y={y - barHeight / 2} // Center the bar at y position
                      width={width}
                      height={barHeight} // Fixed smaller height
                      fill="rgba(0,51,102,0.8)"
                    />
                  );
                })}
            </>
          );
        case 'stacked-bar':
          return (
            <>
              {/* Horizontal stacked bars - sorted by combined value (longest at top) */}
              {[...data]
                .map((point, index) => ({
                  point,
                  point2: data2[index],
                  totalWidth: point.y + data2[index].y,
                  originalIndex: index
                }))
                .sort((a, b) => b.totalWidth - a.totalWidth) // Sort by combined length in descending order
                .map((item, index) => {
                  // Adjust the y position to space bars evenly across the available height
                  const availableHeight = svgHeight - padding * 2;
                  const barSpacing = availableHeight / data.length;
                  // Significantly increase spacing between bars to prevent overlap
                  const barHeight = Math.min(barSpacing * 0.5, 15); // Limit bar height to 50% of spacing or 15px max
                  const y = padding + barSpacing * index + barSpacing / 2;

                  // Scale the widths to use more of the available width
                  const width1 = item.point.y * (svgWidth - padding * 2) / 100;
                  const width2 = item.point2.y * (svgWidth - padding * 2) / 100;
                  return (
                    <g key={`stacked-bar-${item.originalIndex}`}>
                      <rect
                        x={padding}
                        y={y - barHeight / 2} // Center the bar at y position
                        width={width1}
                        height={barHeight} // Fixed smaller height
                        fill="rgba(0,51,102,0.8)"
                      />
                      <rect
                        x={padding + width1}
                        y={y - barHeight / 2} // Center the bar at y position
                        width={width2}
                        height={barHeight} // Fixed smaller height
                        fill="rgba(51,153,255,0.8)"
                      />
                    </g>
                  );
                })}
            </>
          );
        default: // line or area
          // Generate the path data for horizontal lines - use full width
          const hPathData = data.map((point, index) => {
            const y = padding + point.x * yScale;
            // Scale the x values to use more of the available width
            const x = padding + point.y * (svgWidth - padding * 2) / 100;
            return `${index === 0 ? 'M' : 'L'} ${x} ${y}`;
          }).join(' ');

          const hPathData2 = data2.map((point, index) => {
            const y = padding + point.x * yScale;
            // Scale the x values to use more of the available width
            const x = padding + point.y * (svgWidth - padding * 2) / 100;
            return `${index === 0 ? 'M' : 'L'} ${x} ${y}`;
          }).join(' ');

          if (type === 'area') {
            // For area charts, add closing path to create filled area
            const areaPath1 = hPathData + ` L ${padding} ${padding + 100 * yScale} L ${padding} ${padding} Z`;
            const areaPath2 = hPathData2 + ` L ${padding} ${padding + 100 * yScale} L ${padding} ${padding} Z`;

            return (
              <>
                <path d={areaPath1} fill="rgba(0,51,102,0.3)" stroke="#003366" strokeWidth="2" />
                <path d={areaPath2} fill="rgba(51,153,255,0.3)" stroke="#3399ff" strokeWidth="2" />

                {/* Data points */}
                {data.map((point, index) => {
                  const y = padding + point.x * yScale;
                  // Use the same scaling as the path
                  const x = padding + point.y * (svgWidth - padding * 2) / 100;
                  return (
                    <circle
                      key={`point-${index}`}
                      cx={x}
                      cy={y}
                      r="4"
                      fill="#003366"
                    />
                  );
                })}

                {data2.map((point, index) => {
                  const y = padding + point.x * yScale;
                  // Use the same scaling as the path
                  const x = padding + point.y * (svgWidth - padding * 2) / 100;
                  return (
                    <circle
                      key={`point2-${index}`}
                      cx={x}
                      cy={y}
                      r="4"
                      fill="#3399ff"
                    />
                  );
                })}
              </>
            );
          }

          return (
            <>
              <path d={hPathData} fill="none" stroke="#003366" strokeWidth="2" />
              <path d={hPathData2} fill="none" stroke="#3399ff" strokeWidth="2" />

              {/* Data points */}
              {data.map((point, index) => {
                const y = padding + point.x * yScale;
                // Use the same scaling as the path
                const x = padding + point.y * (svgWidth - padding * 2) / 100;
                return (
                  <circle
                    key={`point-${index}`}
                    cx={x}
                    cy={y}
                    r="4"
                    fill="#003366"
                  />
                );
              })}

              {data2.map((point, index) => {
                const y = padding + point.x * yScale;
                // Use the same scaling as the path
                const x = padding + point.y * (svgWidth - padding * 2) / 100;
                return (
                  <circle
                    key={`point2-${index}`}
                    cx={x}
                    cy={y}
                    r="4"
                    fill="#3399ff"
                  />
                );
              })}
            </>
          );
      }
    } else {
      // Vertical orientation
      switch (type) {
        case 'bar':
          return (
            <>
              {/* Vertical bars for first series */}
              {data.map((point, index) => {
                const x = padding + point.x * xScale;
                const barHeight = point.y * yScale;
                return (
                  <rect
                    key={`bar1-${index}`}
                    x={x - barWidth / 2}
                    y={svgHeight - padding - barHeight}
                    width={barWidth}
                    height={barHeight}
                    fill="rgba(0,51,102,0.8)"
                  />
                );
              })}
            </>
          );
        case 'stacked-bar':
          return (
            <>
              {/* Vertical stacked bars */}
              {data.map((point, index) => {
                const x = padding + point.x * xScale;
                const height1 = point.y * yScale;
                const height2 = data2[index].y * yScale;
                return (
                  <g key={`stacked-bar-${index}`}>
                    <rect
                      x={x - barWidth / 2}
                      y={svgHeight - padding - height1}
                      width={barWidth}
                      height={height1}
                      fill="rgba(0,51,102,0.8)"
                    />
                    <rect
                      x={x - barWidth / 2}
                      y={svgHeight - padding - height1 - height2}
                      width={barWidth}
                      height={height2}
                      fill="rgba(51,153,255,0.8)"
                    />
                  </g>
                );
              })}
            </>
          );
        default: // line or area
          // Generate the path data for the first line
          const pathData = data.map((point, index) => {
            const x = padding + point.x * xScale;
            const y = svgHeight - padding - point.y * yScale;
            return `${index === 0 ? 'M' : 'L'} ${x} ${y}`;
          }).join(' ');

          // Generate the path data for the second line
          const pathData2 = data2.map((point, index) => {
            const x = padding + point.x * xScale;
            const y = svgHeight - padding - point.y * yScale;
            return `${index === 0 ? 'M' : 'L'} ${x} ${y}`;
          }).join(' ');

          if (type === 'area') {
            // For area charts, add closing path to create filled area
            const areaPath1 = pathData + ` L ${padding + 100 * xScale} ${svgHeight - padding} L ${padding} ${svgHeight - padding} Z`;
            const areaPath2 = pathData2 + ` L ${padding + 100 * xScale} ${svgHeight - padding} L ${padding} ${svgHeight - padding} Z`;

            return (
              <>
                <path d={areaPath1} fill="rgba(0,51,102,0.3)" stroke="#003366" strokeWidth="2" />
                <path d={areaPath2} fill="rgba(51,153,255,0.3)" stroke="#3399ff" strokeWidth="2" />

                {/* Data points */}
                {data.map((point, index) => {
                  const x = padding + point.x * xScale;
                  const y = svgHeight - padding - point.y * yScale;
                  return (
                    <circle
                      key={`point-${index}`}
                      cx={x}
                      cy={y}
                      r="4"
                      fill="#003366"
                    />
                  );
                })}

                {data2.map((point, index) => {
                  const x = padding + point.x * xScale;
                  const y = svgHeight - padding - point.y * yScale;
                  return (
                    <circle
                      key={`point2-${index}`}
                      cx={x}
                      cy={y}
                      r="4"
                      fill="#3399ff"
                    />
                  );
                })}
              </>
            );
          }

          return (
            <>
              <path d={pathData} fill="none" stroke="#003366" strokeWidth="2" />
              <path d={pathData2} fill="none" stroke="#3399ff" strokeWidth="2" />

              {/* Data points */}
              {data.map((point, index) => {
                const x = padding + point.x * xScale;
                const y = svgHeight - padding - point.y * yScale;
                return (
                  <circle
                    key={`point-${index}`}
                    cx={x}
                    cy={y}
                    r="4"
                    fill="#003366"
                  />
                );
              })}

              {data2.map((point, index) => {
                const x = padding + point.x * xScale;
                const y = svgHeight - padding - point.y * yScale;
                return (
                  <circle
                    key={`point2-${index}`}
                    cx={x}
                    cy={y}
                    r="4"
                    fill="#3399ff"
                  />
                );
              })}
            </>
          );
      }
    }
  };

  // Define the legend items
  const legendItems = [
    { label: 'Series 1', color: '#003366' },
    { label: 'Series 2', color: '#3399ff' },
    { label: 'Series 3', color: '#66cc99' }
  ];

  // Render the legend based on position
  const renderLegend = () => {
    if (!showLegend) return null;

    if (legendPosition === 'right') {
      return (
        <div style={{
          position: 'absolute',
          right: '10px',
          top: '30px',
          bottom: '10px',
          width: '120px',
          backgroundColor: 'rgba(255, 255, 255, 0.8)',
          padding: '10px',
          borderRadius: '4px',
          border: '1px solid #ddd',
          display: 'flex',
          flexDirection: 'column',
          gap: '10px',
          overflowY: 'auto'
        }}>
          <div style={{ fontWeight: 'bold', marginBottom: '5px' }}>Legend (Right)</div>
          {legendItems.map((item, index) => (
            <div key={index} style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
              <div style={{ width: '12px', height: '12px', backgroundColor: item.color, borderRadius: '2px' }}></div>
              <div>{item.label}</div>
            </div>
          ))}
        </div>
      );
    }

    return (
      <div style={{
        position: 'absolute',
        bottom: '10px',
        left: '50%',
        transform: 'translateX(-50%)',
        backgroundColor: 'rgba(255, 255, 255, 0.8)',
        padding: '10px',
        borderRadius: '4px',
        border: '1px solid #ddd',
        display: 'flex',
        gap: '15px'
      }}>
        <div style={{ fontWeight: 'bold', marginRight: '5px' }}>Legend (Bottom)</div>
        {legendItems.map((item, index) => (
          <div key={index} style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
            <div style={{ width: '12px', height: '12px', backgroundColor: item.color, borderRadius: '2px' }}></div>
            <div>{item.label}</div>
          </div>
        ))}
      </div>
    );
  };

  // Adjust container style based on legend position
  const containerStyle = {
    width: '100%',
    height: '300px',
    background: '#fff',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    position: 'relative'
  };

  // Adjust SVG width and padding if legend is on the right
  const svgStyle = {
    width: showLegend && legendPosition === 'right' ? 'calc(100% - 150px)' : '100%',
    height: '100%',
    paddingRight: showLegend && legendPosition === 'right' ? '150px' : '0'
  };

  return (
    <div ref={containerRef} style={containerStyle}>
      <svg style={svgStyle} viewBox={viewBox} preserveAspectRatio="xMidYMid meet">
        {/* X and Y axes - simplified */}
        <line x1={padding} y1={svgHeight - padding} x2={svgWidth - padding} y2={svgHeight - padding} stroke="#d6e7ff" />
        <line x1={padding} y1={padding} x2={padding} y2={svgHeight - padding} stroke="#d6e7ff" />

        {/* Render the appropriate graph type */}
        {renderGraph()}
      </svg>
      {renderLegend()}
    </div>
  );
}

export default SimpleGraph;
