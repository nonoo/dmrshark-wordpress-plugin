CREATE TABLE IF NOT EXISTS `dmrshark-live` (
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
