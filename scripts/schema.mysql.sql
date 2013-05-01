SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `degree_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `degree_group` ;

CREATE  TABLE IF NOT EXISTS `degree_group` (
  `iddegree_group` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `name_unescaped` VARCHAR(255) NULL ,
  `order` INT NOT NULL DEFAULT 0 ,
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
  `name_unescaped` VARCHAR(255) NULL ,
  `order` INT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`iddegree`) ,
  INDEX `fk_degree_degree_group_idx` (`degree_group_iddegree_group` ASC) ,
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
  `name_unescaped` VARCHAR(255) NULL ,
  `name_short` VARCHAR(14) NULL ,
  `order` INT NOT NULL DEFAULT 1 ,
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
  `begin_time` DATETIME NULL ,
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
  `order` VARCHAR(45) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`idexam_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `exam_sub_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_sub_type` ;

CREATE  TABLE IF NOT EXISTS `exam_sub_type` (
  `idexam_sub_type` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `order` INT NOT NULL DEFAULT 0 ,
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
  `order` INT NOT NULL DEFAULT 0 ,
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
  `downloads` INT NOT NULL DEFAULT 0 ,
  `create_date` TIMESTAMP NULL ,
  `modified_last_date` TIMESTAMP NULL ,
  PRIMARY KEY (`idexam`) ,
  INDEX `fk_exame_semester_idx` (`semester_idsemester` ASC) ,
  INDEX `fk_exame_exame_type_idx` (`exam_type_idexam_type` ASC) ,
  INDEX `fk_exame_exame_sub_type_idx` (`exam_sub_type_idexam_sub_type` ASC) ,
  INDEX `fk_exam_exam_status_idx` (`exam_status_idexam_status` ASC) ,
  INDEX `fk_exam_exam_degree1_idx` (`exam_degree_idexam_degree` ASC) ,
  INDEX `fk_exam_university1_idx` (`university_iduniversity` ASC) ,
  INDEX `fk_exam_degree1_idx` (`degree_iddegree` ASC) ,
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
  `name_unescaped` VARCHAR(255) NULL ,
  `first_name_unescaped` VARCHAR(255) NULL ,
  `degree_unescaped` VARCHAR(255) NULL ,
  `order` INT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`idlecturer`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `document`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `document` ;

CREATE  TABLE IF NOT EXISTS `document` (
  `iddocument` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `display_name` VARCHAR(255) NULL ,
  `file_name` VARCHAR(255) NULL ,
  `submit_file_name` VARCHAR(255) NULL ,
  `extention` VARCHAR(10) NULL ,
  `mime_type` VARCHAR(255) NULL ,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0 ,
  `reviewed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `collection` TINYINT(1) NOT NULL DEFAULT 0 ,
  `downloads` INT NOT NULL DEFAULT 0 ,
  `upload_date` TIMESTAMP NULL ,
  `delete_date` TIMESTAMP NULL ,
  `md5_sum` VARCHAR(32) NULL ,
  PRIMARY KEY (`iddocument`) ,
  INDEX `fk_document_exam1_idx` (`exam_idexam` ASC) ,
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
  INDEX `fk_degree_has_course_degree_idx` (`degree_iddegree` ASC) ,
  INDEX `fk_degree_has_course_course_idx` (`course_idcourse` ASC) ,
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
  INDEX `fk_exam_log_exam_idx` (`exam_idexam` ASC) ,
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
  INDEX `fk_exam_has_course_group_exam_idx` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_course_group_course1_idx` (`course_idcourse` ASC) ,
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
  INDEX `fk_degree_has_lecturer_degree_idx` (`degree_iddegree` ASC) ,
  INDEX `fk_degree_has_lecturer_lecturer_idx` (`lecturer_idlecturer` ASC) ,
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
  INDEX `fk_exam_has_lecturer_exam_idx` (`exam_idexam` ASC) ,
  INDEX `fk_exam_has_lecturer_lecturer_idx` (`lecturer_idlecturer` ASC) ,
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
  INDEX `fk_course_has_course_course2_idx` (`course_idcourse1` ASC) ,
  INDEX `fk_course_has_course_course1_idx` (`course_idcourse` ASC) ,
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


-- -----------------------------------------------------
-- Table `exam_download_statistic_day`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_download_statistic_day` ;

CREATE  TABLE IF NOT EXISTS `exam_download_statistic_day` (
  `idexam_download_statistic_day` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `date` DATE NOT NULL ,
  `downloads` INT NOT NULL ,
  PRIMARY KEY (`idexam_download_statistic_day`) ,
  INDEX `fk_exam_download_statistic_day_exam1_idx` (`exam_idexam` ASC) ,
  UNIQUE INDEX `exam_idexam_date_UNIQUE` (`exam_idexam` ASC, `date` ASC) ,
  CONSTRAINT `fk_exam_download_statistic_day_exam1`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `document_download_statistic_day`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `document_download_statistic_day` ;

CREATE  TABLE IF NOT EXISTS `document_download_statistic_day` (
  `iddocument_download_statistic_day` INT NOT NULL AUTO_INCREMENT ,
  `document_iddocument` INT NOT NULL ,
  `date` DATE NOT NULL ,
  `downloads` INT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`iddocument_download_statistic_day`) ,
  INDEX `fk_document_download_statistic_day_document1_idx` (`document_iddocument` ASC) ,
  UNIQUE INDEX `document_iddocument_date_UNIQUE` (`document_iddocument` ASC, `date` ASC) ,
  CONSTRAINT `fk_document_download_statistic_day_document1`
    FOREIGN KEY (`document_iddocument` )
    REFERENCES `document` (`iddocument` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `log` ;

CREATE  TABLE IF NOT EXISTS `log` (
  `idlog` INT NOT NULL AUTO_INCREMENT ,
  `message` TEXT NULL ,
  PRIMARY KEY (`idlog`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_privilege_mapping`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_privilege_mapping` ;

CREATE  TABLE IF NOT EXISTS `user_privilege_mapping` (
  `iduser_privilege_mapping` INT NOT NULL AUTO_INCREMENT ,
  `authadapter` VARCHAR(255) NULL ,
  `identity` VARCHAR(255) NULL ,
  `role` VARCHAR(255) NULL ,
  PRIMARY KEY (`iduser_privilege_mapping`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `api_master_key`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `api_master_key` ;

CREATE  TABLE IF NOT EXISTS `api_master_key` (
  `idapi_master_key` INT NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`idapi_master_key`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `api_temporary_key`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `api_temporary_key` ;

CREATE  TABLE IF NOT EXISTS `api_temporary_key` (
  `idapi_temporary_key` INT NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(255) NOT NULL ,
  `expire_time` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`idapi_temporary_key`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;