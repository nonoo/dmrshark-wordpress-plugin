<?php
/**
 * Plugin Name: dmrshark wordpress plugin
 * Plugin URI: https://github.com/nonoo/dmrshark-wordpress-plugin
 * Description: Displays a searchable, live amateur radio Hytera DMR network log table. Data is from https://github.com/nonoo/dmrshark
 * Version: 1.0
 * Author: Nonoo
 * Author URI: http://dp.nonoo.hu/
 * License: MIT
*/

include_once(dirname(__FILE__) . '/dmrshark-config.inc.php');

function dmrshark_log_generate() {
	$out = '<img id="dmrshark-log-loader" src="' . plugins_url('loader.gif', __FILE__) . '" />' . "\n";
	$out .= '<form id="dmrshark-log-search">' . "\n";
	$out .= '	<input type="text" id="dmrshark-log-search-string" />' . "\n";
	$out .= '	<input type="submit" id="dmrshark-log-search-button" value="' . __('Search', 'dmrshark') . '" />' . "\n";
	$out .= '</form>' . "\n";
	$out .= '<div id="dmrshark-log-container"></div>' . "\n";
	$out .= '<script type="text/javascript">' . "\n";
	$out .= '	var dmrshark_log_searchfor = "";' . "\n";
	$out .= '	$(document).ready(function () {' . "\n";
	$out .= '		$("#dmrshark-log-container").jtable({' . "\n";
	$out .= '			paging: true,' . "\n";
	$out .= '			sorting: true,' . "\n";
	$out .= '			defaultSorting: "startts desc",' . "\n";
	$out .= '			loadingAnimationDelay: 1000,' . "\n";
	$out .= '			actions: {' . "\n";
	$out .= '				listAction: "' . plugins_url('dmrshark-log-getdata.php', __FILE__) . '",' . "\n";
	$out .= '			},' . "\n";
	$out .= '			fields: {' . "\n";
	$out .= '				startts: { title: "' . __('Call start', 'dmrshark') . '", width: "8%" },' . "\n";
	$out .= '				endts: { title: "' . __('Call end', 'dmrshark') . '", width: "8%", display: function (data) {' . "\n";
	$out .= '					if (data.record.endts == "00:00:00")' . "\n";
	$out .= '						return "' . __('In call', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.endts;' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				src: { title: "' . __('From', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					txt = data.record.src;' . "\n";
	$out .= '					if (data.record.srcname != null) txt += " " + data.record.srcname;' . "\n";
	$out .= '					return "<span title=\"" + data.record.srcid + "\">" + txt + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				dst: { title: "' . __('To', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					txt = data.record.dst;' . "\n";
	$out .= '					if (data.record.dstname != null) txt += " " + data.record.dstname;' . "\n";
	$out .= '					return "<span title=\"" + data.record.dstid + "\">" + txt + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				repeater: { title: "' . __('Repeater', 'dmrshark') . '", width: "5%", display: function (data) {' . "\n";
	$out .= '					if (data.record.repeater == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return "<span title=\"" + data.record.repeaterid + "\">" + data.record.repeater + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				timeslot: { title: "' . __('TS', 'dmrshark') . '", width: "4%" },' . "\n";
	$out .= '				calltype: { title: "' . __('Calltype', 'dmrshark') . '", width: "6%", display: function (data) {' . "\n";
	$out .= '					if (data.record.calltype == 0)' . "\n";
	$out .= '						return "' . __('Priv.', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return "' . __('Group', 'dmrshark') . '";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				avgrssi: { title: "' . __('Avg. RSSI', 'dmrshark') . '", width: "8%", display: function (data) {' . "\n";
	$out .= '					if (data.record.avgrssi == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.avgrssi;' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				currrssi: { title: "' . __('RSSI', 'dmrshark') . '", width: "6%", display: function (data) {' . "\n";
	$out .= '					if (data.record.currrssi == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return data.record.currrssi;' . "\n";
	$out .= '				} }' . "\n";
	$out .= '			}' . "\n";
	$out .= '		});' . "\n";
	$out .= '		function dmrshark_log_update_showloader() {' . "\n";
	$out .= '			$("#dmrshark-log-loader").fadeIn();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function dmrshark_log_update_hideloader() {' . "\n";
	$out .= '			$("#dmrshark-log-loader").fadeOut();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function dmrshark_log_update() {' . "\n";
	$out .= '			$("#dmrshark-log-container").jtable("load", {' . "\n";
	$out .= '				searchfor: dmrshark_log_searchfor' . "\n";
	$out .= '			}, dmrshark_log_update_hideloader);' . "\n";
	$out .= '		};' . "\n";
	$out .= '		$("#dmrshark-log-search-button").click(function (e) {' . "\n";
	$out .= '			e.preventDefault();' . "\n";
	$out .= '			dmrshark_log_update_showloader();' . "\n";
	$out .= '			dmrshark_log_searchfor = $("#dmrshark-log-search-string").val();' . "\n";
	$out .= '			dmrshark_log_update();' . "\n";
	$out .= '		});' . "\n";
	$out .= '		setInterval(function() { dmrshark_log_update_showloader(); $("#dmrshark-log-container").jtable("reload", dmrshark_log_update_hideloader); }, 500);' . "\n";
	$out .= '		dmrshark_log_update();' . "\n";
	$out .= '	});' . "\n";
	$out .= '</script>' . "\n";

	return $out;
}

function dmrshark_repeaters_generate() {
	$out = '<img id="dmrshark-repeaters-loader" src="' . plugins_url('loader.gif', __FILE__) . '" />' . "\n";
	$out .= '<form id="dmrshark-repeaters-search">' . "\n";
	$out .= '	<input type="text" id="dmrshark-repeaters-search-string" />' . "\n";
	$out .= '	<input type="submit" id="dmrshark-repeaters-search-button" value="' . __('Search', 'dmrshark') . '" />' . "\n";
	$out .= '</form>' . "\n";
	$out .= '<div id="dmrshark-repeaters-container"></div>' . "\n";
	$out .= '<script type="text/javascript">' . "\n";
	$out .= '	var dmrshark_repeaters_searchfor = "";' . "\n";
	$out .= '	$(document).ready(function () {' . "\n";
	$out .= '		$("#dmrshark-repeaters-container").jtable({' . "\n";
	$out .= '			paging: true,' . "\n";
	$out .= '			sorting: true,' . "\n";
	$out .= '			defaultSorting: "lastactive desc",' . "\n";
	$out .= '			loadingAnimationDelay: 1000,' . "\n";
	$out .= '			actions: {' . "\n";
	$out .= '				listAction: "' . plugins_url('dmrshark-repeaters-getdata.php', __FILE__) . '",' . "\n";
	$out .= '			},' . "\n";
	$out .= '			fields: {' . "\n";
	$out .= '				callsign: { title: "' . __('Callsign', 'dmrshark') . '" },' . "\n";
	$out .= '				id: { title: "' . __('ID', 'dmrshark') . '" },' . "\n";
	$out .= '				type: { title: "' . __('Type', 'dmrshark') . '" },' . "\n";
	$out .= '				fwversion: { title: "' . __('FW ver.', 'dmrshark') . '" },' . "\n";
	$out .= '				dlfreq: { title: "' . __('DL freq.', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					return (data.record.dlfreq/1000000).toFixed(3);' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				ulfreq: { title: "' . __('UL freq.', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					return (data.record.ulfreq/1000000).toFixed(3);' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				lastactive: { title: "' . __('Last act.', 'dmrshark') . '" }' . "\n";
	$out .= '				}' . "\n";
	$out .= '			}' . "\n";
	$out .= '		);' . "\n";
	$out .= '		function dmrshark_repeaters_update_showloader() {' . "\n";
	$out .= '			$("#dmrshark-repeaters-loader").fadeIn();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function dmrshark_repeaters_update_hideloader() {' . "\n";
	$out .= '			$("#dmrshark-repeaters-loader").fadeOut();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function dmrshark_repeaters_update() {' . "\n";
	$out .= '			$("#dmrshark-repeaters-container").jtable("load", {' . "\n";
	$out .= '				searchfor: dmrshark_repeaters_searchfor' . "\n";
	$out .= '			}, dmrshark_repeaters_update_hideloader);' . "\n";
	$out .= '		};' . "\n";
	$out .= '		$("#dmrshark-repeaters-search-button").click(function (e) {' . "\n";
	$out .= '			e.preventDefault();' . "\n";
	$out .= '			dmrshark_repeaters_update_showloader();' . "\n";
	$out .= '			dmrshark_repeaters_searchfor = $("#dmrshark-repeaters-search-string").val();' . "\n";
	$out .= '			dmrshark_repeaters_update();' . "\n";
	$out .= '		});' . "\n";
	$out .= '		setInterval(function() { dmrshark_repeaters_update_showloader(); $("#dmrshark-repeaters-container").jtable("reload", dmrshark_repeaters_update_hideloader); }, 6000);' . "\n";
	$out .= '		dmrshark_repeaters_update();' . "\n";
	$out .= '	});' . "\n";
	$out .= '</script>' . "\n";

	return $out;
}

function dmrshark_filter($content) {
    $startpos = strpos($content, '<dmrshark-');
    if ($startpos === false)
		return $content;

    for ($j=0; ($startpos = strpos($content, '<dmrshark-log', $j)) !== false;) {
		$endpos = strpos($content, '>', $startpos);
		$block = substr($content, $startpos, $endpos - $startpos + 1);

		$out = dmrshark_log_generate();

		$content = str_replace($block, $out, $content);
		$j = $endpos;
    }

    for ($j=0; ($startpos = strpos($content, '<dmrshark-repeaters', $j)) !== false;) {
		$endpos = strpos($content, '>', $startpos);
		$block = substr($content, $startpos, $endpos - $startpos + 1);

		$out = dmrshark_repeaters_generate();

		$content = str_replace($block, $out, $content);
		$j = $endpos;
    }
    return $content;
}
load_plugin_textdomain('dmrshark', false, basename(dirname(__FILE__)) . '/languages');
add_filter('the_content', 'dmrshark_filter');
add_filter('the_content_rss', 'dmrshark_filter');
add_filter('the_excerpt', 'dmrshark_filter');
add_filter('the_excerpt_rss', 'dmrshark_filter');

function dmrshark_jscss() {
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . plugins_url('jtable-theme/jtable_basic.css', __FILE__) . '" />';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . plugins_url('dmrshark.css', __FILE__) . '" />';
}
add_action('wp_head', 'dmrshark_jscss');
?>
