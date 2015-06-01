dmrshark-live-wordpress-plugin
==============================

Wordpress plugin which displays a searchable, live amateur radio Hytera DMR network log and repeater status table.
Data is from http://github.com/nonoo/dmrshark

#### Usage

- Edit and rename **dmrshark-live-config-example.inc.php** to **dmrshark-live-config.inc.php**,
**dmrshark-live-example.css** to **dmrshark-live.css**.
- Enable the plugin on the Wordpress plugin configuration page.
- Copy **loader-example.gif** to **loader.gif**.

To show the live log, insert this to a Wordpress page or post:

```
<dmrshark-live />
```

You can see a working example [here](http://ham-dmr.hu/elo-statusz/).
