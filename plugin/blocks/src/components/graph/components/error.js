/**
 * WordPress dependencies
 */
import * as icons from '@wordpress/icons';
import { Icon } from '@wordpress/components';

export default () => {
	return (
		<>
			<div style={{ width: "100%", height: "100%", backgroundColor: "white", opacity: "66%" }} />
			<div style={{ position: "absolute", top: "50%", left: "50%", transform: "translate(-50%, -50%)" }}>
				<Icon icon={icons['warning']}  style={{
  					height: 'calc(4px * 20)',
    				width: 'calc(4px * 20)'
				}}
				/>
			</div>
		</>
	);
}