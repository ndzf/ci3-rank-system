<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rank_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function getClassrooms($major, $class_id, $session, $semester)
    {
        $param = [
            "major"     => $major,
            "class_id"  => $class_id,
        ];

        $classrooms = [];
        $sections = $this->db->select("id, name, major, class_id")->where($param)->from("sections")->get()->result_array();

        foreach ($sections as $section) {
            $classroom = [
                "section_id"    => $section["id"],
                "class_id"      => $section["class_id"],
                "semester"      => $semester,
                "session"       => $session
            ];

            array_push($classrooms, $classroom);
        }

        return $classrooms;
    }

    public function getStudents($classrooms)
    {
        $studentsByClass = [];
        $studentFilter = [];

        foreach ($classrooms as $classroom) {
            $students = $this->db->select("student_id, class_id, session, semester, section_id")->where($classroom)->from("legger")->get()->result_array();
            array_push($studentsByClass, $students);
        }

        $studentsClean = [];

        foreach ($studentsByClass as $classrooms) {
            foreach ($classrooms as $student) {
                if (in_array($student["student_id"], $studentFilter)) {
                    continue;
                } else {
                    array_push($studentFilter, $student["student_id"]);
                    array_push($studentsClean, $student);
                }
            }
        }

        return $studentsClean;
    }

    public function filterStudents($students)
    {
        $studentsClean = [];
        $studentFilter = [];

        foreach ($students as $student) {
            if (in_array($student["student_id"], $studentFilter)) {
                continue;
            } else {
                array_push($studentsClean, $student);
                array_push($studentFilter, $student["student_id"]);
            }
        }

        return $studentsClean;
    }

    public function getStudentLeggers($student)
    {
        $leggers = $this->db->select("student_id, cognition_mark, practice_mark, session, semester, section_id")->from('legger')->where($student)->get()->result_array();
        return $leggers;
    }

    public function getLeggersBySection($param)
    {
        return $this->db->select("student_id, class_id, session, semester, section_id")->from('legger')->where($param)->get()->result_array();
    }
}
