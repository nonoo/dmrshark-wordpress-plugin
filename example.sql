CREATE TABLE IF NOT EXISTS `dmrshark-log` (
  `repeaterid` int(11) NOT NULL,
  `srcid` int(11) NOT NULL,
  `timeslot` tinyint(4) NOT NULL,
  `dstid` int(11) NOT NULL,
  `calltype` tinyint(4) NOT NULL,
  `startts` datetime NOT NULL,
  `endts` datetime NOT NULL,
  `currrssi` smallint(6) NOT NULL,
  `avgrssi` double NOT NULL,
  PRIMARY KEY (`srcid`,`startts`,`repeaterid`,`timeslot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dmrshark-repeaters` (
  `callsign` varchar(25) NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `type` varchar(25) NOT NULL,
  `fwversion` varchar(25) NOT NULL,
  `dlfreq` int(10) unsigned NOT NULL,
  `ulfreq` int(10) unsigned NOT NULL,
  `lastactive` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `callsign` (`callsign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
