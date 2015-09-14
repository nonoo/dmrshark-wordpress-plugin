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
	$out .= '					txt = data.record.srcid;' . "\n";
	$out .= '					if (data.record.srcname != null) txt += " " + data.record.srcname;' . "\n";
	$out .= '					return "<span title=\"" + txt + "\">" + data.record.src + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				dst: { title: "' . __('To', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					txt = data.record.dstid;' . "\n";
	$out .= '					if (data.record.dstname != null) txt += " " + data.record.dstname;' . "\n";
	$out .= '					return "<span title=\"" + txt + "\">" + data.record.dst + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				repeater: { title: "' . __('Repeater', 'dmrshark') . '", width: "5%", display: function (data) {' . "\n";
	$out .= '					if (data.record.repeater == "0")' . "\n";
	$out .= '						return "' . __('N/A', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return "<span title=\"" + data.record.repeaterid + "\">" + data.record.repeater + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				timeslot: { title: "' . __('TS', 'dmrshark') . '", width: "3%" },' . "\n";
	$out .= '				calltype: { title: "' . __('Calltype', 'dmrshark') . '", width: "4%", display: function (data) {' . "\n";
	$out .= '					if (data.record.calltype == 0)' . "\n";
	$out .= '						return "' . __('Priv.', 'dmrshark') . '";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return "' . __('Group', 'dmrshark') . '";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				rssi: { title: "' . __('RSSI/avg.', 'dmrshark') . '", listClass: "rssi", display: function (data) {' . "\n";
	$out .= '					if (data.record.currrssi < -130 || data.record.avgrssi < -130)' . "\n";
	$out .= '						return "Leszakadt";' . "\n";
	$out .= '					var rssi_str;' . "\n";
	$out .= '					if (data.record.currrssi == "0")' . "\n";
	$out .= '						rssi_str = "?";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						rssi_str = data.record.currrssi;' . "\n";
	$out .= '					var avgrssi_str;' . "\n";
	$out .= '					if (data.record.avgrssi == "0")' . "\n";
	$out .= '						avgrssi_str = "?";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						avgrssi_str = data.record.avgrssi;' . "\n";
	$out .= '					return "<span class=\"currrssi\">" + rssi_str + "</span><span class=\"separator\">/</span><span class=\"avgrssi\">" + avgrssi_str + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				vol: { title: "' . __('Vol./avg.', 'dmrshark') . '", listClass: "vol", display: function (data) {' . "\n";
	$out .= '					var vol_str;' . "\n";
	$out .= '					if (data.record.currrmsvol == "127")' . "\n";
	$out .= '						vol_str = "?";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						vol_str = data.record.currrmsvol;' . "\n";
	$out .= '					var avg_vol_str;' . "\n";
	$out .= '					if (data.record.avgrmsvol == "127")' . "\n";
	$out .= '						avg_vol_str = "?";' . "\n";
	$out .= '					else' . "\n";
	$out .= '						avg_vol_str = data.record.avgrmsvol;' . "\n";
	$out .= '					return "<span class=\"currvol\">" + vol_str + "</span><span class=\"separator\">/</span><span class=\"avgvol\">" + avg_vol_str + "</span>";' . "\n";
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

function dmrshark_stats_generate() {
	$out = '<img id="dmrshark-stats-loader" src="' . plugins_url('loader.gif', __FILE__) . '" />' . "\n";
	$out .= '<div id="dmrshark-stats-search-container">' . "\n";
	$out .= '	<input type="text" id="dmrshark-stats-startts" placeholder="' . __('From time', 'dmrshark') . '"/>' . "\n";
	$out .= '	<input type="text" id="dmrshark-stats-endts" placeholder="' . __('To time', 'dmrshark') . '"/>' . "\n";
	$out .= '	<form id="dmrshark-stats-search">' . "\n";
	$out .= '		<input type="text" id="dmrshark-stats-search-string" />' . "\n";
	$out .= '		<input type="submit" id="dmrshark-stats-search-button" value="' . __('Search', 'dmrshark') . '" />' . "\n";
	$out .= '	</form>' . "\n";
	$out .= '</div>' . "\n";
	$out .= '<div id="dmrshark-stats-search-helper-container">' . "\n";
	$out .= '	<input type="button" id="dmrshark-stats-searchhelper-all" value="' . __('All', 'dmrshark') . '" onclick="javascript:dmrshark_stats_resetts();" />' . "\n";
	$out .= '	<input type="button" id="dmrshark-stats-searchhelper-today" value="' . __('Today', 'dmrshark') . '" onclick="javascript:dmrshark_stats_helper_today();" />' . "\n";
	$out .= '	<input type="button" id="dmrshark-stats-searchhelper-yesterday" value="' . __('Yesterday', 'dmrshark') . '" onclick="javascript:dmrshark_stats_helper_yesterday();" />' . "\n";
	$out .= '</div>' . "\n";
	$out .= '<div id="dmrshark-stats-container"></div>' . "\n";
	$out .= '<script type="text/javascript">' . "\n";
	$out .= '	var dmrshark_stats_searchfor = "";' . "\n";
	$out .= '	function dmrshark_stats_helper_today() {' . "\n";
	$out .= '		$("#dmrshark-stats-startts").datepicker("setDate", new Date());' . "\n";
	$out .= '		$("#dmrshark-stats-endts").datepicker("setDate", new Date(new Date().getTime() + 24 * 60 * 60 * 1000));' . "\n";
	$out .= '		dmrshark_stats_update();' . "\n";
	$out .= '	}' . "\n";
	$out .= '	function dmrshark_stats_helper_yesterday() {' . "\n";
	$out .= '		$("#dmrshark-stats-startts").datepicker("setDate", new Date(new Date().getTime() - 24 * 60 * 60 * 1000));' . "\n";
	$out .= '		$("#dmrshark-stats-endts").datepicker("setDate", new Date());' . "\n";
	$out .= '		dmrshark_stats_update();' . "\n";
	$out .= '	}' . "\n";
	$out .= '	function dmrshark_stats_resetts() {' . "\n";
	$out .= '		$("#dmrshark-stats-startts").datepicker("setDate", null);' . "\n";
	$out .= '		$("#dmrshark-stats-endts").datepicker("setDate", null);' . "\n";
	$out .= '		dmrshark_stats_update();' . "\n";
	$out .= '	}' . "\n";
	$out .= '	function dmrshark_stats_update() {' . "\n";
	$out .= '		$("#dmrshark-stats-container").jtable("load", {' . "\n";
	$out .= '			searchfor: dmrshark_stats_searchfor,' . "\n";
	$out .= '			startts: $("#dmrshark-stats-startts").datepicker("getDate") / 1000,' . "\n";
	$out .= '			endts: $("#dmrshark-stats-endts").datepicker("getDate") / 1000,' . "\n";
	$out .= '		}, dmrshark_stats_update_hideloader);' . "\n";
	$out .= '	};' . "\n";
	$out .= '	function dmrshark_stats_update_showloader() {' . "\n";
	$out .= '		$("#dmrshark-stats-loader").fadeIn();' . "\n";
	$out .= '	}' . "\n";
	$out .= '	function dmrshark_stats_update_hideloader() {' . "\n";
	$out .= '		$("#dmrshark-stats-loader").fadeOut();' . "\n";
	$out .= '	}' . "\n";
	$out .= '	$("#dmrshark-stats-search-button").click(function (e) {' . "\n";
	$out .= '		e.preventDefault();' . "\n";
	$out .= '		dmrshark_stats_update_showloader();' . "\n";
	$out .= '		dmrshark_stats_searchfor = $("#dmrshark-stats-search-string").val();' . "\n";
	$out .= '		dmrshark_stats_update();' . "\n";
	$out .= '	});' . "\n";
	$out .= '	$(document).ready(function () {' . "\n";
	$out .= '		$("#dmrshark-stats-startts").datepicker({ firstDay: 1, dateFormat: "yy/mm/dd", defaultDate: "now", onClose: function() { dmrshark_stats_update(); } });' . "\n";
	$out .= '		$("#dmrshark-stats-endts").datepicker({ firstDay: 1, dateFormat: "yy/mm/dd", defaultDate: "+1d", onClose: function() { dmrshark_stats_update(); } });' . "\n";
	$out .= '		$("#dmrshark-stats-container").jtable({' . "\n";
	$out .= '			paging: true,' . "\n";
	$out .= '			sorting: true,' . "\n";
	$out .= '			defaultSorting: "talktime desc",' . "\n";
	$out .= '			loadingAnimationDelay: 1000,' . "\n";
	$out .= '			actions: {' . "\n";
	$out .= '				listAction: "' . plugins_url('dmrshark-stats-getdata.php', __FILE__) . '"' . "\n";
	$out .= '			},' . "\n";
	$out .= '			fields: {' . "\n";
	$out .= '				callsign: { title: "' . __('Callsign', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					if (data.record.callsign == null)' . "\n";
	$out .= '						return data.record.id;' . "\n";
	$out .= '					else' . "\n";
	$out .= '						return "<span title=\"" + data.record.id + "\">" + data.record.callsign + " " + data.record.name + "</span>";' . "\n";
	$out .= '				} },' . "\n";
	$out .= '				talktime: { title: "' . __('Talktime (min.)', 'dmrshark') . '", display: function (data) {' . "\n";
	$out .= '					return (data.record.talktime/60).toFixed(1);' . "\n";
	$out .= '				} }' . "\n";
	$out .= '			}' . "\n";
	$out .= '		});' . "\n";
	$out .= '		setInterval(function() { dmrshark_stats_update_showloader(); $("#dmrshark-stats-container").jtable("reload", dmrshark_stats_update_hideloader); }, 60000);' . "\n";
	$out .= '		dmrshark_stats_helper_today();' . "\n";
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

    for ($j=0; ($startpos = strpos($content, '<dmrshark-stats', $j)) !== false;) {
		$endpos = strpos($content, '>', $startpos);
		$block = substr($content, $startpos, $endpos - $startpos + 1);

		$out = dmrshark_stats_generate();

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
