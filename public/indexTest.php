<?php

$teachersIds = [
    1 => 'Jan Nowak',
    2 => 'Grazyna Abc'];
$studentsIds = [
    1 => 'Piotr Kowalski',
    2 => 'Anna Aaa',
    3 => 'Paweł Bbb'
];
$subjects = ['Biologia', 'Chemia'];
$grades = [
    1 => [['Biologia', 4],['Chemia', 3]],
    2 => [['Biologia', 5],['Chemia', 4]],
    3 => [['Biologia', 3],['Chemia', 3]]
    ];
$behaviours =
    [
        1 => 4,
        2 => 5,
        3 => 3,
    ];

function wypiszWszystkieOcenyUcznia($students, $grades, $imieNazwisko){

    $studentId = null;
    foreach ($students as $id => $student){
     if ($student == $imieNazwisko){
         $studentId = $id ;
     }
    }

    foreach ($grades[$studentId] as $index =>$grade){

        echo ' ' . $grade[1];
    }
};




function porownajKtoMialLepszeZachowanie($imieNazwisko1, $imieNazwisko2){};

function porownajKtoMialLepszaSrednia($students, $grades, $imieNazwisko1, $imieNazwisko2){

    $srednia1 = obliczSrednia($students, $grades, $imieNazwisko1);
    $srednia2 = obliczSrednia($students, $grades, $imieNazwisko2);

    if ($srednia1 > $srednia2){
        echo $imieNazwisko1;
    }
    else{
        echo $imieNazwisko2;
    }

};
function obliczSrednia($students, $grades, $imieNazwisko){

    $studentId = null;
    foreach ($students as $id => $student){
        if ($student == $imieNazwisko){
            $studentId = $id ;
        }
    }

    $sum =0;

    foreach ($grades[$studentId] as $index =>$grade){

        $sum = $grade[1] + $sum;

    }

    return $sum / count($grades[$studentId]);
};

//porownajKtoMialLepszaSrednia($studentsIds, $grades, 'Paweł Bbb', 'Anna Aaa');


class Student extends Person {

    /**
     * @var Grade[]
     */
    public $grades;

    public function __construct($imie, $nazwisko, $grades)
    {
        parent::__construct($imie, $nazwisko);
        $this->grades = $grades;
    }

    public function obliczSrednia(){

        $sum = 0;
        foreach ($this->grades as $grade) {

            $sum+= $grade->ocena();
        }
        return $sum / count($this->grades);
    }

    public function czyMaLepszaSrednia(Student $innyUczen){

        if($this->obliczSrednia() > $innyUczen->obliczSrednia()){
            echo 'Tak';
        }
        else{
            echo 'Nie';
        }

    }
}

class Teacher extends Person {

    public $subjects;

    public function __construct($imie, $nazwisko, $subjects)
    {
        parent::__construct($imie, $nazwisko);
        $this->subjects = $subjects;
    }

}

class Grade {


    private $grade;
    private $subject;
    private $teacher;

    public function setTeacher(Teacher $teacher){

        $this->teacher = $teacher;
    }

    public function wypisz()
    {
        echo 'ocena' . $this->grade . 'z przedmiotu' . $this->subject . 'wystawiona przez' . $this->teacher->nazwisko;
    }

    public function __construct($grade, $subject, Teacher $teacher=null)
    {
        $this->subject = $subject;
        $this->teacher = $teacher;
        $this->grades = $grade;
    }

    public function ocena()
    {
       return $this->grade;
    }


}
class Behaviour {

}

abstract class Person{

    public $imie;
    public $nazwisko;

    public function __construct($imie, $nazwisko)
    {
        $this->imie = $imie;
        $this->nazwisko = $nazwisko;
    }

    public function wypisz()
    {
        echo '<h1>' . $this->imie . ' ' . $this->nazwisko . '</h1>';
    }
}


$teacher1 = new Teacher('Jan', 'Nowak', 'Biologia');
$teacher2 = new Teacher('Maria', 'Ddd', 'Chemia');
$grade1 = new Grade(1, 'Biologia');
$grade2 = new Grade(4, 'Biologia', $teacher2);
$grade3 = new Grade(3, 'Biologia', $teacher2);
$student1 = new Student('Piotr', 'Abc', [$grade1, $grade2]);
$student2 = new Student('Adam', 'Ccc', [$grade3]);

$grade1->setTeacher($teacher1);
$grade1->wypisz();


//echo $student2->czyMaLepszaSrednia($student1);
//echo $student2->obliczSrednia();
