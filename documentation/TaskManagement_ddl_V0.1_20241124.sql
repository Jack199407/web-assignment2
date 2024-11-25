-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema TaskManagement
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `TaskManagement` ;

-- -----------------------------------------------------
-- Schema TaskManagement
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `TaskManagement` DEFAULT CHARACTER SET utf8 ;
USE `TaskManagement` ;

-- -----------------------------------------------------
-- Table `TaskManagement`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TaskManagement`.`user` ;

CREATE TABLE IF NOT EXISTS `TaskManagement`.`user` (
  `userId` INT NOT NULL,
  `loginName` VARCHAR(20) NOT NULL,
  `passwd` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `firstName` VARCHAR(20) NULL,
  `lastName` VARCHAR(20) NULL,
  PRIMARY KEY (`userId`),
  UNIQUE INDEX `login_name_UNIQUE` (`loginName` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `user_id_UNIQUE` (`userId` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TaskManagement`.`task`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TaskManagement`.`task` ;

CREATE TABLE IF NOT EXISTS `TaskManagement`.`task` (
  `taskId` INT NOT NULL,
  `taskName` VARCHAR(50) NOT NULL,
  `priority` INT NOT NULL COMMENT '0 - High\n1 - Middle\n2 - Low',
  `dueDate` DATE NOT NULL,
  `taskStatus` INT NOT NULL COMMENT '0 - ToDo\n1 - InProgress\n2 - Completed\n3 - Paused\n4 - Cancelled',
  `userId` INT NOT NULL,
  PRIMARY KEY (`taskId`, `userId`),
  UNIQUE INDEX `task_id_UNIQUE` (`taskId` ASC) VISIBLE,
  INDEX `userID_idx` (`userId` ASC) VISIBLE,
  CONSTRAINT `task_with_user`
    FOREIGN KEY (`userId`)
    REFERENCES `TaskManagement`.`user` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TaskManagement`.`task_logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TaskManagement`.`task_logs` ;

CREATE TABLE IF NOT EXISTS `TaskManagement`.`task_logs` (
  `logId` INT NOT NULL,
  `taskId` INT NOT NULL,
  `operaterUserId` INT NOT NULL,
  `operationType` INT NOT NULL COMMENT '0 - Create\n1 - Update\n2 - Delete\n3 - Status Change',
  `changeTime` VARCHAR(50) NOT NULL,
  `taskName` VARCHAR(50) NOT NULL,
  `priority` INT NOT NULL,
  `dueDate` DATE NOT NULL,
  `taskStatus` INT NOT NULL,
  PRIMARY KEY (`logId`, `taskId`, `operaterUserId`),
  UNIQUE INDEX `log_id_UNIQUE` (`logId` ASC) VISIBLE,
  INDEX `taskID_idx` (`taskId` ASC) VISIBLE,
  INDEX `userID_idx` (`operaterUserId` ASC) VISIBLE,
  CONSTRAINT `tasklog_with_task`
    FOREIGN KEY (`taskId`)
    REFERENCES `TaskManagement`.`task` (`taskId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `tasklog_with_user`
    FOREIGN KEY (`operaterUserId`)
    REFERENCES `TaskManagement`.`user` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
