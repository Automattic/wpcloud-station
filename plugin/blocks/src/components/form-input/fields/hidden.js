function Hidden( { attributes } ) {
	const { name } = attributes;
	return (
		<span className="wpcloud-block-form-input--hidden">{ `{ ${ name } } ` }</span>
	);
}

export default Hidden;
