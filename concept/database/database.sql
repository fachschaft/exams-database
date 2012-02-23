SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `exams-database` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `exams-database`;

-- -----------------------------------------------------
-- Table `exams-database`.`degree_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`degree_group` (
  `iddegree_group` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`iddegree_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`degree`
-- -----------------------------------------------------
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
-- Table `exams-database`.`course_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`course_group` (
  `idcourse_group` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idcourse_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`course`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`course` (
  `idcourse` INT NOT NULL ,
  `courses_group_idcourses_group` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idcourse`) ,
  INDEX `fk_courses_courses_group` (`courses_group_idcourses_group` ASC) ,
  CONSTRAINT `fk_courses_courses_group`
    FOREIGN KEY (`courses_group_idcourses_group` )
    REFERENCES `exams-database`.`course_group` (`idcourse_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`semester`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`semester` (
  `idsemester` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idsemester`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_type` (
  `idexam_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`document`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`document` (
  `iddocument` INT NOT NULL AUTO_INCREMENT ,
  `extention` VARCHAR(10) NULL ,
  `submit_file_name` VARCHAR(255) NULL ,
  `data` LONGBLOB NULL ,
  `deleted` BOOLEAN NULL DEFAULT false ,
  PRIMARY KEY (`iddocument`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_sub_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_sub_type` (
  `idexam_sub_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_sub_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam_status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_status` (
  `idexam_status` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`idexam_status`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`exam`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`exam` (
  `idexam` INT NOT NULL AUTO_INCREMENT ,
  `semester_idsemester` INT NOT NULL ,
  `exame_type_idexame_type` INT NOT NULL ,
  `document_iddocument` INT NOT NULL ,
  `exame_sub_type_idexame_sub_type` INT NOT NULL ,
  `exam_status_idexam_status` INT NOT NULL ,
  `comment` TEXT NULL ,
  PRIMARY KEY (`idexam`) ,
  INDEX `fk_exame_semester` (`semester_idsemester` ASC) ,
  INDEX `fk_exame_exame_type` (`exame_type_idexame_type` ASC) ,
  INDEX `fk_exame_document` (`document_iddocument` ASC) ,
  INDEX `fk_exame_exame_sub_type` (`exame_sub_type_idexame_sub_type` ASC) ,
  INDEX `fk_exam_exam_status` (`exam_status_idexam_status` ASC) ,
  CONSTRAINT `fk_exame_semester`
    FOREIGN KEY (`semester_idsemester` )
    REFERENCES `exams-database`.`semester` (`idsemester` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_exame_type`
    FOREIGN KEY (`exame_type_idexame_type` )
    REFERENCES `exams-database`.`exam_type` (`idexam_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_document`
    FOREIGN KEY (`document_iddocument` )
    REFERENCES `exams-database`.`document` (`iddocument` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exame_exame_sub_type`
    FOREIGN KEY (`exame_sub_type_idexame_sub_type` )
    REFERENCES `exams-database`.`exam_sub_type` (`idexam_sub_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_exam_status`
    FOREIGN KEY (`exam_status_idexam_status` )
    REFERENCES `exams-database`.`exam_status` (`idexam_status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`lecturer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`lecturer` (
  `idlecturer` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `first_name` VARCHAR(255) NULL ,
  `degree` VARCHAR(255) NULL ,
  PRIMARY KEY (`idlecturer`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exams-database`.`degree_has_course`
-- -----------------------------------------------------
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
-- Table `exams-database`.`exam_has_course_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `exams-database`.`exam_has_course_group` (
  `exam_idexam` INT NOT NULL ,
  `course_group_idcourse_group` INT NOT NULL ,
  PRIMARY KEY (`exam_idexam`, `course_group_idcourse_group`) ,
  INDEX `fk_exam_has_course_group_exam` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_course_group_course_group` (`course_group_idcourse_group` ASC) ,
  CONSTRAINT `fk_exam_has_course_group_exam`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exams-database`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_has_course_group_course_group`
    FOREIGN KEY (`course_group_idcourse_group` )
    REFERENCES `exams-database`.`course_group` (`idcourse_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `exams-database`.`degree_has_lecturer`
-- -----------------------------------------------------
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



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
