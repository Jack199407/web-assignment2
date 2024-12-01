-- Create the database if it does not exist
CREATE DATABASE IF NOT EXISTS `taskmanagement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Use the database
USE `taskmanagement`;

-- Disable unique checks and foreign key checks temporarily
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

-- Drop old tables if they exist
DROP TABLE IF EXISTS `taskLogs`;
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `users`;

-- Create the users table
CREATE TABLE `users` (
  `userId` INT NOT NULL AUTO_INCREMENT, -- Primary key for the users table
  `loginName` VARCHAR(50) NOT NULL,    -- Unique login name
  `passwd` VARCHAR(255) NOT NULL,      -- User password (hashed)
  `email` VARCHAR(100) NOT NULL,       -- User email
  `firstName` VARCHAR(50) NULL,        -- First name (optional)
  `lastName` VARCHAR(50) NULL,         -- Last name (optional)
  PRIMARY KEY (`userId`),              -- Define primary key
  UNIQUE INDEX `login_name_UNIQUE` (`loginName`), -- Ensure loginName is unique
  UNIQUE INDEX `email_UNIQUE` (`email`)          -- Ensure email is unique
) ENGINE = InnoDB;

-- Create the tasks table
CREATE TABLE `tasks` (
  `taskId` INT NOT NULL AUTO_INCREMENT, -- Primary key for the tasks table
  `taskName` VARCHAR(100) NOT NULL,    -- Task name
  `priority` INT NOT NULL COMMENT '0 - High, 1 - Middle, 2 - Low', -- Task priority levels
  `dueDate` DATE NOT NULL,             -- Due date for the task
  `taskStatus` INT NOT NULL COMMENT '0 - ToDo, 1 - InProgress, 2 - Completed, 3 - Paused, 4 - Cancelled', -- Task status
  `userId` INT NOT NULL,               -- Foreign key referencing the users table
  PRIMARY KEY (`taskId`),              -- Define primary key
  FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE -- Foreign key relationship
) ENGINE = InnoDB;

-- Create the taskLogs table
CREATE TABLE `taskLogs` (
  `logId` INT NOT NULL AUTO_INCREMENT, -- Primary key for the taskLogs table
  `taskId` INT NOT NULL,               -- Foreign key referencing the tasks table
  `operatorUserId` INT NOT NULL,       -- Foreign key referencing the users table (operator)
  `operationType` INT NOT NULL COMMENT '0 - Create, 1 - Update, 2 - Delete, 3 - Status Change', -- Type of operation performed
  `changeTime` DATETIME NOT NULL,      -- Timestamp of the operation
  `taskName` VARCHAR(100) NOT NULL,    -- Name of the task at the time of the operation
  `priority` INT NOT NULL,             -- Priority of the task at the time of the operation
  `dueDate` DATE NOT NULL,             -- Due date of the task at the time of the operation
  `taskStatus` INT NOT NULL,           -- Task status at the time of the operation
  PRIMARY KEY (`logId`),               -- Define primary key
  FOREIGN KEY (`taskId`) REFERENCES `tasks` (`taskId`) ON DELETE CASCADE ON UPDATE CASCADE, -- Foreign key to tasks table
  FOREIGN KEY (`operatorUserId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE -- Foreign key to users table
) ENGINE = InnoDB;

-- Restore original unique checks and foreign key checks
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
