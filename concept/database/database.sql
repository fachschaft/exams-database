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
  INDEX `fk_degree_degree_group` (`degree_group_iddegree_group` ASC) ,
  CONSTRAINT `fk_degree_degree_group`
    FOREIGN KEY (`degree_group_iddegree_group` )
    REFERENCES `degree_group` (`iddegree_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course` ;

CREATE  TABLE IF NOT EXISTS `course` (
  `idcourse` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idcourse`) )
ENGINE = InnoDB
PACK_KEYS = DEFAULT;


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
-- Table `exam_degree`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_degree` ;

CREATE  TABLE IF NOT EXISTS `exam_degree` (
  `idexam_degree` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_degree`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `university`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `university` ;

CREATE  TABLE IF NOT EXISTS `university` (
  `iduniversity` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iduniversity`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exam`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam` ;

CREATE  TABLE IF NOT EXISTS `exam` (
  `idexam` INT NOT NULL AUTO_INCREMENT ,
  `degree_iddegree` INT NOT NULL ,
  `semester_idsemester` INT NOT NULL ,
  `exam_type_idexam_type` INT NOT NULL ,
  `exam_sub_type_idexam_sub_type` INT NOT NULL ,
  `exam_status_idexam_status` INT NOT NULL ,
  `exam_degree_idexam_degree` INT NOT NULL ,
  `university_iduniversity` INT NOT NULL ,
  `comment` TEXT NULL ,
  `autor` TEXT NULL ,
  `create_date` TIMESTAMP NULL ,
  `modified_last_date` TIMESTAMP NULL ,
  PRIMARY KEY (`idexam`) ,
  INDEX `fk_exame_semester` (`semester_idsemester` ASC) ,
  INDEX `fk_exame_exame_type` (`exam_type_idexam_type` ASC) ,
  INDEX `fk_exame_exame_sub_type` (`exam_sub_type_idexam_sub_type` ASC) ,
  INDEX `fk_exam_exam_status` (`exam_status_idexam_status` ASC) ,
  INDEX `fk_exam_exam_degree1` (`exam_degree_idexam_degree` ASC) ,
  INDEX `fk_exam_university1` (`university_iduniversity` ASC) ,
  INDEX `fk_exam_degree1` (`degree_iddegree` ASC) ,
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
  CONSTRAINT `fk_exame_exame_sub_type`
    FOREIGN KEY (`exam_sub_type_idexam_sub_type` )
    REFERENCES `exam_sub_type` (`idexam_sub_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_exam_status`
    FOREIGN KEY (`exam_status_idexam_status` )
    REFERENCES `exam_status` (`idexam_status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_exam_degree1`
    FOREIGN KEY (`exam_degree_idexam_degree` )
    REFERENCES `exam_degree` (`idexam_degree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_university1`
    FOREIGN KEY (`university_iduniversity` )
    REFERENCES `university` (`iduniversity` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_degree1`
    FOREIGN KEY (`degree_iddegree` )
    REFERENCES `degree` (`iddegree` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


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
-- Table `document`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `document` ;

CREATE  TABLE IF NOT EXISTS `document` (
  `iddocument` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `extention` VARCHAR(10) NULL ,
  `submit_file_name` VARCHAR(255) NULL ,
  `mime_type` VARCHAR(255) NULL ,
  `file_name` VARCHAR(255) NULL ,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0 ,
  `reviewed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `downloads` INT NOT NULL DEFAULT 0 ,
  `upload_date` TIMESTAMP NULL ,
  `md5_sum` VARCHAR(32) NULL ,
  PRIMARY KEY (`iddocument`) ,
  INDEX `fk_document_exam1` (`exam_idexam` ASC) ,
  CONSTRAINT `fk_document_exam1`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `degree_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree_has_course` ;

CREATE  TABLE IF NOT EXISTS `degree_has_course` (
  `degree_iddegree` INT NOT NULL ,
  `course_idcourse` INT NOT NULL ,
  PRIMARY KEY (`degree_iddegree`, `course_idcourse`) ,
  INDEX `fk_degree_has_course_degree` (`degree_iddegree` ASC) ,
  INDEX `fk_degree_has_course_course` (`course_idcourse` ASC) ,
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


-- -----------------------------------------------------
-- Table `exam_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_log` ;

CREATE  TABLE IF NOT EXISTS `exam_log` (
  `idexam_log` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `message` TEXT NULL ,
  `date` TIMESTAMP NULL DEFAULT NOW() ,
  PRIMARY KEY (`idexam_log`) ,
  INDEX `fk_exam_log_exam` (`exam_idexam` ASC) ,
  CONSTRAINT `fk_exam_log_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exam_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_has_course` ;

CREATE  TABLE IF NOT EXISTS `exam_has_course` (
  `exam_idexam` INT NOT NULL ,
  `course_idcourse` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `course_idcourse`) ,
  INDEX `fk_exam_has_course_group_exam` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_course_group_course1` (`course_idcourse` ASC) ,
  CONSTRAINT `fk_exam_has_course_group_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_has_course_group_course1`
    FOREIGN KEY (`course_idcourse` )
    REFERENCES `course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `degree_has_lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree_has_lecturer` ;

CREATE  TABLE IF NOT EXISTS `degree_has_lecturer` (
  `degree_iddegree` INT NOT NULL ,
  `lecturer_idlecturer` INT NOT NULL ,
  PRIMARY KEY (`degree_iddegree`, `lecturer_idlecturer`) ,
  INDEX `fk_degree_has_lecturer_degree` (`degree_iddegree` ASC) ,
  INDEX `fk_degree_has_lecturer_lecturer` (`lecturer_idlecturer` ASC) ,
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


-- -----------------------------------------------------
-- Table `exam_has_lecturer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_has_lecturer` ;

CREATE  TABLE IF NOT EXISTS `exam_has_lecturer` (
  `exam_idexam` INT NOT NULL ,
  `lecturer_idlecturer` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `lecturer_idlecturer`) ,
  INDEX `fk_exam_has_lecturer_exam` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_lecturer_lecturer` (`lecturer_idlecturer` ASC) ,
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


-- -----------------------------------------------------
-- Table `course_has_course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course_has_course` ;

CREATE  TABLE IF NOT EXISTS `course_has_course` (
  `course_idcourse` INT NOT NULL ,
  `course_idcourse1` INT NOT NULL ,
  `course_has_relationship` INT NULL DEFAULT 100 ,
  PRIMARY KEY (`course_idcourse`, `course_idcourse1`) ,
  INDEX `fk_course_has_course_course2` (`course_idcourse1` ASC) ,
  INDEX `fk_course_has_course_course1` (`course_idcourse` ASC) ,
  CONSTRAINT `fk_course_has_course_course1`
    FOREIGN KEY (`course_idcourse` )
    REFERENCES `course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_has_course_course2`
    FOREIGN KEY (`course_idcourse1` )
    REFERENCES `course` (`idcourse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `degree_group`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `degree_group` (`iddegree_group`, `name`) VALUES (1, 'Informatik');
INSERT INTO `degree_group` (`iddegree_group`, `name`) VALUES (2, 'Mikrosystemtechnik');

COMMIT;

-- -----------------------------------------------------
-- Data for table `degree`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (1, 1, 'Informatik (Bachelor)');
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (2, 1, 'Informatik (Master)');
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (3, 2, 'Mikrosystemtechnik (Bachelor)');
INSERT INTO `degree` (`iddegree`, `degree_group_iddegree_group`, `name`) VALUES (4, 2, 'Mikrosystemtechnik (Master)');

COMMIT;

-- -----------------------------------------------------
-- Data for table `course`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `course` (`idcourse`, `name`) VALUES (1, 'Datenbanken I');
INSERT INTO `course` (`idcourse`, `name`) VALUES (2, 'Mustererkennung I');
INSERT INTO `course` (`idcourse`, `name`) VALUES (3, 'Informatik I');
INSERT INTO `course` (`idcourse`, `name`) VALUES (4, 'Rechnerarchitektur');
INSERT INTO `course` (`idcourse`, `name`) VALUES (5, 'Simulation');
INSERT INTO `course` (`idcourse`, `name`) VALUES (6, 'Messtechnik (Praktikum)');
INSERT INTO `course` (`idcourse`, `name`) VALUES (7, 'Halbleiter');
INSERT INTO `course` (`idcourse`, `name`) VALUES (8, 'Nanotechnology');

COMMIT;

-- -----------------------------------------------------
-- Data for table `semester`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `semester` (`idsemester`, `name`) VALUES (1, 'WS 2010/11');
INSERT INTO `semester` (`idsemester`, `name`) VALUES (2, 'SS 2011');
INSERT INTO `semester` (`idsemester`, `name`) VALUES (3, 'WS 2011/12');
INSERT INTO `semester` (`idsemester`, `name`) VALUES (4, 'SS 2012');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_type`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam_type` (`idexam_type`, `name`) VALUES (1, 'Klausur');
INSERT INTO `exam_type` (`idexam_type`, `name`) VALUES (2, 'Protokoll');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_sub_type`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam_sub_type` (`idexam_sub_type`, `name`) VALUES (1, 'mit L&ouml;sung');
INSERT INTO `exam_sub_type` (`idexam_sub_type`, `name`) VALUES (2, 'ohne L&ouml;sung');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_status`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (3, 'public');
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (2, 'unchecked');
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (4, 'deleted');
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (1, 'no file uploaded');
INSERT INTO `exam_status` (`idexam_status`, `name`) VALUES (5, 'reported');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_degree`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam_degree` (`idexam_degree`, `name`) VALUES (1, 'Bachelor');
INSERT INTO `exam_degree` (`idexam_degree`, `name`) VALUES (2, 'Master');
INSERT INTO `exam_degree` (`idexam_degree`, `name`) VALUES (3, 'Diploma');

COMMIT;

-- -----------------------------------------------------
-- Data for table `university`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `university` (`iduniversity`, `name`) VALUES (1, 'University of Freiburg');
INSERT INTO `university` (`iduniversity`, `name`) VALUES (2, 'University of Hamburg');
INSERT INTO `university` (`iduniversity`, `name`) VALUES (3, 'University of Karlsruhe');

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `create_date`, `modified_last_date`) VALUES (1, 2, 1, 1, 2, 2, 1, 1, 'Note:', NULL, '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `create_date`, `modified_last_date`) VALUES (2, 1, 2, 1, 2, 2, 2, 2, '-', 'Max Mustermann', '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `create_date`, `modified_last_date`) VALUES (3, 3, 1, 2, 2, 2, 1, 3, 'ohne in seiner blinden Begierde zu sehen, welche Schmerzen und Unannehmlichkeiten seiner deshalb warten', '-', '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `create_date`, `modified_last_date`) VALUES (4, 3, 3, 1, 1, 2, 3, 1, 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus', '', '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `create_date`, `modified_last_date`) VALUES (5, 1, 4, 2, 1, 2, 2, 1, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua', NULL, '2012-03-17 21:56:40', '2012-03-17 21:56:40');

COMMIT;

-- -----------------------------------------------------
-- Data for table `lecturer`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (1, 'Huber', 'D. R.', 'Prof.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (2, 'Schwerkel', 'A.', 'Prof. Dr.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (3, 'Nubra', 'W.', 'Dr.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (4, 'Adams', 'S. G.', 'Prof. Dr.');
INSERT INTO `lecturer` (`idlecturer`, `name`, `first_name`, `degree`) VALUES (5, 'Gerbert', '', '');

COMMIT;

-- -----------------------------------------------------
-- Data for table `document`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `document` (`iddocument`, `exam_idexam`, `extention`, `submit_file_name`, `mime_type`, `file_name`, `deleted`, `reviewed`, `downloads`, `upload_date`, `md5_sum`) VALUES (1, 1, 'txt', 'test_exam1.txt', 'text/plain', '1c4e65b8dcdbe17bfe53d298fc0eab4d', 0, 0, 0, '2012-03-17 21:56:40', 'c9faf47132abf2d237a0949dcbc43030');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `extention`, `submit_file_name`, `mime_type`, `file_name`, `deleted`, `reviewed`, `downloads`, `upload_date`, `md5_sum`) VALUES (2, 2, 'txt', 'test_exam2.txt', 'text/plain', '63db1674260fdb57b7bd3b69b33b1491', 0, 0, 0, '2012-03-17 21:56:40', 'f76097a6979a61b515ccddc4b80ccb59');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `extention`, `submit_file_name`, `mime_type`, `file_name`, `deleted`, `reviewed`, `downloads`, `upload_date`, `md5_sum`) VALUES (3, 3, 'txt', 'test_exam3.txt', 'text/plain', '08d9a05df222a233efa7f27b90a70f05', 0, 0, 0, '2012-03-17 21:56:40', '277095673fb7f948512648bd69c3607a');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `extention`, `submit_file_name`, `mime_type`, `file_name`, `deleted`, `reviewed`, `downloads`, `upload_date`, `md5_sum`) VALUES (4, 4, 'txt', 'test_exam4.txt', 'text/plain', 'cf27f120476f898ed1da059d2e7f5234', 0, 0, 0, '2012-03-17 21:56:40', '7a24edad50b029d78b09d09cbf371b71');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `extention`, `submit_file_name`, `mime_type`, `file_name`, `deleted`, `reviewed`, `downloads`, `upload_date`, `md5_sum`) VALUES (5, 5, 'txt', 'test_exam5.txt', 'text/plain', 'a8c52e04391af64ba6d815199d653783', 0, 0, 0, '2012-03-17 21:56:40', '11fb493d1ac41b079bc4146c56d8cf99');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `extention`, `submit_file_name`, `mime_type`, `file_name`, `deleted`, `reviewed`, `downloads`, `upload_date`, `md5_sum`) VALUES (6, 5, 'txt', 'test_exam5_2.txt', 'text/plain', 'ab6d161805060de7b390e84c8c0019cf', 0, 0, 0, '2012-03-17 21:56:40', 'bbb7c6c5eb85929f239fd229e04fb1bf');

COMMIT;

-- -----------------------------------------------------
-- Data for table `degree_has_course`
-- -----------------------------------------------------
START TRANSACTION;
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
-- Data for table `exam_has_course`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (1, 1);
INSERT INTO `exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (2, 3);
INSERT INTO `exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (3, 5);
INSERT INTO `exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (4, 7);
INSERT INTO `exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (5, 2);
INSERT INTO `exam_has_course` (`exam_idexam`, `course_idcourse`) VALUES (1, 3);

COMMIT;

-- -----------------------------------------------------
-- Data for table `degree_has_lecturer`
-- -----------------------------------------------------
START TRANSACTION;
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
INSERT INTO `degree_has_lecturer` (`degree_iddegree`, `lecturer_idlecturer`) VALUES (3, 5);

COMMIT;

-- -----------------------------------------------------
-- Data for table `exam_has_lecturer`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (1, 3);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (2, 2);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (3, 5);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (4, 4);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 1);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (2, 3);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 3);
INSERT INTO `exam_has_lecturer` (`exam_idexam`, `lecturer_idlecturer`) VALUES (5, 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `course_has_course`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (1, 2, 100);
INSERT INTO `course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (8, 7, 100);
INSERT INTO `course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (5, 6, 100);
INSERT INTO `course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (3, 2, 100);
INSERT INTO `course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (4, 3, 100);
INSERT INTO `course_has_course` (`course_idcourse`, `course_idcourse1`, `course_has_relationship`) VALUES (7, 6, 100);

COMMIT;
