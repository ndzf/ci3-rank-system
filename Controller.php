<?php 

public function ranking()
    {
        $this->load->model("Rank_model");
        $this->load->helper("average_score");

        $session = 1;
        $semester = "Even";
        $major = "MIPA";
        $section_id = "1";
        $class_id = 1;
        $students = null;

        if ($section_id) {
            $param = [
                "session"       => $session,
                "semester"      => $semester,
                "section_id"    => $section_id,
            ];
            $leggers = $this->Rank_model->getLeggersBySection($param);
            $students = $this->Rank_model->filterStudents($leggers);
        } else {
            $classrooms = $this->Rank_model->getClassrooms($major, $class_id, $session, $semester);
            $students  = $this->Rank_model->getStudents($classrooms);
        }

        $data = [];

        foreach ($students as $student) {
            $leggers = $this->Rank_model->getStudentLeggers($student);

            $cognition_mark = get_cognition_average_score($leggers);
            $practice_mark = get_practice_average_score($leggers);
            $average = ($cognition_mark + $practice_mark) / 2;

            $array = [
                "student_id"            => $student["student_id"],
                "class_id"              => $student["class_id"],
                "section_id"            => $student["section_id"],
                "average"               => $average,
            ];

            array_push($data, $array);
        }

        usort($data, function ($item1, $item2) {
            return $item2['average'] <=> $item1['average'];
        });

        var_dump($data);

    }
