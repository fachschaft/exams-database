SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `degree_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree_group` ;

CREATE  TABLE IF NOT EXISTS `degree_group` (
  `iddegree_group` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iddegree_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `degree`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree` ;

CREATE  TABLE IF NOT EXISTS `degree` (
  `iddegree` INT NOT NULL AUTO_INCREMENT ,
  `degree_group_iddegree_group` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iddegree`) ,
  CONSTRAINT `fk_degree_degree_group`
    FOREIGN KEY (`degree_group_iddegree_group` )
    REFERENCES `degree_group` (`iddegree_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_degree_degree_group` ON `degree` (`degree_group_iddegree_group` ASC) ;


-- -----------------------------------------------------
-- Table `course_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course_group` ;

CREATE  TABLE IF NOT EXISTS `course_group` (
  `idcourse_group` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idcourse_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course` ;

CREATE  TABLE IF NOT EXISTS `course` (
  `idcourse` INT NOT NULL AUTO_INCREMENT ,
  `course_group_idcourse_group` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idcourse`) ,
  CONSTRAINT `fk_courses_courses_group`
    FOREIGN KEY (`course_group_idcourse_group` )
    REFERENCES `course_group` (`idcourse_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
PACK_KEYS = DEFAULT;

CREATE INDEX `fk_courses_courses_group` ON `course` (`course_group_idcourse_group` ASC) ;


-- -----------------------------------------------------
-- Table `semester`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `semester` ;

CREATE  TABLE IF NOT EXISTS `semester` (
  `idsemester` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idsemester`) )
ENGINE = InnoDB
PACK_KEYS = DEFAULT;


-- -----------------------------------------------------
-- Table `exam_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_type` ;

