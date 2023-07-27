<?php
    // Fetch Students
    class Students {
        private $filePath;

        public function __construct($filePath) {
            $this->filePath = $filePath;
        }

        public function loadData() {
            $jsonData = file_get_contents($this->filePath);
            return json_decode($jsonData, true);
        }
    }

    // Sort Class
    class Sorter {
        // Sort the students by their average
        public function sortByAverageGrade($studentsList) {
            usort($studentsList, function($a, $b) {
                $averageA = ($a['physics'] + $a['maths'] + $a['english']) / 3;
                $averageB = ($b['physics'] + $b['maths'] + $b['english']) / 3;
                return $averageB - $averageA;
            });

            //Returning the list with only the student's name, gender, and average
            $sortedStudents = array_map(function($student) {
                $average = ($student['physics'] + $student['maths'] + $student['english']) / 3;
                return [
                    'name' => $student['name'],
                    'gender' => $student['gender'],
                    'average' => $average,
                ];
            }, $studentsList);
    
            return $sortedStudents;
        }
    }

    // Filter Class
    class Filter {
        // Filter the students by gender
        public function filterByGender($studentsList, $gender) {
            return array_filter($studentsList, function($student) use ($gender) {
                return $student['gender'] === $gender;
            });
        }
    }

    //Display the students
    class Display {
        public function displayStudents($studentsList) {
            foreach ($studentsList as $student) {
                echo "<h2>Name: {$student['name']}, Average: {$student['average']}</h2>";
            }
        }
    }

    // Dependency Injection Container
    class Container {
        private $dependencies = [];

        public function register($name, $dependency) {
            $this->dependencies[$name] = $dependency;
        }

        public function resolve($name) {
            if (!isset($this->dependencies[$name])) {
                throw new Exception("Dependency not found: {$name}");
            }
            return $this->dependencies[$name];
        }
    }

    function runCode(){
        $container = new Container();

        // Register the dependencies
        $container->register('dataSource', new Students('students.json'));
        $container->register('sorter', new Sorter());
        $container->register('filter', new Filter());
        $container->register('display', new Display());

        //Resolve the dependencies
        $dataSource = $container->resolve('dataSource');
        $sorter = $container->resolve('sorter');
        $filter = $container->resolve('filter');
        $display = $container->resolve('display');

        // Load the students from datasource
        $studentsLists = $dataSource->loadData();

        // Sort the students by their average
        $sortedStudents = $sorter->sortByAverageGrade($studentsLists);
        
        // Filter the students by gender
        $filteredStudents = $filter->filterByGender($sortedStudents, 'Male');

        $display->displayStudents($filteredStudents);
    }

    runCode();
?>