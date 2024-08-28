const mutableOptions = window.wpcloud?.siteMutableOptions || {};

export function inputTemplate({ name, label, options, hint, type, placeholder } ) {

	let hintTemplate = null;
	if ( hint ) {
		hintTemplate = [
			'wpcloud/expanding-section',
			{
				metadata: {
					name: `${ name } hint`,
				},
				clickToToggle: true,
				hideHeader: false,
			},
			[
				[
					'wpcloud/expanding-header',
					{},
					[
						[ 'core/paragraph', { content: label } ],
						[ 'wpcloud/icon', { icon: 'info' } ],
					],
				],
				[
					'wpcloud/expanding-content',
					{},
					[
						[
							'core/paragraph',
							{
								content: hint,
							},
						],
					],
				],
			],
		];
	}
	const inputAttributes = {
		type,
		name,
		label: '',
		meta: { name: label },
		placeholder,
	};
	const input = [ 'wpcloud/form-input' ];

	if ( ! hintTemplate ) {
		inputAttributes.label = label;
	}

	if ( type === 'select' ) {
		const optionData = [];

		if ( Array.isArray( options ) ) {
			options.forEach( ( option ) => {
				optionData.push( { value: option, label: option } );
			} );
		} else {
			for ( const [ key, value ] of Object.entries( options ) ) {
				optionData.push( { value: key, label: value } );
			}
		}
		inputAttributes.options = optionData;
	}

	input.push( inputAttributes );
	if ( hintTemplate ) {
		input.push( [ hintTemplate ] );
	}

	return input;
}