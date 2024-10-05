<?php
class DataLoader{
    private $path;
    private $nullIndexs;
    public function __construct($path, $nullIndexs){
        $this->path = $path;
        $this->nullIndexs = $nullIndexs;
    }

    public function ReadData(){
        $Badlines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $GoodLines = [];

        for ($i = 0; $i < count($Badlines); $i++) {

            $Line = explode(";", $Badlines[$i]);
        

            //trabajo para otra clase
            for ($j = 0; $j < count($Line); $j++){
                //si este es nulo y no debe serlo, lo marcamos con X
                if ($Line[$j] == "" && !in_array($j, $this->nullIndexs)){
                    $Line[$j] = "X";
                }
    }

            if ($this->CheckFormat($Line)==true){
                $GoodLines[] = $Line;
            }
            /*else{
                //mandarlo para otra clase
            }*/
        
        }
        return $GoodLines;
    }

    private function CheckFormat($Line){
        if ($this->path == "csvArchives/Estudiantes.csv"){
            return correctFormatArchiveOne($Line);
        }

        return correctFormatArchiveTwo($Line);
    }
}
?>