-- I'm using MyISAM DB engine so referential integrity is handled inside the code
-- I.E. That's way I'm not using foreign keys in DB design

CREATE TABLE IF NOT EXISTS `schools` ( 
    `SchoolID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , 
    `Name` VARCHAR(255) , 
    `DateAdded` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (`SchoolID`), 
    INDEX `SchoolName` (`Name`)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `members` ( 
    `MemberID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , 
    `Name` VARCHAR(255) , 
    `EmailAddress` VARCHAR(255) NULL ,
    `DateAdded` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (`MemberID`)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `member_schools` ( 
    `ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , 
    `MemberID` INT(11) UNSIGNED NOT NULL , 
    `SchoolID` INT(11) UNSIGNED NOT NULL ,
    `DateAdded` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (`ID`)
) ENGINE = MyISAM;