# Block CSS
These source css files are intended to be added inline to the `theme.json` under the `css` key in each respective block.

To build the styles run:

`npm run build:css`

The script accepts `-V --verbose` flag to output the generated css. This is helpful for debugging the selectors.

While developing use:

`npm run watch:css`

to build on change to the source css.

The build script will only pick up styles that are defined in the `theme.json` `blocks` field.
The script also accepts a list of space delineated prefixes to filter blocks it look for source styles.


### Note

The `css` field in `theme.json` is limited on how it can parse css. It can expand `&` to the core block class but that's about it. It can not handle nested css nor multiple selectors per rule (ex: `div, span { color: red }`)

Also since JSON is not multiline, the style needs to be rendered to a single line string.

The build scripts uses `postcss` to handle:
- css nesting (sass style)
- Expanding selectors so that each rule has only one selector
- Setting up new lines and tabs so the styles render cleanly in the site editor css panel.

#### On `&` expansion
As noted, core will expand `&` to the top level block class. The build script, however, will expand the nested `&` to target the parent block.

## Example

Given the block `wpcloud/button`

in `assets/block/src/wpcloud-button.css`

```
& {
	color: blue;
	&.is-primary {
		color: red;
		.inner-text {
			text-transform: lowercase;
		}
	}
	.inner-text, p {
		text-transform: uppercase;
	}
}
```

Will expand to:

```
& {
	color: blue;
}

&.is-primary {
	color: red;
}

&.is-primary .inner-text {
	text-transform: lowercase;
}

& .inner-text {
	text-transform: uppercase;
}

& p {
	text-transform: uppercase;
}
```

and end up in `theme.json`:

```
{ ...
 "blocks": {
	....
	"wpcloud/button": {
		...
		"css" : "& {\n\tcolor:blue\n}...."
	}
 }
}
```

The finally in the browser the `&` will be expanded to the core block class:

```
.wp-block-wpcloud-button {
	color: blue
}

.wp-block-wpcloud-button.is-primary {
	text-transform: uppercase;
}
...
```