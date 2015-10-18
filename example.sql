CREATE TABLE IF NOT EXISTS `dmrshark-log` (
  `repeaterid` int(11) NOT NULL,
  `srcid` int(11) NOT NULL,
  `timeslot` tinyint(4) NOT NULL,
  `dstid` int(11) NOT NULL,
  `calltype` tinyint(4) NOT NULL,
  `startts` datetime NOT NULL,
  `endts` datetime NOT NULL,
  `currrssi` smallint(6) NOT NULL DEFAULT '0',
  `avgrssi` smallint(6) NOT NULL DEFAULT '0',
  `currrmsvol` tinyint(4) NOT NULL DEFAULT '127',
  `avgrmsvol` tinyint(4) NOT NULL DEFAULT '127',
  `datatype` enum('unknown','normal sms','motorola tms sms') NOT NULL,
  `datadecoded` varchar(1500) NOT NULL,
  PRIMARY KEY (`srcid`,`startts`,`repeaterid`,`timeslot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

CREATE TABLE IF NOT EXISTS `dmrshark-repeaters` (
  `callsign` varchar(25) NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `type` varchar(25) NOT NULL,
  `fwversion` varchar(25) NOT NULL,
  `dlfreq` int(10) unsigned NOT NULL,
  `ulfreq` int(10) unsigned NOT NULL,
  `psuvoltage` float NOT NULL,
  `patemperature` float NOT NULL,
  `vswr` float NOT NULL,
  `txfwdpower` float NOT NULL,
  `txrefpower` float NOT NULL,
  `lastactive` datetime NOT NULL,
  `lastactive` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `callsign` (`callsign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dmrshark-msg-queue` (
  `index` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `srcid` int(11) NOT NULL,
  `dstid` int(11) NOT NULL,
  `state` enum('waiting','processing','success','failure') NOT NULL DEFAULT 'waiting',
  `msg` varchar(250) NOT NULL,
  `addedat` datetime NOT NULL,
  PRIMARY KEY (`index`),
  UNIQUE KEY `secondary` (`srcid`,`dstid`,`msg`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;

CREATE TABLE IF NOT EXISTS `dmrshark-emails-out` (
  `dstemail` varchar(50) NOT NULL,
  `srcid` int(11) NOT NULL,
  `state` enum('waiting','success') NOT NULL DEFAULT 'waiting',
  `msg` varchar(250) NOT NULL,
  `addedat` datetime NOT NULL,
  PRIMARY KEY (`dstemail`,`srcid`,`addedat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
