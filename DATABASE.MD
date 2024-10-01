Ok this is just a mysqldump but it is the latest version (01 Oct 204)




--
-- Table structure for table `comp`
--

DROP TABLE IF EXISTS `comp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comp` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `compLetters` char(3) DEFAULT NULL,
  `compName` char(255) DEFAULT NULL,
  `hish` char(255) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `isChild` int(1) DEFAULT NULL,
  `isParent` int(1) DEFAULT NULL,
  `parentComp` char(3) DEFAULT NULL,
  `contact` char(255) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `streamURL` char(255) DEFAULT NULL,
  `sheet` tinyint(1) DEFAULT NULL,
  `lights` tinyint(1) DEFAULT NULL,
  `seshs` char(255) DEFAULT NULL,
  `fed` char(3) DEFAULT NULL,
  `token` char(64) DEFAULT NULL,
  `tokenexp` int(11) DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `compLetters` (`compLetters`)
) ENGINE=InnoDB AUTO_INCREMENT=859 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comps`
--

DROP TABLE IF EXISTS `comps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comps` (
  `compLetters` char(3) DEFAULT NULL,
  `compName` char(255) DEFAULT NULL,
  `contact` char(255) DEFAULT NULL,
  `compID` int(4) NOT NULL AUTO_INCREMENT,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `leftLight` tinyint(1) NOT NULL,
  `centreLight` tinyint(1) NOT NULL,
  `rightLight` tinyint(1) NOT NULL,
  `timeTo` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeTwo` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lifterName` char(255) DEFAULT NULL,
  `currentAttempt` double(5,2) DEFAULT NULL,
  `total` double(6,2) DEFAULT NULL,
  `compStatus` tinyint(4) DEFAULT NULL,
  `lifterTeam` varchar(255) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lifterBW` double(5,2) DEFAULT NULL,
  `lifterClass` varchar(255) DEFAULT NULL,
  `lifterCat` varchar(255) DEFAULT NULL,
  `lifterFlight` char(1) DEFAULT NULL,
  `nextLot` int(11) DEFAULT NULL,
  `nextName` char(255) DEFAULT NULL,
  `nextRack` char(255) DEFAULT NULL,
  `nextLoad` double(5,2) DEFAULT NULL,
  `lot` int(11) DEFAULT NULL,
  `rack` char(16) DEFAULT NULL,
  `hish` char(255) DEFAULT NULL,
  `bar` double(5,2) DEFAULT NULL,
  `parentComp` char(3) DEFAULT NULL,
  `isChild` int(1) DEFAULT NULL,
  `isParent` tinyint(1) DEFAULT NULL,
  `streamURL` char(1) DEFAULT NULL,
  PRIMARY KEY (`compID`)
) ENGINE=InnoDB AUTO_INCREMENT=827 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compstatus`
--

DROP TABLE IF EXISTS `compstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compstatus` (
  `compLetters` char(3) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `timeTo` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeTwo` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeThree` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `l` int(11) DEFAULT NULL,
  `c` int(11) DEFAULT NULL,
  `r` int(11) DEFAULT NULL,
  `activeLift` char(3) DEFAULT NULL,
  `activeLifter` int(11) DEFAULT NULL,
  `nextLifter` int(11) DEFAULT NULL,
  `bar` float DEFAULT NULL,
  `thirdLifter` int(11) DEFAULT NULL,
  `activeGp` char(1) DEFAULT NULL,
  `nextLiftIs` char(3) DEFAULT NULL,
  `thirdLiftIs` char(3) DEFAULT NULL,
  `session` char(3) DEFAULT NULL,
  `streamURL` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comptemplate`
--

DROP TABLE IF EXISTS `comptemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comptemplate` (
  `gp` char(1) DEFAULT NULL,
  `lot` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `team` varchar(5) DEFAULT NULL,
  `year` date DEFAULT NULL,
  `agediv` varchar(10) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `gear` char(2) DEFAULT NULL,
  `lifts` char(2) DEFAULT NULL,
  `bw` float DEFAULT NULL,
  `wc` varchar(10) DEFAULT NULL,
  `division` varchar(10) DEFAULT NULL,
  `sr` varchar(10) DEFAULT NULL,
  `br` varchar(10) DEFAULT NULL,
  `sq1` float DEFAULT NULL,
  `sq2` float DEFAULT NULL,
  `sq3` float DEFAULT NULL,
  `bsq` float DEFAULT NULL,
  `bp1` float DEFAULT NULL,
  `bp2` float DEFAULT NULL,
  `bp3` float DEFAULT NULL,
  `bbp` float DEFAULT NULL,
  `st` float DEFAULT NULL,
  `dl1` float DEFAULT NULL,
  `dl2` float DEFAULT NULL,
  `dl3` float DEFAULT NULL,
  `bdl` float DEFAULT NULL,
  `total` float DEFAULT NULL,
  `formula` float DEFAULT NULL,
  `teampoints` int(11) DEFAULT NULL,
  `session` char(3) DEFAULT NULL,
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `pt` float DEFAULT NULL,
  `sa1` int(1) DEFAULT NULL,
  `sa2` int(1) DEFAULT NULL,
  `sa3` int(1) DEFAULT NULL,
  `ba1` int(1) DEFAULT NULL,
  `ba2` int(1) DEFAULT NULL,
  `ba3` int(1) DEFAULT NULL,
  `da1` int(1) DEFAULT NULL,
  `da2` int(1) DEFAULT NULL,
  `da3` int(1) DEFAULT NULL,
  `lighthistory` char(9) DEFAULT NULL,
  `pbb` float DEFAULT NULL,
  `pbs` float DEFAULT NULL,
  `pbd` float DEFAULT NULL,
  `pbt` float DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `liftidx` int(11) DEFAULT NULL,
  `place` int(11) DEFAULT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-01  0:40:32