CREATE  TABLE IF NOT EXISTS `exam_type` (
  `idexam_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `document`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `document` ;

CREATE  TABLE IF NOT EXISTS `document` (
  `iddocument` INT NOT NULL AUTO_INCREMENT ,
  `extention` VARCHAR(10) NULL ,
  `submit_file_name` VARCHAR(255) NULL ,
  `data` LONGBLOB NULL ,
  `deleted` BOOLEAN NULL DEFAULT false ,
  PRIMARY KEY (`iddocument`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exam_sub_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_sub_type` ;

CREATE  TABLE IF NOT EXISTS `exam_sub_type` (
  `idexam_sub_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_sub_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exam_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_status` ;

CREATE  TABLE IF NOT EXISTS `exam_status` (
  `idexam_status` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_status`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exam`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam` ;

CREATE  TABLE IF NOT EXISTS `exam` (
  `idexam` INT NOT NULL AUTO_INCREMENT ,
  `semester_idsemester` INT NOT NULL ,
  `exam_type_idexam_type` INT NOT NULL ,
  `document_iddocument` INT NOT NULL ,
  `exam_sub_type_idexam_sub_type` INT NOT NULL ,
  `exam_status_idexam_status` INT NOT NULL ,
  `comment` TEXT NULL ,
  PRIMARY KEY (`idexam`) ,
  CONSTRAINT `fk_exame_semester`
    FOREIGN KEY (`semester_idsemester` )
    REFERENCES `semester` (`idsemester` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_exame_type`
    FOREIGN KEY (`exam_type_idexam_type` )
    REFERENCES `exam_type` (`idexam_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_document`
    FOREIGN KEY (`document_iddocument` )
    REFERENCES `document` (`iddocument` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_exame_sub_type`
    FOREIGN KEY (`exam_sub_type_idexam_sub_type` )
    REFERENCES `exam_sub_type` (`idexam_sub_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_exam_status`
    FOREIGN KEY (`exam_status_idexam_status` )
    REFERENCES `exam_status` (`idexam_status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_exame_semester` ON `exam` (`semester_idsemester` ASC) ;

CREATE INDEX `fk_exame_exame_type` ON `exam` (`exam_type_idexam_type` ASC) ;

CREATE INDEX `fk_exame_document` ON `exam` (`document_iddocument` ASC) ;

CREATE INDEX `fk_exame_exame_sub_type` ON `exam` (`exam_sub_type_idexam_sub_type` ASC) ;

CREATE INDEX `fk_exam_exam_status` ON `exam` (`exam_status_idexam_status` ASC) ;


-- -----------------------------------------------------
-- Table `lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lecturer` ;

CREATE  TABLE IF NOT EXISTS `lecturer` (
  `idlecturer` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `first_name` VARCHAR(255) NULL ,
  `degree` VARCHAR(255) NULL ,
  PRIMARY KEY (`idlecturer`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `degree_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree_has_course` ;

CREATE  TABLE IF NOT EXISTS `degree_has_course` (
  `degree_iddegree` INT NOT NULL ,
  `course_idcourse` INT NOT NULL ,
  PRIMARY KEY (`degree_iddegree`, `course_idcourse`) ,
  CONSTRAINT `fk_degree_has_course_degree`
    FOREIGN KEY (`degree_iddegree` )
    REFERENCES `degree` (`iddegree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_degree_has_course_course`
    FOREIGN KEY (`course_idcourse` )
    REFERENCES `course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE INDEX `fk_degree_has_course_degree` ON `degree_has_course` (`degree_iddegree` ASC) ;

CREATE INDEX `fk_degree_has_course_course` ON `degree_has_course` (`course_idcourse` ASC) ;


-- -----------------------------------------------------
-- Table `exam_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_log` ;

CREATE  TABLE IF NOT EXISTS `exam_log` (
  `idexam_log` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `message` TEXT NULL ,
  PRIMARY KEY (`idexam_log`) ,
  CONSTRAINT `fk_exam_log_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_exam_log_exam` ON `exam_log` (`exam_idexam` ASC) ;


-- -----------------------------------------------------
-- Table `exam_has_course_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_has_course_group` ;

CREATE  TABLE IF NOT EXISTS `exam_has_course_group` (
  `exam_idexam` INT NOT NULL ,
  `course_group_idcourse_group` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `course_group_idcourse_group`) ,
  CONSTRAINT `fk_exam_has_course_group_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_has_course_group_course_group`
    FOREIGN KEY (`course_group_idcourse_group` )
    REFERENCES `course_group` (`idcourse_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE INDEX `fk_exam_has_course_group_exam` ON `exam_has_course_group` (`exam_idexam` ASC) ;

CREATE INDEX `fk_exam_has_course_group_course_group` ON `exam_has_course_group` (`course_group_idcourse_group` ASC) ;


-- -----------------------------------------------------
-- Table `degree_has_lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree_has_lecturer` ;

CREATE  TABLE IF NOT EXISTS `degree_has_lecturer` (
  `degree_iddegree` INT NOT NULL ,
  `lecturer_idlecturer` INT NOT NULL ,
  PRIMARY KEY (`degree_iddegree`, `lecturer_idlecturer`) ,
  CONSTRAINT `fk_degree_has_lecturer_degree`
    FOREIGN KEY (`degree_iddegree` )
    REFERENCES `degree` (`iddegree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_degree_has_lecturer_lecturer`
    FOREIGN KEY (`lecturer_idlecturer` )
    REFERENCES `lecturer` (`idlecturer` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE INDEX `fk_degree_has_lecturer_degree` ON `degree_has_lecturer` (`degree_iddegree` ASC) ;

CREATE INDEX `fk_degree_has_lecturer_lecturer` ON `degree_has_lecturer` (`lecturer_idlecturer` ASC) ;


-- -----------------------------------------------------
-- Table `exam_has_lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_has_lecturer` ;

CREATE  TABLE IF NOT EXISTS `exam_has_lecturer` (
  `exam_idexam` INT NOT NULL ,
  `lecturer_idlecturer` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `lecturer_idlecturer`) ,
  CONSTRAINT `fk_exam_has_lecturer_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_has_lecturer_lecturer`
    FOREIGN KEY (`lecturer_idlecturer` )
    REFERENCES `lecturer` (`idlecturer` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE INDEX `fk_exam_has_lecturer_exam` ON `exam_has_lecturer` (`exam_idexam` ASC) ;

CREATE INDEX `fk_exam_has_lecturer_lecturer` ON `exam_has_lecturer` (`lecturer_idlecturer` ASC) ;


-- -----------------------------------------------------
-- Data for table `degree`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (1, 1, 'Informatik (Bachelor)');
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (2, 1, 'Informatik (Master)');
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (3, 2, 'Mikrosystemtechnik (Bachelor)');
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (4, 2, 'Mikrosystemtechnik (Master)');

COMMIT;

-- -----------------------------------------------------
-- Data for table `course`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (1, 1, 'Datenbanken I');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (2, 2, 'Mustererkennung I');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (3, 6, 'Informatik I');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (4, 1, 'Rechnerarchitektur');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (5, 3, 'Simulation');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (6, 3, 'Messtechnik (Praktikum)');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (7, 4, 'Halbleiter');
INSERT INTO `course` (`idcourse`, `course_group_idcourse_group`, `name`) VALUES (8, 5, 'Nanotechnology');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `document_iddocument`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `comment`) VALUES (1, 1, 1, 1, 2, 1, NULL);
INSERT INTO `exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `document_iddocument`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `comment`) VALUES (2, 2, 1, 2, 2, 1, NULL);
INSERT INTO `exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `document_iddocument`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `comment`) VALUES (3, 1, 2, 3, 2, 1, NULL);
INSERT INTO `exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `document_iddocument`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `comment`) VALUES (4, 3, 1, 4, 1, 1, NULL);
INSERT INTO `exam` (`idexam`, `semester_idsemester`, `exam_type_idexam_type`, `document_iddocument`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `comment`) VALUES (5, 4, 2, 5, 1, 1, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `course_group`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `course_group` (`idcourse_group`) VALUES (1);
INSERT INTO `course_group` (`idcourse_group`) VALUES (2);
INSERT INTO `course_group` (`idcourse_group`) VALUES (3);
INSERT INTO `course_group` (`idcourse_group`) VALUES (4);
INSERT INTO `course_group` (`idcourse_group`) VALUES (5);
INSERT INTO `course_group` (`idcourse_group`) VALUES (6);

COMMIT;

-- -----------------------------------------------------
-- Data for table `degree_group`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `degree_group` (`iddegree_group`, `name`) VALUES (1, 'Informatik');
INSERT INTO `degree_group` (`iddegree_group`, `name`) VALUES (2, 'Mikrosystemtechnik');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `exam_type` (`idexam_type`, `name`) VALUES (1, 'Klausur');
INSERT INTO `exam_type` (`idexam_type`, `name`) VALUES (2, 'Protokoll');

COMMIT;

-- -----------------------------------------------------
-- Data for table `lecturer`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (1, 'Huber', 'D. R.', 'Prof.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (2, 'Schwerkel', 'A.', 'Prof. Dr.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (3, 'Nubra', 'W.', 'Dr.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (4, 'Adams', 'S. G.', 'Prof. Dr.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (5, 'Gerbert', '', '');

COMMIT;

-- -----------------------------------------------------
-- Data for table `semester`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `semester` (`idsemester`, `name`) VALUES (1, 'WS 2010/11');
INSERT INTO `semester` (`idsemester`, `name`) VALUES (2, 'SS 2011');
INSERT INTO `semester` (`idsemester`, `name`) VALUES (3, 'WS 2011/12');
INSERT INTO `semester` (`idsemester`, `name`) VALUES (4, 'SS 2012');

COMMIT;

-- -----------------------------------------------------
-- Data for table `document`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`) VALUES (1, '.pdf', 'zusammenfassung.pdf', NULL, false);
INSERT INTO `document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`) VALUES (2, '.pdf', '1.pdf', NULL, false);
INSERT INTO `document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`) VALUES (3, '.zip', 'exam.zip', NULL, false);
INSERT INTO `document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`) VALUES (4, '.pdf', '012930.pdf', NULL, false);
INSERT INTO `document` (`iddocument`, `extention`, `submit_file_name`, `data`, `deleted`) VALUES (5, '.tar.gz', 'w12.tar.gz', NULL, false);

COMMIT;

-- -----------------------------------------------------
-- Data for table `degree_has_course`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 1);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 2);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 3);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (1, 4);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (2, 1);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (2, 3);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (2, 4);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (3, 5);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (3, 6);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (3, 7);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (4, 8);
INSERT INTO `degree_has_course` (`degree_iddegree`, `course_idcourse`) VALUES (4, 6);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_sub_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `exam_sub_type` (`idexam_sub_type`, `name`) VALUES (1, 'mit L&ouml;sung');
INSERT INTO `exam_sub_type` (`idexam_sub_type`, `name`) VALUES (2, 'ohne L&ouml;sung');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_status`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (1, 'public');
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (2, 'unchecked');
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (3, 'deleted');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_has_course_group`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (1, 1);
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (2, 3);
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (3, 5);
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (4, 6);
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (5, 2);
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (3, 4);
INSERT INTO `exam_has_course_group` (`exam_idexam`, `course_group_idcourse_group`) VALUES (5, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `degree_has_lecturer`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (1, 1);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (1, 2);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (1, 3);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (2, 2);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (2, 3);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (2, 4);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 2);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 3);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 4);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (4, 4);
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (4, 5);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_has_lecturer`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (1, 3);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (2, 2);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (3, 5);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (4, 4);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 1);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (2, 3);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 3);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 2);

COMMIT;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
