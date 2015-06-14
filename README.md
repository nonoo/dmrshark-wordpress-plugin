dmrshark-wordpress-plugin
=========================

Wordpress plugin which displays searchable, live amateur radio Hytera DMR network log, repeater status and stats tables.
Data is from http://github.com/nonoo/dmrshark
You'll need [ha5kdr-dmr-db](https://github.com/nonoo/ha5kdr-dmr-db) Wordpress plugin's actual database too.

#### Usage

- Edit and rename **dmrshark-config-example.inc.php** to **dmrshark-config.inc.php**,
**dmrshark-example.css** to **dmrshark.css**.
- Enable the plugin on the Wordpress plugin configuration page.
- Copy **loader-example.gif** to **loader.gif**.

To calculate the stats, run **dmrshark-calcstats.php** from crontab periodically (for ex. every minute).
Don't run it under the www folder, copy it to another directory.

#### Live log

To show the live log, insert this to a Wordpress page or post:

```
<dmrshark-log />
```

To show the live repeater log, insert this to a Wordpress page or post:

```
<dmrshark-repeaters />
```

You can see a working example [here](http://ham-dmr.hu/elo-statusz/).

#### Stats

To show the stats table, insert this to a Wordpress page or post:

```
<dmrshark-stats />
```

You can see a working example [here](http://ham-dmr.hu/statisztika/).
