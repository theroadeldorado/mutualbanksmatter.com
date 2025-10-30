# Fire WP Starter Theme

## Guides

- [ ] Install the WP CLI - https://wp-cli.org/#installing
- [ ] Clone the WordPress project locally. Eg. `git clone [git@github.com](mailto:git@github.com):skycatchfire/iontra.com.git`
- [ ] Create a new Host in MAMP PRO pointing to the cloned site on your local machine
  - [ ] Select Hosts in the sidebar and click the + button at the bottom.
  - [ ] Select `Empty` and hit Continue
  - [ ] Name your host. Eg. [`iontra.fire`](http://iontra.fire) and choose the document root. This is where you cloned the project into.
  - [ ] Generate certificate for https access should be enabled
  - [ ] Click Create Host
  - [ ] Click the Databases tab and create a database for the new Host
  - [ ] Click the Save button in the bottom right corner
  - [ ] Make sure Apache and MySQL are both running
- [ ] Export a copy of the production database and files from WP Engine
  - [ ] Login to WP Engine, credentials should be in 1Password for the project
- [ ] In MAMP Pro, import the production database into the local database you created using Sequel Ace. It’s a button under the list of databases. Clicking that will open up Sequel Ace and you can import your database.
- [ ] Move the `wp-content/uploads` from the export into `wp-content/uploads` for your local project.
- [ ] Move the `.htaccess` file from the export into the root of your local project
- [ ] Open project in VS Code
- [ ] Run the following commands in your terminal. You’ll need to update `--dbname`. Eg `--dbname=iontra`

```bash
wp core download

wp core config --dbhost=localhost --dbuser=root --dbpass=root --dbprefix=wp_ --dbname=SITENAME
```

- [ ] Add the following code to your `wp-config.php` file. Update `WP_HOME` and `WP_SITEURL` with the host name you set up in MAMP PRO.

```php
define( 'WP_HOME', 'https://SITENAME.fire' );
define( 'WP_SITEURL', 'https://SITENAME.fire' );

define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG', true );

// Used to prevent error emails from sending to site admin when working locally
define( 'WP_LOCAL_DEV', true );
```

- [ ] In your terminal, CD into the Fire Theme directory and run the following commands in your terminal to spin up a local server for your front-end.

```bash
npm install
npm run dev
```

## Related Resources

MAMP PRO - [https://www.mamp.info/en/mamp/mac](https://www.mamp.info/en/mamp/mac/)

Setting up XDebug in VSCode - https://brent.craft.me/R818HxRrX5R87C
