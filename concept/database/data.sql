SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

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