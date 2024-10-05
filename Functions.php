<?php

function correctFormatArchiveOne($Lines){
    $cond_1 = is_string($Lines[0]) && is_string($Lines[1]) && is_string($Lines[2]);
    $cond_4 = $Lines[3] == "N" || $Lines[3] == "S" ||  $Lines[3] == "X";
    $cond_5 = is_numeric($Lines[4]) || $Lines[4] == "X";
    $cond_6 = (is_numeric($Lines[5]) && strlen($Lines[5]) == 1) || $Lines[5] == "X";
    $cond_7 = is_string($Lines[6]) && is_string($Lines[7]) && is_string($Lines[8]) && is_string($Lines[9]);
    $cond_11 = is_numeric($Lines[10]) || $Lines[10] == "X";
    $cond_12 = is_string($Lines[11]) && is_string($Lines[12]) && is_string($Lines[13]) && is_string($Lines[14]) && is_string($Lines[15]);
    $cond_17 = is_numeric($Lines[16]) || $Lines[16] == "X";
    $cond_18 = is_numeric($Lines[17]) || $Lines[17] == "X";
    $cond_19 = is_string($Lines[18]);
    $cond_20 = is_numeric($Lines[19]) || $Lines[19] == "";
    //var_dump($cond_20);
    //$cond_21 = is_numeric($Lines[20]) || $Lines[20] == "X"; pq hay INGRESO y numeros
    $cond_21 = true;
    $cond_22 = is_string($Lines[21]);
    $cond_23 = is_string($Lines[22]);
    return $cond_1 && $cond_4 && $cond_5 && $cond_6 && $cond_7 && $cond_11 && $cond_12 && $cond_17 && $cond_18 && $cond_19 && $cond_20 && $cond_21 && $cond_22 && $cond_23;

}

function correctFormatArchiveTwo($Lines){

    $cond_1 = is_numeric($Lines[0]) || $Lines[0] == "X";
    $cond_2 = (is_numeric($Lines[1]) && strlen($Lines[1]) == 1) || $Lines[1] == "X";
    $cond_3 = is_string($Lines[2+3]) && is_string($Lines[3+3]);
    $cond_5 = (strlen($Lines[4+3]) == 9 || strlen($Lines[4+3]) == 0) && is_numeric($Lines[4+3]);
    $cond_6 = is_string($Lines[5+3]) && $Lines[6+3]=="diurno" && $Lines[7+3] == "vespertino" && is_string($Lines[8+3]) && is_string($Lines[9+3]) && is_string($Lines[10+3]) && is_string($Lines[11+3]);

    return $cond_1 && $cond_2 && $cond_3 && $cond_5 && $cond_6;
}

function cargaAcademicaAcumuluda($Notas, $RUN){
    $infoPorPeriodo = []; //periodo->[[nota1,nombre1,sigla1], [nota2,...,...]]
    //$siglaYaVisitada = [];

    for ($i=0; $i<count($Notas); $i++){
        if ($RUN == $Notas[$i][5]){
            //print("Hola encontre el RUN\n");
            
            $sigla = $Notas[$i][1];
            $curso = $Notas[$i][6];
            $nota = $Notas[$i][4];
            $calificacion = $Notas[$i][3];
            $periodo = $Notas[$i][2];

            //if (!in_array($sigla, $siglaYaVisitada)
                //print("Encontre una sigla\n");
            if (!array_key_exists($periodo, $infoPorPeriodo)){
                $infoPorPeriodo[$periodo] = [[$sigla, $curso, $nota, $calificacion]];
            }

            else{
                $infoPorPeriodo[$periodo][] = [$sigla, $curso, $nota, $calificacion];
            }
            //$siglaYaVisitada[] = $sigla;
        }
    }

    //ordenamos $infoPorPeriodo

    $keys = array_keys($infoPorPeriodo);
    //print_r($infoPorPeriodo);
    usort($keys, function($a, $b){
        list($yearA, $semesterA) = explode("-", $a);
        list($yearB, $semesterB) = explode("-", $b);

        if ($yearA != $yearB) {
            return strcmp($yearA, $yearB);
        }
        return strcmp($semesterA, $semesterB);
    });

    imprimirInfoPorPeriodo($infoPorPeriodo, $keys);
}

