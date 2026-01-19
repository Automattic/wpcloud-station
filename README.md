> ### ⚠️ Currently, Station is not recommended for production usage. It is an experimental example dashboard to demonstrate how to interact with the WP Cloud API. Some WP Cloud features are not be available or fully functional within Station.

# WP Cloud Station

WP Cloud Station is a client plugin to manage WP Cloud sites.

## Getting Started

WP Cloud Station plugin and theme can be installed on any WordPress site.

Our plugin provides a custom site type for managing your WP Cloud sites as well as many hooks for integrating with your own site.

Our theme is block-based and easy to configure and edit.

### Plugin

1. Upload `wpcloud-station` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to `/wp-admin/admin.php?page=wpcloud_admin_settings`.
4. Save your WP Cloud Client Name and API Key, as well as a default primary Domain.
5. Make note of your IP Address at the bottom and contact your WP Cloud representative to add the IP's.

### Theme

If you would like to use our block-based theme:

1. Upload `wpcloud-station` to the `/wp-content/themes/` directory.
2. Activate the theme through the 'Themes' menu in WordPress.
3. Navigate to Site Editor to alter.

## Reporting Issues and Feature Requests

Issues and feature requests can be submitted in [GitHub Issues](https://github.com/Automattic/wpcloud-station/issues).

## Contribute

We welcome contributions from the community. If you would like to contribute, please see [Contributing](https://github.com/Automattic/wpcloud-station/blob/trunk/CONTRIBUTING.md).

[Code of Conduct](https://github.com/Automattic/wpcloud-station/blob/trunk/CODE-OF-CONDUCT.md)

### Local Setup

#### Docker
- Copy the `docker-compose.yml-example` file to `docker-compose.yaml`
- Update the environment variable `WP_CLOUD_API_KEY` to your WP Cloud API key.
- Run `docker compose up`

#### wp-env
- run `wp-env start`
- Note: you will need to add your api key to the WP Cloud settings page

## Block CSS

The themes include CSS files that are processed and added inline to the `theme.json` under the `css` key for each respective block.

### Building and Watching Theme CSS

To build the CSS for themes, use the following npm commands:

- `npm run build:theme-css:theme` - Build CSS for the main theme
- `npm run build:theme-css:theme-pico` - Build CSS for the theme-pico theme
- `npm run build:theme-css` - Build CSS for all themes

For development, you can use the watch commands to automatically rebuild CSS when files change:

- `npm run watch:theme-css:theme` - Watch and build CSS for the main theme
- `npm run watch:theme-css:theme-pico` - Watch and build CSS for the theme-pico theme
- `npm run watch:theme-css` - Watch and build CSS for all themes

The build script accepts the following options:
- `-V, --verbose` - Show verbose output including processed CSS
- `-h, --help` - Show help information

You can also filter which blocks to process by specifying block names:
```
npm run build:theme-css:theme core/query wpcloud/button
```

### CSS Source Files

CSS source files are located in each theme's `assets/blocks/src` directory. The build script will only process styles for blocks that are defined in the theme's `theme.json` `blocks` field.

### CSS Limitations and Processing

The `css` field in `theme.json` is limited in how it can parse CSS:
- It can expand `&` to the core block class but that's about it
- It cannot handle nested CSS nor multiple selectors per rule (e.g., `div, span { color: red }`)
- Since JSON is not multiline, the style needs to be rendered to a single line string

The build script uses `postcss` to handle:
- CSS nesting (sass style)
- Expanding selectors so that each rule has only one selector
- Setting up new lines and tabs so the styles render cleanly in the site editor CSS panel

#### On `&` expansion
Core will expand `&` to the top level block class. The build script will expand nested `&` to target the parent block.

### Example

Given the block `wpcloud/button` in `assets/blocks/src/wpcloud-button.css`:

```css
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

```css
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

And end up in `theme.json`:

```json
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

Finally, in the browser, the `&` will be expanded to the core block class:

```css
.wp-block-wpcloud-button {
	color: blue
}

.wp-block-wpcloud-button.is-primary {
	text-transform: uppercase;
}
...
```

## Frequently Asked Questions

* Can I use WP Cloud Station plugin on any site?

  Yes, WP Cloud Station plugin can be installed on any existing WordPress site.

* How much does it cost?

  WP Cloud Station plugin and theme are free. You will need a client set up and an agreement with Automattic to create new sites.

  Please contact us for more information.

* Can I integrate WP Cloud with my own site?

  Yes! We provide a custom post type `wpcloud_site` to manage your sites, but we also integrate with native WordPress architecture include REST API, as well as providing numerous hooks to integrate with actions and callbacks.
