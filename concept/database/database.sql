SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `exams-database` ;
CREATE SCHEMA IF NOT EXISTS `exams-database` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `exams-database` ;

-- -----------------------------------------------------
-- Table `exams-database`.`degree_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`degree_group` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`degree_group` (
  `iddegree_group` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iddegree_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`degree`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`degree` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`degree` (
  `iddegree` INT NOT NULL AUTO_INCREMENT ,
  `degree_group_iddegree_group` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iddegree`) ,
  INDEX `fk_degree_degree_group` (`degree_group_iddegree_group` ASC) ,
  CONSTRAINT `fk_degree_degree_group`
    FOREIGN KEY (`degree_group_iddegree_group` )
    REFERENCES `exams-database`.`degree_group` (`iddegree_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`course` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`course` (
  `idcourse` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idcourse`) )
ENGINE = InnoDB
PACK_KEYS = DEFAULT;


-- -----------------------------------------------------
-- Table `exams-database`.`semester`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`semester` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`semester` (
  `idsemester` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idsemester`) )
ENGINE = InnoDB
PACK_KEYS = DEFAULT;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_type` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_type` (
  `idexam_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_sub_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_sub_type` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_sub_type` (
  `idexam_sub_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_sub_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_status` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_status` (
  `idexam_status` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_status`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_degree`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_degree` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_degree` (
  `idexam_degree` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_degree`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`university`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`university` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`university` (
  `iduniversity` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iduniversity`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam` (
  `idexam` INT NOT NULL AUTO_INCREMENT ,
  `semester_idsemester` INT NOT NULL ,
  `exam_type_idexam_type` INT NOT NULL ,
  `exam_sub_type_idexam_sub_type` INT NOT NULL ,
  `exam_status_idexam_status` INT NOT NULL ,
  `exam_degree_idexam_degree` INT NOT NULL ,
  `university_iduniversity` INT NOT NULL ,
  `comment` TEXT NULL ,
  PRIMARY KEY (`idexam`) ,
  INDEX `fk_exame_semester` (`semester_idsemester` ASC) ,
  INDEX `fk_exame_exame_type` (`exam_type_idexam_type` ASC) ,
  INDEX `fk_exame_exame_sub_type` (`exam_sub_type_idexam_sub_type` ASC) ,
  INDEX `fk_exam_exam_status` (`exam_status_idexam_status` ASC) ,
  INDEX `fk_exam_exam_degree1` (`exam_degree_idexam_degree` ASC) ,
  INDEX `fk_exam_university1` (`university_iduniversity` ASC) ,
  CONSTRAINT `fk_exame_semester`
    FOREIGN KEY (`semester_idsemester` )
    REFERENCES `exams-database`.`semester` (`idsemester` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_exame_type`
    FOREIGN KEY (`exam_type_idexam_type` )
    REFERENCES `exams-database`.`exam_type` (`idexam_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_exame_sub_type`
    FOREIGN KEY (`exam_sub_type_idexam_sub_type` )
    REFERENCES `exams-database`.`exam_sub_type` (`idexam_sub_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_exam_status`
    FOREIGN KEY (`exam_status_idexam_status` )
    REFERENCES `exams-database`.`exam_status` (`idexam_status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_exam_degree1`
    FOREIGN KEY (`exam_degree_idexam_degree` )
    REFERENCES `exams-database`.`exam_degree` (`idexam_degree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_university1`
    FOREIGN KEY (`university_iduniversity` )
    REFERENCES `exams-database`.`university` (`iduniversity` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`lecturer` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`lecturer` (
  `idlecturer` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `first_name` VARCHAR(255) NULL ,
  `degree` VARCHAR(255) NULL ,
  PRIMARY KEY (`idlecturer`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`document`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`document` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`document` (
  `iddocument` INT NOT NULL AUTO_INCREMENT ,
  `extention` VARCHAR(10) NULL ,
  `submit_file_name` VARCHAR(255) NULL ,
  `data` LONGBLOB NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  `exam_idexam` INT NOT NULL ,
  PRIMARY KEY (`iddocument`) ,
  INDEX `fk_document_exam1` (`exam_idexam` ASC) ,
  CONSTRAINT `fk_document_exam1`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exams-database`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`degree_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`degree_has_course` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`degree_has_course` (
  `degree_iddegree` INT NOT NULL ,
  `course_idcourse` INT NOT NULL ,
  PRIMARY KEY (`degree_iddegree`, `course_idcourse`) ,
  INDEX `fk_degree_has_course_degree` (`degree_iddegree` ASC) ,
  INDEX `fk_degree_has_course_course` (`course_idcourse` ASC) ,
  CONSTRAINT `fk_degree_has_course_degree`
    FOREIGN KEY (`degree_iddegree` )
    REFERENCES `exams-database`.`degree` (`iddegree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_degree_has_course_course`
    FOREIGN KEY (`course_idcourse` )
    REFERENCES `exams-database`.`course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `exams-database`.`exam_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_log` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_log` (
  `idexam_log` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `message` TEXT NULL ,
  PRIMARY KEY (`idexam_log`) ,
  INDEX `fk_exam_log_exam` (`exam_idexam` ASC) ,
  CONSTRAINT `fk_exam_log_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exams-database`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_has_course` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_has_course` (
  `exam_idexam` INT NOT NULL ,
  `course_idcourse` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `course_idcourse`) ,
  INDEX `fk_exam_has_course_group_exam` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_course_group_course1` (`course_idcourse` ASC) ,
  CONSTRAINT `fk_exam_has_course_group_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exams-database`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_has_course_group_course1`
    FOREIGN KEY (`course_idcourse` )
    REFERENCES `exams-database`.`course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `exams-database`.`degree_has_lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`degree_has_lecturer` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`degree_has_lecturer` (
  `degree_iddegree` INT NOT NULL ,
  `lecturer_idlecturer` INT NOT NULL ,
  PRIMARY KEY (`degree_iddegree`, `lecturer_idlecturer`) ,
  INDEX `fk_degree_has_lecturer_degree` (`degree_iddegree` ASC) ,
  INDEX `fk_degree_has_lecturer_lecturer` (`lecturer_idlecturer` ASC) ,
  CONSTRAINT `fk_degree_has_lecturer_degree`
    FOREIGN KEY (`degree_iddegree` )
    REFERENCES `exams-database`.`degree` (`iddegree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_degree_has_lecturer_lecturer`
    FOREIGN KEY (`lecturer_idlecturer` )
    REFERENCES `exams-database`.`lecturer` (`idlecturer` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `exams-database`.`exam_has_lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`exam_has_lecturer` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_has_lecturer` (
  `exam_idexam` INT NOT NULL ,
  `lecturer_idlecturer` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `lecturer_idlecturer`) ,
  INDEX `fk_exam_has_lecturer_exam` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_lecturer_lecturer` (`lecturer_idlecturer` ASC) ,
  CONSTRAINT `fk_exam_has_lecturer_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exams-database`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_has_lecturer_lecturer`
    FOREIGN KEY (`lecturer_idlecturer` )
    REFERENCES `exams-database`.`lecturer` (`idlecturer` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `exams-database`.`course_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exams-database`.`course_has_course` ;

CREATE  TABLE IF NOT EXISTS `exams-database`.`course_has_course` (
  `course_idcourse` INT NOT NULL ,
  `course_idcourse1` INT NOT NULL ,
  `course_has_relationship` INT NULL DEFAULT 100 ,
  PRIMARY KEY (`course_idcourse`, `course_idcourse1`) ,
  INDEX `fk_course_has_course_course2` (`course_idcourse1` ASC) ,
  INDEX `fk_course_has_course_course1` (`course_idcourse` ASC) ,
  CONSTRAINT `fk_course_has_course_course1`
    FOREIGN KEY (`course_idcourse` )
    REFERENCES `exams-database`.`course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_has_course_course2`
    FOREIGN KEY (`course_idcourse1` )
    REFERENCES `exams-database`.`course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `exams-database`.`degree_group`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`degree_group` (`iddegree_group`, `name`) VALUES (1, 'Informatik');
INSERT INTO `exams-database`.`degree_group` (`iddegree_group`, `name`) VALUES (2, 'Mikrosystemtechnik');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`degree`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (1, 1, 'Informatik (Bachelor)');
INSERT INTO `exams-database`.`degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (2, 1, 'Informatik (Master)');
INSERT INTO `exams-database`.`degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (3, 2, 'Mikrosystemtechnik (Bachelor)');
INSERT INTO `exams-database`.`degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (4, 2, 'Mikrosystemtechnik (Master)');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`course`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (1, 'Datenbanken I');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (2, 'Mustererkennung I');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (3, 'Informatik I');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (4, 'Rechnerarchitektur');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (5, 'Simulation');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (6, 'Messtechnik (Praktikum)');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (7, 'Halbleiter');
INSERT INTO `exams-database`.`course` (`idcourse`, `name`) VALUES (8, 'Nanotechnology');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`semester`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`semester` (`idsemester`, `name`) VALUES (1, 'WS 2010/11');
INSERT INTO `exams-database`.`semester` (`idsemester`, `name`) VALUES (2, 'SS 2011');
INSERT INTO `exams-database`.`semester` (`idsemester`, `name`) VALUES (3, 'WS 2011/12');
INSERT INTO `exams-database`.`semester` (`idsemester`, `name`) VALUES (4, 'SS 2012');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam_type`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam_type` (`idexam_type`, `name`) VALUES (1, 'Klausur');
INSERT INTO `exams-database`.`exam_type` (`idexam_type`, `name`) VALUES (2, 'Protokoll');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam_sub_type`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam_sub_type` (`idexam_sub_type`, `name`) VALUES (1, 'mit L&ouml;sung');
INSERT INTO `exams-database`.`exam_sub_type` (`idexam_sub_type`, `name`) VALUES (2, 'ohne L&ouml;sung');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam_status`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam_status` (`idexam_status`, `name`) VALUES (1, 'public');
INSERT INTO `exams-database`.`exam_status` (`idexam_status`, `name`) VALUES (2, 'unchecked');
INSERT INTO `exams-database`.`exam_status` (`idexam_status`, `name`) VALUES (3, 'deleted');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam_degree`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam_degree` (`idexam_degree`, `name`) VALUES (1, 'Bachelor');
INSERT INTO `exams-database`.`exam_degree` (`idexam_degree`, `name`) VALUES (2, 'Master');
INSERT INTO `exams-database`.`exam_degree` (`idexam_degree`, `name`) VALUES (3, 'Diploma');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`university`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`university` (`iduniversity`, `name`) VALUES (1, 'University of Freiburg');
INSERT INTO `exams-database`.`university` (`iduniversity`, `name`) VALUES (2, 'University of Hamburg');
INSERT INTO `exams-database`.`university` (`iduniversity`, `name`) VALUES (3, 'University of Karlsruhe');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`) VALUES (1, 1, 1, 2, 1, 1, 1, 'Note:');
INSERT INTO `exams-database`.`exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`) VALUES (2, 2, 1, 2, 1, 2, 2, NULL);
INSERT INTO `exams-database`.`exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`) VALUES (3, 1, 2, 2, 1, 1, 3, NULL);
INSERT INTO `exams-database`.`exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`) VALUES (4, 3, 1, 1, 1, 3, 1, NULL);
INSERT INTO `exams-database`.`exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`) VALUES (5, 4, 2, 1, 1, 2, 1, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`lecturer`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (1, 'Huber', 'D. R.', 'Prof.');
INSERT INTO `exams-database`.`lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (2, 'Schwerkel', 'A.', 'Prof. Dr.');
INSERT INTO `exams-database`.`lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (3, 'Nubra', 'W.', 'Dr.');
INSERT INTO `exams-database`.`lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (4, 'Adams', 'S. G.', 'Prof. Dr.');
INSERT INTO `exams-database`.`lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (5, 'Gerbert', '', '');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`document`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`, `exam_idexam`) VALUES (1, '.pdf', 'zusammenfassung.pdf', NULL, \func false, NULL);
INSERT INTO `exams-database`.`document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`, `exam_idexam`) VALUES (2, '.pdf', '1.pdf', NULL, \func false, NULL);
INSERT INTO `exams-database`.`document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`, `exam_idexam`) VALUES (3, '.zip', 'exam.zip', NULL, \func false, NULL);
INSERT INTO `exams-database`.`document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`, `exam_idexam`) VALUES (4, '.pdf', '012930.pdf', NULL, \func false, NULL);
INSERT INTO `exams-database`.`document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`, `exam_idexam`) VALUES (5, '.tar.gz', 'w12.tar.gz', NULL, \func false, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`degree_has_course`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 1);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 2);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 3);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 4);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (2, 1);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (2, 3);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (2, 4);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (3, 5);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (3, 6);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (3, 7);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (4, 8);
INSERT INTO `exams-database`.`degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (4, 6);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam_has_course`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (1, 1);
INSERT INTO `exams-database`.`exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (2, 3);
INSERT INTO `exams-database`.`exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (3, 5);
INSERT INTO `exams-database`.`exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (4, 7);
INSERT INTO `exams-database`.`exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (5, 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`degree_has_lecturer`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (1, 1);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (1, 2);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (1, 3);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (2, 2);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (2, 3);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (2, 4);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 2);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 3);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 4);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (4, 4);
INSERT INTO `exams-database`.`degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (4, 5);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`exam_has_lecturer`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (1, 3);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (2, 2);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (3, 5);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (4, 4);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 1);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (2, 3);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 3);
INSERT INTO `exams-database`.`exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exams-database`.`course_has_course`
-- -----------------------------------------------------
START TRANSACTION;
USE `exams-database`;
INSERT INTO `exams-database`.`course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (1, 2, 100);
INSERT INTO `exams-database`.`course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (8, 7, 100);
INSERT INTO `exams-database`.`course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (5, 6, 100);
INSERT INTO `exams-database`.`course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (3, 2, 100);
INSERT INTO `exams-database`.`course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (4, 3, 100);
INSERT INTO `exams-database`.`course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (7, 6, 100);

COMMIT;
