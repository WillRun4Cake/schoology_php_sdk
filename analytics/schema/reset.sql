CREATE DATABASE IF NOT EXISTS `schoology`;
CREATE USER IF NOT EXISTS `santa`@`localhost` IDENTIFIED BY 'hG8(*3te8@-)&et#uC8%3Et*7e';
GRANT ALL PRIVILEGES ON `schoology`.* TO `santa`@`localhost`;
GRANT FILE ON *.* TO `santa`@`localhost`;
USE `schoology`;
DROP TABLE IF EXISTS `data`;
CREATE TABLE IF NOT EXISTS `data` (
	`id` BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`roleName` VARCHAR(50),
	`userBuildingId` INTEGER,
	`userBuildingName` VARCHAR(100),
	`username` VARCHAR(50),
	`email` VARCHAR(80),
	`schoologyUserId` INTEGER,
	`uniqueUserId` VARCHAR(150),
	`actionType` VARCHAR(40),
	`itemType` VARCHAR(60),
	`itemId` BIGINT,
	`itemName` VARCHAR(150),
	`courseName` VARCHAR(150),
	`courseCode` VARCHAR(40),
	`sectionName` VARCHAR(60),
	`lastEventTimestamp` DATETIME,
	`eventCount` INTEGER,
	`roleId` INTEGER,
	`userBuildingCode` INTEGER,
	`lastName` VARCHAR(40),
	`firstName` VARCHAR(40),
	`deviceType` VARCHAR(20),
	`itemBuildingId` INTEGER,
	`itemBuildingName` VARCHAR(100),
	`itemBuildingCode` INTEGER,
	`itemParentType` VARCHAR(40),
	`groupId` BIGINT,
	`groupName` VARCHAR(40),
	`courseId` BIGINT,
	`sectionId` BIGINT,
	`sectionSchoolCode`VARCHAR(100),
	`sectionCode` VARCHAR(60),
	`month` VARCHAR(10),
	`date` VARCHAR(20),
	`timestamp` TIME,
	`timeSpent` INTEGER
) ENGINE = MyISAM;
