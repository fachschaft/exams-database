-- -----------------------------------------------------
-- Table `log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `log` ;

CREATE  TABLE IF NOT EXISTS `log` (
  `idlog` INT NOT NULL ,
  `message` TEXT NULL ,
  PRIMARY KEY (`idlog`) )
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
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `downloads`, `create_date`, `modified_last_date`) VALUES (1, 2, 1, 1, 2, 2, 1, 1, 'Note:', NULL, 0, '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `downloads`, `create_date`, `modified_last_date`) VALUES (2, 1, 2, 1, 2, 2, 2, 2, '-', 'Max Mustermann', 0, '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `downloads`, `create_date`, `modified_last_date`) VALUES (3, 3, 1, 2, 2, 2, 1, 3, 'ohne in seiner blinden Begierde zu sehen, welche Schmerzen und Unannehmlichkeiten seiner deshalb warten', '-', 0, '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `downloads`, `create_date`, `modified_last_date`) VALUES (4, 3, 3, 1, 1, 2, 3, 1, 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus', '', 0, '2012-03-17 21:56:40', '2012-03-17 21:56:40');
INSERT INTO `exam` (`idexam`, `degree_iddegree`, `semester_idsemester`, `exam_type_idexam_type`, `exam_sub_type_idexam_sub_type`, `exam_status_idexam_status`, `exam_degree_idexam_degree`, `university_iduniversity`, `comment`, `autor`, `downloads`, `create_date`, `modified_last_date`) VALUES (5, 1, 4, 2, 1, 2, 2, 1, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua', NULL, 0, '2012-03-17 21:56:40', '2012-03-17 21:56:40');

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
INSERT INTO `document` (`iddocument`, `exam_idexam`, `display_name`, `file_name`, `submit_file_name`, `extention`, `mime_type`, `deleted`, `reviewed`, `collection`, `downloads`, `upload_date`, `md5_sum`) VALUES (1, 1, 'test exam1', '1c4e65b8dcdbe17bfe53d298fc0eab4d', 'test_exam1.txt', 'txt', 'text/plain', 0, 0, 0, 0, '2012-03-17 21:56:40', 'c9faf47132abf2d237a0949dcbc43030');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `display_name`, `file_name`, `submit_file_name`, `extention`, `mime_type`, `deleted`, `reviewed`, `collection`, `downloads`, `upload_date`, `md5_sum`) VALUES (2, 2, 'test exam2', '63db1674260fdb57b7bd3b69b33b1491', 'test_exam2.txt', 'txt', 'text/plain', 0, 0, 0, 0, '2012-03-17 21:56:40', 'f76097a6979a61b515ccddc4b80ccb59');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `display_name`, `file_name`, `submit_file_name`, `extention`, `mime_type`, `deleted`, `reviewed`, `collection`, `downloads`, `upload_date`, `md5_sum`) VALUES (3, 3, 'test exam3', '08d9a05df222a233efa7f27b90a70f05', 'test_exam3.txt', 'txt', 'text/plain', 0, 0, 0, 0, '2012-03-17 21:56:40', '277095673fb7f948512648bd69c3607a');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `display_name`, `file_name`, `submit_file_name`, `extention`, `mime_type`, `deleted`, `reviewed`, `collection`, `downloads`, `upload_date`, `md5_sum`) VALUES (4, 4, 'test exam4', 'cf27f120476f898ed1da059d2e7f5234', 'test_exam4.txt', 'txt', 'text/plain', 0, 0, 0, 0, '2012-03-17 21:56:40', '7a24edad50b029d78b09d09cbf371b71');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `display_name`, `file_name`, `submit_file_name`, `extention`, `mime_type`, `deleted`, `reviewed`, `collection`, `downloads`, `upload_date`, `md5_sum`) VALUES (5, 5, 'test exam5', 'a8c52e04391af64ba6d815199d653783', 'test_exam5.txt', 'txt', 'text/plain', 0, 0, 0, 0, '2012-03-17 21:56:40', '11fb493d1ac41b079bc4146c56d8cf99');
INSERT INTO `document` (`iddocument`, `exam_idexam`, `display_name`, `file_name`, `submit_file_name`, `extention`, `mime_type`, `deleted`, `reviewed`, `collection`, `downloads`, `upload_date`, `md5_sum`) VALUES (6, 5, 'test exam5_2', 'ab6d161805060de7b390e84c8c0019cf', 'test_exam5_2.txt', 'txt', 'text/plain', 0, 0, 0, 0, '2012-03-17 21:56:40', 'bbb7c6c5eb85929f239fd229e04fb1bf');

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
