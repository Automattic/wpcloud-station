
// Placeholder colors pulled from github.com/automattic/color-studio/dist/colors.meta.json as of v2.6.0.
// Background colors are 40's, foreground colors are 60's
const backgroundColors = ['#787c82', '#1689db', '#b35eb1', '#e34c84', '#e65054', '#d67709', '#c08c00', '#00a32a', '#009e73', '#1490c7', '#187aa2', '#4678eb', '#9a69c7', '#069e08'];
const foregroundColors = ['#50575e', '#055d9c', '#7c3982', '#ab235a', '#b32d2e', '#8a4d00', '#7d5600', '#007017', '#007053', '#036085', '#004e6e', '#1d4fc4', '#674399', '#007117'];

/**
 * Get the placeholder style for a given id
 * @param {*} id
 * @returns
 */
export function getPlaceholderStyle(id) {
	const index = id % ( backgroundColors.length - 1 );

	return {
		backgroundColor: backgroundColors[index],
		color: foregroundColors[index],
	}
}

export function Placeholder({ postId, name = 'Site Name' }) {
	if ( !postId ) {
		return null;
	}
	const style = getPlaceholderStyle(postId);
	return (
		<div className="wpcloud-site-card--placeholder" style={style} >
			<p style={style}>{name[0].toUpperCase()}</p>
		</div>
	)
}