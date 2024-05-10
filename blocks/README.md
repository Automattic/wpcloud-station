# WP Cloud Blocks

### Development

To add a new block:

```
cd ./blocks/src
npx @wordpress/create-block  --no-plugin --namespace=wpcloud {block name}
```

add `--variant dynamic` to add a `render.php` file for server side rendering.

When the block is registered, `blocks-init.php` will include the `{block name}/index.php` if it exists. This can be used to add any hooks the block might need.

When working on a block run
```
npm start
```
from the plugin root directory.
