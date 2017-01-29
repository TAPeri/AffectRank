-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsql-ipg36.eigbox.net
-- Generation Time: Apr 18, 2016 at 05:23 PM
-- Server version: 5.5.43
-- PHP Version: 4.4.9
-- 
-- Database: `emotion_annotation123`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `Annotations`
-- 

CREATE TABLE `Annotations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ContinuousTask` tinyint(1) NOT NULL,
  `VideoTime` float NOT NULL,
  `Arousal` float NOT NULL,
  `Valence` float NOT NULL,
  `AnnotationTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `VideoName` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=81130 DEFAULT CHARSET=latin1 AUTO_INCREMENT=81130 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Users`
-- 

CREATE TABLE `Users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `ContinuousFirst` tinyint(1) NOT NULL,
  `CurrentPercent` float NOT NULL,
  `FinishedVideos` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;