// periodo mas chico 2005-01 y mas largo 2024-01
function imprimirInfoPorPeriodo($infoPorPeriodo, $keys){
    $globalSumaNotas = 0;
    $globalCantNotas = 0;

    echo "\n";
    for ($i=0;$i<count($keys);$i++){
        $periodo = $keys[$i];
        $listaDeListaConNotasPorPeriodo = $infoPorPeriodo[$periodo];
        
        $sumaNotas = 0;
        $cantNotas = 0;
        echo "Periodo: {$periodo}\n";

        for ($j=0;$j<count($listaDeListaConNotasPorPeriodo);$j++){
            $sigla = $listaDeListaConNotasPorPeriodo[$j][0];
            $curso = $listaDeListaConNotasPorPeriodo[$j][1]; 
            $nota = $listaDeListaConNotasPorPeriodo[$j][2]; 
            $calificacion = $listaDeListaConNotasPorPeriodo[$j][3];
            echo"\t\t Sigla: {$sigla}\n";
            echo"\t\t Curso: {$curso}\n";
            echo"\t\t Nota: {$nota}\n";
            echo"\t\t Calificacion: {$calificacion}\n\n";

            if ($nota != ""){
                $sumaNotas += $nota;
                $cantNotas++;
            }
        }

        if($cantNotas == 0){
            echo "\tTodas las notas en el periodo {$periodo} son nulas";
        }
        else{
            
            $promedioPeriodo = round($sumaNotas/$cantNotas, 2);
            echo "\tPromedio de Periodo {$periodo}: {$promedioPeriodo}\n";
        }
        echo"\n";
        $globalSumaNotas += $sumaNotas;
        $globalCantNotas += $cantNotas;

    }
    if ($globalCantNotas == 0){
        echo "Todas las notas del estudiante son nulas\n\n";
    }

    else{
        $promedioGlobal = round($globalSumaNotas/$globalCantNotas, 2);
        echo "Promedio Global: {$promedioGlobal}\n";
    }
} 

function listaCursos($Notas, $Estudiante, $sigla){
    // lista -> periodo => [[cohorte, nombre completo, RUN, numAlumno], [..., ..., ..., ...]]

    $alumnosQueDieronElCurso = [];

    for ($j = 0; $j < count($Notas); $j++){
        if ($Notas[$j][1] == $sigla){
            $periodo = $Notas[$j][2];
            $RUN = $Notas[$j][5];
            

            for ($i = 0; $i < count($Estudiante); $i++){
                $cohorte = $Estudiante[$i][0];
                $nombreCompleto = $Estudiante[$i][6];
                $RUNestudiante = $Estudiante[$i][7];
                $numeroAlumno = $Estudiante[$i][1];
                
                if ($RUN == $RUNestudiante){

                    if (!array_key_exists($periodo, $alumnosQueDieronElCurso)){
                        $alumnosQueDieronElCurso[$periodo] = [[$cohorte, $nombreCompleto, $RUN, $numeroAlumno]];
                    }

                    else{
                        $alumnosQueDieronElCurso[$periodo][] = [$cohorte, $nombreCompleto, $RUN, $numeroAlumno];
                    }
                }
            }
        }
    }

    $keys = array_keys($alumnosQueDieronElCurso);
    //print_r($infoPorPeriodo);
    usort($keys, function($a, $b){
        list($yearA, $semesterA) = explode("-", $a);
        list($yearB, $semesterB) = explode("-", $b);

        if ($yearA != $yearB) {
            return strcmp($yearA, $yearB);
        }
        return strcmp($semesterA, $semesterB);
    });

    imprimirCurso($keys, $alumnosQueDieronElCurso, $sigla);
    return;

    
}

function imprimirCurso($key, $alumnos, $sigla){
    echo "Sigla del curso: {$sigla}\n\n";
    for($i=0;$i<count($key); $i++){
        $periodo = $key[$i];
        echo "\tPERIODO: {$periodo}\n\n";

        for($j=0; $j<count($alumnos[$periodo]); $j++){
            
            echo "\t\tCohorte: {$alumnos[$periodo][$j][0]}\n";
            echo "\t\tNombre Completo: {$alumnos[$periodo][$j][1]}\n";
            echo "\t\tRUN: {$alumnos[$periodo][$j][2]}\n";
            echo "\t\tNumero De Alumno: {$alumnos[$periodo][$j][3]}\n\n";
        }        
    }
}

/*(
    [0] => Cohorte
    [1] => Código Plan
    [2] => Plan
    [3] => Bloqueo
    [4] => RUN
    [5] => DV
    [6] => Nombres
    [7] => Apellido Paterno
    [8] => Apellido Materno
    [9] => Nombre Completo
    [10] => Número estudiante
    [11] => Mail Personal
    [12] => Mail Institucional
    [13] => Periodo curso
    [14] => Sigla curso
    [15] => Asignatura
    [16] => Sección
    [17] => Nivel
    [18] => Calificación
    [19] => Nota
    [20] => Último Logro
    [21] => Fecha Logro
    [22] => Última Carga
)*/
?>