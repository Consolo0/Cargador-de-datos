<?php
include_once 'Functions.php';
include_once 'DataLoader.php';

$FirstArchivoNullIndex = [8, 11, 12, 19];
$SecondArchivoNullIndex = [5,6,7,8,9,10,11,12,13,14];
//tmb esta apellido paterno y materno del profe y los nombres
//33661
$BadFirstlines = file("csvArchives/Estudiantes.csv", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$BadSecondlines = file("csvArchives/Trabajadores.csv", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


$OneArchiveLoader = new DataLoader("csvArchives/Estudiantes.csv", $FirstArchivoNullIndex);
$TwoArchiveLoader = new DataLoader("csvArchives/Trabajadores.csv", $SecondArchivoNullIndex);
$FirstArchiveGoodLines = $OneArchiveLoader->ReadData();
$SecondArchiveGoodLines = $TwoArchiveLoader->ReadData();

$HashsetPersonas = [];
$MatrizPersonas = [];

$HashsetEstudiantes = [];
$MatrizEstudiantes = [];

$MatrizNotas = [];

$HashsetCursos = [];
$MatrizCursos = [];
$DicCursosSecciones = [];
$SeccionesVisitadas = [];

for ($i = 0; $i < count($FirstArchiveGoodLines); $i++){
    
    //RUN
    if (!in_array($FirstArchiveGoodLines[$i][4]."-".$FirstArchiveGoodLines[$i][5], $HashsetEstudiantes)){
        $HashsetEstudiantes[] = $FirstArchiveGoodLines[$i][4]."-".$FirstArchiveGoodLines[$i][5];
        $completeName = $FirstArchiveGoodLines[$i][6] . " ". $FirstArchiveGoodLines[$i][7]. " ". $FirstArchiveGoodLines[$i][8];

        $MatrizEstudiantes[] = [
            $FirstArchiveGoodLines[$i][0], $FirstArchiveGoodLines[$i][10], $FirstArchiveGoodLines[$i][1],
            $FirstArchiveGoodLines[$i][20], $FirstArchiveGoodLines[$i][21],$FirstArchiveGoodLines[$i][22], $completeName, $FirstArchiveGoodLines[$i][4]];
    }

    //RUN
    if (!in_array($FirstArchiveGoodLines[$i][4]."-".$FirstArchiveGoodLines[$i][5], $HashsetPersonas)){
        $HashsetPersonas[] = $FirstArchiveGoodLines[$i][4]."-".$FirstArchiveGoodLines[$i][5];
        $MatrizPersonas[] = [
            $FirstArchiveGoodLines[$i][4],$FirstArchiveGoodLines[$i][5],$FirstArchiveGoodLines[$i][6],$FirstArchiveGoodLines[$i][7],
            $FirstArchiveGoodLines[$i][8],$FirstArchiveGoodLines[$i][9],"X",$FirstArchiveGoodLines[$i][11],$FirstArchiveGoodLines[$i][12]
        ];
    }

    //sigla curso
    if (!in_array($FirstArchiveGoodLines[$i][14], $HashsetCursos) && $FirstArchiveGoodLines[$i][14] != "X" && $FirstArchiveGoodLines[$i][15] != "X" && $FirstArchiveGoodLines[$i][16] != "X" && $FirstArchiveGoodLines[$i][17] != "X"){
        $HashsetCursos[] = $FirstArchiveGoodLines[$i][14];
        $MatrizCursos[] = [$FirstArchiveGoodLines[$i][14], $FirstArchiveGoodLines[$i][15], 1, $FirstArchiveGoodLines[$i][17]];
        $DicCursosSecciones[$FirstArchiveGoodLines[$i][14]] = 1;

        $SeccionesVisitadas[$FirstArchiveGoodLines[$i][14]] = [$FirstArchiveGoodLines[$i][16]];
    }

    //por si encontramos una nueva seccion
    if (in_array($FirstArchiveGoodLines[$i][14], $HashsetCursos) && !in_array($FirstArchiveGoodLines[$i][16], $SeccionesVisitadas[$FirstArchiveGoodLines[$i][14]])){

        $DicCursosSecciones[$FirstArchiveGoodLines[$i][14]] = $DicCursosSecciones[$FirstArchiveGoodLines[$i][14]] + 1;

        for ($j = 0; $j < count($MatrizCursos); $j++){
            if ($MatrizCursos[$j][0] == $FirstArchiveGoodLines[$i][14]){
                $MatrizCursos[$j][2] = $DicCursosSecciones[$FirstArchiveGoodLines[$i][14]];
            }
        }
        $SeccionesVisitadas[$FirstArchiveGoodLines[$i][14]][] = $FirstArchiveGoodLines[$i][16];
    }

    //sigla curso
    if ($FirstArchiveGoodLines[$i][10] != "X" && $FirstArchiveGoodLines[$i][14] != "X" && $FirstArchiveGoodLines[$i][13] != "X" && $FirstArchiveGoodLines[$i][18] != "X"){
        $MatrizNotas[] = [
        $FirstArchiveGoodLines[$i][10], $FirstArchiveGoodLines[$i][14], $FirstArchiveGoodLines[$i][13], 
        $FirstArchiveGoodLines[$i][18], $FirstArchiveGoodLines[$i][19], $FirstArchiveGoodLines[$i][4], $FirstArchiveGoodLines[$i][15]];
        #agregamos run y curso al final

    }
}

$MatrizAdministrativo = [];

$MatrizProfesor = [];

for ($i=0; $i<count($SecondArchiveGoodLines); $i++){
    //RUN
    if (!in_array($SecondArchiveGoodLines[$i][0]."-".$SecondArchiveGoodLines[$i][1], $HashsetPersonas)){
        $HashsetPersonas[] = $SecondArchiveGoodLines[$i][0]."-".$SecondArchiveGoodLines[$i][1];

        $nombreCompleto = $SecondArchiveGoodLines[$i][4] . " " . $SecondArchiveGoodLines[$i][2] . " " . $SecondArchiveGoodLines[$i][3];

        $MatrizPersonas[] = [$SecondArchiveGoodLines[$i][0], $SecondArchiveGoodLines[$i][1], $SecondArchiveGoodLines[$i][4], $SecondArchiveGoodLines[$i][2], $SecondArchiveGoodLines[$i][3], $nombreCompleto, $SecondArchiveGoodLines[$i][7], $SecondArchiveGoodLines[$i][5], $SecondArchiveGoodLines[$i][6]];
    }

    if ($SecondArchiveGoodLines[$i][14] != ""){
        $MatrizAdministrativo[] = [$SecondArchiveGoodLines[$i][0], $SecondArchiveGoodLines[$i][1], $SecondArchiveGoodLines[$i][14]];
    }


    //si hay profesor en cargo o jerarquia o jerarquia no es nulo,lo ponemos en profesor
    if (stripos(strtolower($SecondArchiveGoodLines[$i][14]), "profesor") != false || stripos(strtolower($SecondArchiveGoodLines[$i][13]), "profesor") != false || $SecondArchiveGoodLines[$i][13] != ""){

        $jornada = ($SecondArchiveGoodLines[$i][9] == "" && $SecondArchiveGoodLines[$i][10] != "") ? $SecondArchiveGoodLines[$i][10] : $SecondArchiveGoodLines[$i][9];

        $MatrizProfesor[] = [
            $SecondArchiveGoodLines[$i][0], $SecondArchiveGoodLines[$i][1], $SecondArchiveGoodLines[$i][8], $jornada, 
            $SecondArchiveGoodLines[$i][11], $SecondArchiveGoodLines[$i][12], $SecondArchiveGoodLines[$i][13], $SecondArchiveGoodLines[$i][14]];
    }

}
$orden = "1";

while ($orden != "3"){
    echo "[1] Carga academica acumuluda\n[2] Lista de cursos\n[3] EXIT\n";
    echo "Escoja una opcion: ";
    
    $orden = trim(fgets(STDIN));

    if ($orden == "1"){
        echo "Ingrese el RUN que desee buscar: ";
        $RUN = trim(fgets(STDIN));
        cargaAcademicaAcumuluda($MatrizNotas, $RUN);
    }

    elseif ($orden == "2"){
        //hacemos esto pq curso por curso nos demoramos demasiado
        echo "Ingrese la sigla del curso que desee buscar: ";
        $sigla = trim(fgets(STDIN));
        print($sigla);
        echo"\n";
        listaCursos($MatrizNotas, $MatrizEstudiantes, $sigla);
    }

    elseif($orden != "3"){
        echo "\nPor favor ingrese un numero del 1 al 3\n\n";
    }
}

/*print_r($MatrizPersonas[5]);
print_r($MatrizEstudiantes[5]);
print_r($MatrizCursos[5]);
print_r($MatrizNotas[5]);
print_r($MatrizAdministrativo[5]);
print_r($MatrizProfesor[5]);*/

/*
Matriz 1 persona -> RUN + DV
Matriz 2 estudiante -> Hashset tiene RUN + DV
Matriz 3 profesor -> Hashset contiene RUN + DV
Matriz 4 administrativo -> RUN + DV
Matriz 5 cursos -> sigla curso
Matriz 6 notas -> sigla curso
*/

?>