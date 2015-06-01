<?php
/**
 * Plugin Name: dmrshark live wordpress plugin
 * Plugin URI: https://github.com/nonoo/dmrshark-live-wordpress-plugin
 * Description: Displays a searchable, live amateur radio Hytera DMR network log table. Data is from https://github.com/nonoo/dmrshark
 * Version: 1.0
 * Author: Nonoo
 * Author URI: http://dp.nonoo.hu/
 * License: MIT
*/

include_once(dirname(__FILE__) . '/dmrshark-live-config.inc.php');

function dmrshark_live_generate() {
	$out = '<img id="dmrshark-live-loader" src="' . plugins_url('loader.gif', __FILE__) . '" />' . "\n";
	$out .= '<form id="dmrshark-live-search">' . "\n";
	$out .= '	<input type="text" id="dmrshark-live-search-string" />' . "\n";
	$out .= '	<input type="submit" id="dmrshark-live-search-button" value="' . __('Search', 'dmrshark-live') . '" />' . "\n";
	$out .= '</form>' . "\n";
	$out .= '<div id="dmrshark-live-container"></div>' . "\n";
	$out .= '<script type="text/javascript">' . "\n";
	$out .= '	var dmr_live_searchfor = "";' . "\n";
	$out .= '	$(document).ready(function () {' . "\n";
	$out .= '		$("#dmrshark-live-container").jtable({' . "\n";
	$out .= '			paging: true,' . "\n";
	$out .= '			sorting: true,' . "\n";
	$out .= '			defaultSorting: "startts desc",' . "\n";
	$out .= '			actions: {' . "\n";
	$out .= '				listAction: "' . plugins_url('dmrshark-live-getdata.php', __FILE__) . '",' . "\n";
	$out .= '			},' . "\n";
	$out .= '			fields: {' . "\n";
	$out .= '				startts: { title: "' . __('Call start', 'dmrshark-live') . '" },' . "\n";
	$out .= '				endts: { title: "' . __('Call end', 'dmrshark-live') . '", display: function (data) {' . "\n";
	$out .= '					if (data.record.endts == "00:00:00")' . "\n";
	$out .= '						return "' . __('In call', 'dmrshark-live') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.endts;' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				src: { title: "' . __('From', 'dmrshark-live') . '" },' . "\n";
	$out .= '				dst: { title: "' . __('To', 'dmrshark-live') . '" },' . "\n";
	$out .= '				repeater: { title: "' . __('Repeater', 'dmrshark-live') . '", display: function (data) {' . "\n";
	$out .= '					if (data.record.repeater == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark-live') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.repeater;' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				timeslot: { title: "' . __('TS', 'dmrshark-live') . '", width: "5%" },' . "\n";
	$out .= '				calltype: { title: "' . __('Calltype', 'dmrshark-live') . '", width: "7%", display: function (data) {' . "\n";
	$out .= '					if (data.record.calltype == 0)' . "\n";
	$out .= '						return "' . __('Priv.', 'dmrshark-live') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return "' . __('Group', 'dmrshark-live') . '";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				avgrssi: { title: "' . __('Avg. RSSI', 'dmrshark-live') . '", display: function (data) {' . "\n";
	$out .= '					if (data.record.avgrssi == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark-live') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.avgrssi;' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				currrssi: { title: "' . __('RSSI', 'dmrshark-live') . '", display: function (data) {' . "\n";
	$out .= '					if (data.record.currrssi == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark-live') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.currrssi;' . "\n";
	$out .= '				} }' . "\n";
	$out .= '			}' . "\n";
	$out .= '		});' . "\n";
	$out .= '		function dmr_live_update_showloader() {' . "\n";
	$out .= '			$("#dmrshark-live-loader").fadeIn();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function dmr_live_update_hideloader() {' . "\n";
	$out .= '			$("#dmrshark-live-loader").fadeOut();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function dmr_live_update() {' . "\n";
	$out .= '			$("#dmrshark-live-container").jtable("load", {' . "\n";
	$out .= '				searchfor: dmr_live_searchfor' . "\n";
	$out .= '			}, dmr_live_update_hideloader);' . "\n";
	$out .= '		};' . "\n";
	$out .= '		$("#dmrshark-live-search-button").click(function (e) {' . "\n";
	$out .= '			e.preventDefault();' . "\n";
	$out .= '			dmr_live_update_showloader();' . "\n";
	$out .= '			dmr_live_searchfor = $("#dmrshark-live-search-string").val();' . "\n";
	$out .= '			dmr_live_update();' . "\n";
	$out .= '		});' . "\n";
	$out .= '		setInterval(function() { dmr_live_update_showloader(); $("#dmrshark-live-container").jtable("reload", dmr_live_update_hideloader); }, 500);' . "\n";
	$out .= '		dmr_live_update();' . "\n";
	$out .= '	});' . "\n";
	$out .= '</script>' . "\n";

	return $out;
}

function dmrshark_live_filter($content) {
    $startpos = strpos($content, '<dmrshark-live');
    if ($startpos === false)
		return $content;

    for ($j=0; ($startpos = strpos($content, '<dmrshark-live', $j)) !== false;) {
		$endpos = strpos($content, '>', $startpos);
		$block = substr($content, $startpos, $endpos - $startpos + 1);

		$out = dmrshark_live_generate();

		$content = str_replace($block, $out, $content);
		$j = $endpos;
    }
    return $content;
}
load_plugin_textdomain('dmrshark-live', false, basename(dirname(__FILE__)) . '/languages');
add_filter('the_content', 'dmrshark_live_filter');
add_filter('the_content_rss', 'dmrshark_live_filter');
add_filter('the_excerpt', 'dmrshark_live_filter');
add_filter('the_excerpt_rss', 'dmrshark_live_filter');

function dmrshark_live_jscss() {
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . plugins_url('jtable-theme/jtable_basic.css', __FILE__) . '" />';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . plugins_url('dmrshark-live.css', __FILE__) . '" />';
}
add_action('wp_head', 'dmrshark_live_jscss');
?>
