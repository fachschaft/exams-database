-- scripts/schema.mysql.sql
--
-- You will need load your database schema with this SQL.
 
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
  `extention` VARCHAR(10) NULL ,
  `submit_file_name` VARCHAR(255) NULL ,
  `data` LONGBLOB NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  `exam_idexam` INT NOT NULL ,
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