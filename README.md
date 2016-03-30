# WordPress Hero Manager
Adds a custom post type and custom meta box (requires Advanced Custom Fields Pro plugin) for managing post/page heroes and extends the manageability to indexes and other "pages" in WordPress.

## Installation
1. Download [zip](https://github.com/tammyhart/wordpress-hero-manager/archive/master.zip) and drop files into theme or clone into theme.
2. Add `include( 'functions/hero.php' );` to theme's functions.php.
3. Add `get_template_part( 'partials/hero' );` to header.php or other global space where it will be added to every page.
4. Add `thd_hero_contrast()` to `body_class()` like so: `<body <?php body_class( thd_hero_contrast() ); ?>>`
5. Use included Sass styles or create your own.

## Usage
The ACF JSON files will add a meta box to posts, pages, and heroes post types. Afte you sync the field group, you can add support for your own custom post types and taxonomies. To support matching heroes to custom taxonomies and post type archives, code in functions/hero.php is commented, just follow the previous pattern.

To use heroes for non post type pages, add a new Hero post and assign the proper relationship. "Pages" that are currently supported:
- Posts Page (main blog index)
- Search Results
- 404
- Optional default fallback
- Any archive default
- Any category default
- Any post tag default