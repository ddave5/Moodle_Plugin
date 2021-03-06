<?php
include_once('Fugges.php');
class Relaciosema
{
    private $nev;
    private $attributumlista;
    private $fuggeshalmaz;
    private $lehetsegeskulcsok;
    private $kulcsok;

    /*------------- CONSTRUCTOR, GETTER, SETTER, TOSTRING START ---------------*/

    /**
     * Relaciosema constructor.
     * @param string $nev
     * @param array $attributumlista
     * @param array $fuggeshalmaz
     * @param array $kulcsok
     * @param array $lehetsegeskulcsok
     */
    public function __construct($nev,$attributumlista, $fuggeshalmaz, $kulcsok, $lehetsegeskulcsok = [])
    {
        $this->nev = $nev;
        $this->attributumlista = $attributumlista;
        $this->fuggeshalmaz = $fuggeshalmaz;
        $this->kulcsok = $kulcsok;
        $this->lehetsegeskulcsok = $lehetsegeskulcsok;
    }

    /**
     * @return array
     */
    public function getAttributumlista()
    {
        return $this->attributumlista;
    }

    /**
     * @param array $attributumlista
     */
    public function setAttributumlista($attributumlista)
    {
        $this->attributumlista = $attributumlista;
    }

    /**
     * @return array
     */
    public function getKulcsok()
    {
        return $this->kulcsok;
    }
    /**
     * @param array $kulcsok
     */
    public function setKulcsok($kulcsok)
    {
        $this->kulcsok = $kulcsok;
    }


    /**
     * @return array
     */
    public function getFuggeshalmaz()
    {
        return $this->fuggeshalmaz;
    }
    /**
     * @param array $fuggeshalmaz
     */
    public function setFuggeshalmaz($fuggeshalmaz)
    {
        $this->fuggeshalmaz = $fuggeshalmaz;
    }

    /**
     * @return string
     */
    public function getNev()
    {
        return $this->nev;
    }
    /**
     * @param string $nev
     */
    public function setNev($nev)
    {
        $this->nev = $nev;
    }

    /**
     * @return array
     */
    public function getLehetsegeskulcsok()
    {
        return $this->lehetsegeskulcsok;
    }
    /**
     * @return array $lehetsegeskulcsok
     */
    public function setLehetsegeskulcsok($lehetsegeskulcsok)
    {
        $this->lehetsegeskulcsok = $lehetsegeskulcsok;
    }

    /** Megadja azt a t??bla nevet, amelyben a param??terben kapott attrib??tum k??ls?? kulcs
     * @param $relaciosemak
     * @param $attributum
     * @param $sajatrelaciosemanev
     * @return string
     */
    private static function getForeignKey($relaciosemak, $attributum, $sajatrelaciosemanev){
        foreach ($relaciosemak as $rel){
            if (in_array($attributum, $rel->getKulcsok()) && strcmp($sajatrelaciosemanev,$rel->getNev()) !=0 ){
                return $rel->getNev();
            }
        }
        return "";
    }

    /** Ki??rja egy norm??lform??ban l??v?? rel??ci??s??m??kban tal??lhat?? attrib??tumokhoz tartoz?? ??sszef??gg??seket egy stringbe.
     * @param array $relaciosemak A rel??ci??s??m??k, amelyek k??z??tt vizsg??ljuk k??ls?? kulcsot.
     * @return string Ha van kapcsolat k??t attrib??tum k??zt, akkor az al??bbi stringet t??rolja K(t??bla1.attr, t??bla2.attr), ha nincs, akkor ??res string.
     */
    private static function writeConnectionRelationshemas($relaciosemak){
        $solution = "";
        $examinedletter= [];
        $counter = 1;
        foreach ($relaciosemak as $rel){
            foreach($rel->getAttributumlista() as $attr){
                if ((strcmp(self::getForeignKey($relaciosemak,$attr,$rel->getNev()),"") != 0) && !in_array($attr,$examinedletter)){
                    $solution.= 'K'.$counter.'(' . $rel->getNev() . '.' . "$attr" . ", " . self::getForeignKey($relaciosemak,$attr,$rel->getNev()) . '.' . "$attr" . ") \r\n";
                    array_push($examinedletter,$attr);
                    $counter++;
                }
            }
        }
        return $solution;
    }

    public static function createSolution($relaciosemak){
        $solution = "";
        $examinedletter= [];
        $examinedRelSchema =[];
        foreach ($relaciosemak as $rel){
            array_push($examinedRelSchema , $rel);
            $solution.=$rel->getNev() . "(";
            foreach ($rel->getAttributumlista() as $attr){
                $tmp = $attr;
                if (in_array($attr,$rel->getKulcsok())){
                    $tmp = '_' . $tmp . '_';
                }
                if (strcmp(self::getForeignKey($examinedRelSchema,$attr,$rel->getNev()),"") != 0 && !in_array($attr,$examinedletter)){
                    $tmp = '*' . $tmp . '*';
                    array_push($examinedletter,$attr);
                }
                $solution .=$tmp . ', ';
            }
            $solution = substr($solution, 0, -2);
            $solution.=')'."\r\n";
        }
        return $solution;
    }

    /** Ki??rja egy fileba a megold??st "_" ??s "*" tagekkel ell??tva. Tartalmazza ezenk??v??l a k??ls?? kulcsok kapcsolatait is.
     * @param array $relaciosemak Rel??ci??s??m??k, melyeket ki akarunk ??rni.
     * @param string $filepath Az ??tvonal, amelybe szeretn??nk ??rni a file-t.
     */
    public static function saveSolution($solutionString, $filepath){
        $myfile = fopen("$filepath", "w") or die("Unable to open file!");
        fwrite($myfile, $solutionString);
        fclose($myfile);
    }

    /** Ki??rja egy fileba a felhaszn??l??t??l kapott megold??st "_" ??s "*" tagekkel ell??tva. Tartalmazza ezenk??v??l a k??ls?? kulcsok kapcsolatait is.
     * @param array $input Rel??ci??s??m??k, melyeket ki akarunk ??rni.
     * @param string $filepath Az ??tvonal, amelybe szeretn??nk ??rni a file-t.
     */
    public static function writeInputToTxt($input,$filepath){
        $input = trim($input);
        $reltomb = explode(") ",$input);

        $solution = "";
        foreach ($reltomb as $relItem){
            $relItem = str_replace(" ","",$relItem);
            $solution.= $relItem . ")\r\n";
        }
        $solution = substr($solution, 0, -3);
        $relsematomb = [];
        foreach($reltomb as $relstr){
            $name = "";
            $attr = [];
            $keys = [];
            for ($i = 0 ; $i < strlen($relstr); $i++){
                if ($relstr[$i] >= 'A' && $relstr[$i] <= 'Z'){
                    $name = $relstr[$i];
                }
                if ($relstr[$i] >= 'a' && $relstr[$i] <= 'z'){
                    array_push($attr,$relstr[$i]);
                }
                if ($relstr[$i] == '_' && $relstr[$i-2] == '_'){
                    array_push($keys,$relstr[$i-1]);
                }
            }
            array_push($relsematomb,new Relaciosema($name,$attr,[],$keys));
        }
        $solution .= "\r\n";
        $solution.=self::writeConnectionRelationshemas($relsematomb);
        $myfile = fopen($filepath, "w") or die("Unable to open file!");
        fwrite($myfile, $solution);
        fclose($myfile);
    }

    public function __toString()
    {
        $sol = "$this->nev";
        $sol .= "(";
        foreach($this->attributumlista as $item){
            $sol.="$item,";
        }
        $sol = substr($sol, 0, -1);
        $sol.=")";
        return $sol;
    }

    /*------------- CONSTRUCTOR, GETTER, SETTER, TOSTRING END ---------------*/

    //__________________ BEGIN SOLVER ____________________

    /** Megadja a param??terben kapott attrib??tumhalmaz lez??rtj??t a param??terben kapott rel??ci??s??ma felett.
     * @param Relaciosema $relaciosema A vizsg??lt rel??ci??s??ma.
     * @param array $attributumhalmaz Egy karakterhalmaz, amelynek akarjuk tudni a lez??rtj??t.
     * @return array Karakterhalmaz, a param??terben kapott attrib??tumhalmza lez??rtja.
     */
    private static function createAttributePlus($relaciosema,$attributumhalmaz){
        $atrset = [];
        foreach($attributumhalmaz as $atr){
            array_push($atrset,$atr);
        }
        $iterator = 0;
        while($iterator < count($relaciosema->getFuggeshalmaz())){
            $tmp = $relaciosema->getFuggeshalmaz()[$iterator];
            if(count(array_diff($tmp->getBaloldal(),$atrset)) == 0 && count(array_diff($tmp->getJobboldal(),$atrset)) != 0){
                foreach ($tmp->getJobboldal() as $jatr){
                    if (!in_array($jatr,$atrset)){
                        array_push($atrset,$jatr);
                    }
                }
                $iterator = 0;
            }
            else{
                $iterator++;
            }

        }
        sort($atrset);
        return $atrset;
    }

    /** Megadja egy rel??ci??s??ma csak jobboldali, csak baloldali ??s mindk??t halmazban szerepl?? elemeit.
     * @param Relaciosema $relsema  A vizsg??lt rel??ci??s??ma.
     * @return array[] A rel??ci??s??ma halmazai az al??bbi m??don: [jobb,bal,jobb-bal].
     */
    private static function generateROandLOandLR($relsema){
        $ro = [];
        $lo = [];
        $lr = [];
        $leftsided = false;
        $rightsided = false;
        foreach($relsema->getAttributumlista() as $attributum){
            foreach($relsema->getFuggeshalmaz() as $fugges){
                if (in_array($attributum,$fugges->getBaloldal())){
                    $leftsided = true;
                }
                if(in_array($attributum,$fugges->getJobboldal())){
                    $rightsided = true;
                }
                if($leftsided && $rightsided && !in_array($attributum,$lr)){
                    array_push($lr,$attributum);
                    continue;
                }
            }
            if($leftsided && !$rightsided){
                array_push($lo,$attributum);
            }
            else if($rightsided && !$leftsided){
                array_push($ro,$attributum);
            }
            $leftsided = false;
            $rightsided = false;
        }
        return array($ro,$lo,$lr);
    }

    /** Elt??vol??tja egy f??gg??shalmazb??l azokat az elemeket, amelyek t??bbsz??r vannak benne.
     * @param array $depList A vizsg??lt f??gg??shalmaz.
     * @return array F??gg??shalmaz, duplik??lt elem n??lk??l.
     */
    private static function removeDuplicateDependencies($depList){
        $dup_key = [];
        for ($i = 0; $i < count($depList)-1; $i++){
            for ($j = $i+1; $j < count($depList); $j++){
                if(empty(array_diff($depList[$i]->getBaloldal(),$depList[$j]->getBaloldal())) &&
                    empty(array_diff($depList[$j]->getBaloldal(),$depList[$i]->getBaloldal())) &&
                    empty(array_diff($depList[$i]->getJobboldal(),$depList[$j]->getJobboldal())) &&
                    empty(array_diff($depList[$j]->getJobboldal(),$depList[$i]->getJobboldal()))){
                    array_push($dup_key,$j);
                }
            }
        }
        $dup_key = array_unique($dup_key);
        foreach($dup_key as $item){
            unset($depList[$item]);
        }
        return $depList;
    }

    /** Elt??vol??tja az extraneous Attrib??tumokat.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma.
     * @return array F??gg??shalmaz, amelynek f??gg??sei k??zt nincsenek extraneous attrib??tumok
     */
    private static function removeExtraneousAttributes($relsema){
        $Y = [];
        $tmpy= [];
        $G = $relsema->getFuggeshalmaz();
        $H = [];
        $extras = self::generateROandLOandLR($relsema)[2];
        foreach($G as $fugges){
            if(count($fugges->getBaloldal())>1){
                $Y = $fugges->getBaloldal();
                foreach($Y as $battr){
                    if(in_array($battr,$extras)){
                        foreach($G as $dep){
                            if(!(empty(array_diff($dep->getBaloldal(),$fugges->getBaloldal())) &&
                                empty(array_diff($fugges->getBaloldal(),$dep->getBaloldal())) &&
                                empty(array_diff($dep->getJobboldal(),$fugges->getJobboldal())) &&
                                empty(array_diff($fugges->getJobboldal(),$dep->getJobboldal())))){
                                array_push($H,$dep);
                            }
                        }
                        array_push($H,new Fugges(array_diff($Y,array($battr)),$fugges->getJobboldal()));
                        if (in_array($battr,self::createAttributePlus(new Relaciosema('R',$relsema->getAttributumlista(),$H,$relsema->getKulcsok()),array_diff($Y,array($battr))))){
                            foreach($Y as $elem){
                                if ($elem != $battr){
                                    array_push($tmpy,$elem);
                                }
                            }
                            $Y = $tmpy;
                            $tmpy = [];
                        }
                    }
                }

                if (!($fugges->getBaloldal() == $Y)){
                    $G=$H;
                    $H = [];
                }
            }
        }

        $G = self::removeDuplicateDependencies($G);

        return $G;
    }

    /** Elt??vol??tja a felesleges f??gg??seket egy f??gg??shalmazb??l.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma, amelyb??l nyeri ki a f??gg??shalmazt.
     * @return array F??gg??shalmaz, amely m??r nem tartalmaz felesleges f??gg??seket.
     */
    private static function removeRedundantDependencies($relsema){
        $fm = self::removeExtraneousAttributes($relsema);
        $G = [];
        $counter = 0;
        foreach($fm as $fugges){ //X -> A
            while($counter < count($fm)){
                $tmp = $fm[$counter]; //Y -> A
                if((
                    (!empty(array_diff($tmp->getBaloldal(),array_intersect($tmp->getBaloldal(),$fugges->getBaloldal()))) ||
                    !empty(array_diff($fugges->getBaloldal(),array_intersect($tmp->getBaloldal(),$fugges->getBaloldal()))))
                    &&
                    (empty(array_diff($tmp->getJobboldal(),$fugges->getJobboldal())) &&
                    empty(array_diff($fugges->getJobboldal(),$tmp->getJobboldal()))))){
                    foreach ($fm as $elem) {
                        if (!($elem->getBaloldal() == $tmp->getBaloldal() && $elem->getJobboldal() == $tmp->getJobboldal())) {
                            array_push($G, $elem);
                        }
                    }
                    if (empty(array_diff($fugges->getBaloldal(), self::createAttributePlus(new Relaciosema('R', $relsema->getAttributumlista(), $G,$relsema->getKulcsok()), $tmp->getBaloldal())))) {
                        $fm = $G;
                    }
                }
                $counter++;
            }
            $G = [];
            $counter = 0;
        }
        return $fm;
    }

    /** Megadja egy rel??ci??s??ma teljes ??s r??szleges f??gg??seit.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma.
     * @return array[] A f??gg??sek csoportos??tva az al??bbiak szerint: [R??szleges,Teljes].
     */
    private static function createFullandPartialDependencies($relsema){
        $tmp = [];
        $fp = [];
        $ff = self::removeRedundantDependencies($relsema);
        $counter =0;
        foreach($ff as $fugges){
            foreach($relsema->getLehetsegesKulcsok() as $canKey){
                if (!(empty(array_diff($canKey,$fugges->getBaloldal()))) && !(empty(array_diff($fugges->getJobboldal(),$canKey)))){
                    array_push($fp,$fugges);
                    foreach($ff as $fg){
                        if (!($fg->getBaloldal() == $fugges->getBaloldal() && $fg->getJobboldal() == $fugges->getJobboldal())) {
                            array_push($tmp, $fg);
                        }
                    }
                    $ff = $tmp;
                    $tmp = [];
                }

                while($counter < count($ff)){
                    $dep = $ff[$counter];
                    if (empty(array_diff($dep->getBaloldal(),
                        self::createAttributePlus(
                            new Relaciosema('R',
                                $relsema->getAttributumlista(),
                                self::removeRedundantDependencies($relsema),
                                $relsema->getKulcsok())
                            ,$fugges->getJobboldal())))){
                        array_push($fp,$dep);
                        foreach($ff as $fg){
                            if (!($fg->getBaloldal() == $dep->getBaloldal() && $fg->getJobboldal() == $dep->getJobboldal())) {
                                array_push($tmp, $fg);
                            }
                        }
                        $ff = $tmp;
                        $tmp = [];
                    }
                    $counter++;
                }
            }
            }

        return [self::removeDuplicateDependencies($fp),self::removeDuplicateDependencies($ff)];

    }

    /** Elt??vol??tja azon f??gg??seket, amelyek olyan karaktereket tartalmaznak, amelyek nincsenek benne a rel??ci??s??m??ba.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma.
     * @return Relaciosema Felesleges attrib??tumok n??lk??li rel??ci??s??ma.
     */
    private static function removeUselessDependencies($relsema){
        $sol = [];
        foreach($relsema->getFuggeshalmaz() as $fugges){
            $baloldal = [];
            $jobboldal = [];
            foreach($fugges->getBaloldal() as $battr){
                if(in_array($battr,$relsema->getAttributumlista())){
                    array_push($baloldal,$battr);
                }
            }
            foreach ($fugges->getJobboldal() as $jattr){
                if (in_array($jattr,$relsema->getAttributumlista())){
                    array_push($jobboldal,$jattr);
                }
            }
            if (!(empty($baloldal) || empty($jobboldal))){
                array_push($sol,new Fugges($baloldal,$jobboldal));
            }
        }
        $relsema->setFuggeshalmaz(array_values($sol));
        return $relsema;
    }

    /** A param??terben adott rel??ci??s??ma 2NF alakja.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma.
     * @return array Rel??ci??s??m??kat tartalmaz?? halmaz, elemei a 2NF elemei.
     */
    public static function NF2($relsema){
        $G = array_values(self::createFullandPartialDependencies($relsema)[0]);
        $Xf = [];
        $tmpXf=[];
        $tmpG=[];
        $R2nf = [];
        $counter = count($R2nf)+1;
        if(empty(self::createFullandPartialDependencies($relsema)[0])){
            array_push($R2nf,$relsema);
            return $R2nf;
        }
        if(!empty(self::createFullandPartialDependencies($relsema)[1])){
            foreach(self::createFullandPartialDependencies($relsema)[1] as $ff){
                foreach ($ff->getBaloldal() as $battr){
                    if(!in_array($battr,$Xf)){
                        array_push($Xf,$battr);
                    }
                }
                foreach ($ff->getJobboldal() as $jattr){
                    if(!in_array($jattr,$Xf)){
                        array_push($Xf,$jattr);
                    }
                }
            }
        }
        else{
            $Xf = $relsema->getAttributumlista();
        }

        foreach($G as $fugges){
            if((empty(array_diff($fugges->getBaloldal(),$relsema->getKulcsok()))) && !empty($G)){
                $attrhalmaz = self::createAttributePlus(new Relaciosema('R',$relsema->getAttributumlista(),$G,$relsema->getKulcsok()),$fugges->getBaloldal());
                $Ry = new Relaciosema('R'.$counter,array_intersect($attrhalmaz,$relsema->getAttributumlista()),$G,$fugges->getBaloldal());
                $relsema->setAttributumlista(array_merge(array_diff($relsema->getAttributumlista(),$attrhalmaz),$relsema->getKulcsok()));
                $counter++;


                foreach($Xf as $attr){
                    if(!(!in_array($attr,$relsema->getKulcsok())
                        &&in_array($attr,self::createAttributePlus(new Relaciosema('',$relsema->getAttributumlista(),$G,$relsema->getKulcsok()),$fugges->getBaloldal())))){
                        array_push($tmpXf,$attr);
                    }
                }

                $Xf = $tmpXf;
                $tmpXf = [];

                foreach($G as $dep){
                    if(!empty(array_diff($dep->getBaloldal(),self::createAttributePlus(new Relaciosema('',$relsema->getAttributumlista(),$G,$fugges->getBaloldal()),$fugges->getBaloldal())))){
                        array_push($tmpG,$dep);
                    }
                }
                $G = $tmpG;
                $tmpG = [];
                array_push($R2nf,$Ry);
            }
        }

        array_push($R2nf,new Relaciosema('R'.$counter
            ,$Xf
            ,$relsema->getFuggeshalmaz()
            ,$relsema->getKulcsok()));

        foreach ($R2nf as $item){
            $item = Relaciosema::removeUselessDependencies($item);
        }
        return $R2nf;
   }

    /** A param??terben adott rel??ci??s??ma 3NF alakja.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma.
     * @return array Rel??ci??s??m??kat tartalmaz?? halmaz, elemei a 3NF elemei.
     */
    public static function NF3($relsema){
        $nf2 = self::NF2($relsema);
        $Rnf3 = [];
        $tmpattr =[];
        $counter = 1;
        foreach ($nf2 as $relsema){
            for ($i = 0; $i < count($relsema->getFuggeshalmaz())-1 ;$i++){
                for ($j = $i+1; $j < count($relsema->getFuggeshalmaz()); $j++){
                    if (empty(array_diff(
                            array_merge($relsema->getFuggeshalmaz()[$j]->getBaloldal(),
                                        $relsema->getFuggeshalmaz()[$j]->getJobboldal()),
                            $relsema->getFuggeshalmaz()[$i]->getJobboldal())) && empty(array_diff($relsema->getFuggeshalmaz()[$i]->getBaloldal(),$relsema->getKulcsok()))
                    ){
                        $attrList = array_merge($relsema->getFuggeshalmaz()[$j]->getBaloldal(),$relsema->getFuggeshalmaz()[$j]->getJobboldal());
                        sort($attrList);
                        $Ry = new Relaciosema('R'.$counter,
                            $attrList,
                            $relsema->getFuggeshalmaz(),
                            $relsema->getFuggeshalmaz()[$j]->getBaloldal(),array($relsema->getFuggeshalmaz()[$j]->getBaloldal()));

                        $counter++;

                        foreach ($relsema->getAttributumlista() as $attr){
                            if(!in_array($attr,$Ry->getAttributumlista()) || in_array($attr,$Ry->getKulcsok())){
                                array_push($tmpattr, $attr);
                            }
                        }
                        $relsema->setAttributumlista(array_unique($tmpattr));
                        $tmpattr = [];

                        $Ry = self::removeUselessDependencies($Ry);
                        array_push($Rnf3,$Ry);
                    }
                }
            }
            $relsema = self::removeUselessDependencies($relsema);
            $relsema->setNev('R'.$counter);
            $counter++;
            array_push($Rnf3,$relsema);
        }
        return $Rnf3;
    }

    /** A param??terben adott rel??ci??s??ma BCNF alakja.
     * @param Relaciosema $relsema A vizsg??lt rel??ci??s??ma.
     * @return array Rel??ci??s??m??kat tartalmaz?? halmaz, elemei a BCNF elemei.
     */
    public static function BCNF($relsema){
        $nf3 = self::NF3($relsema);
        $bcnf = [];
        $tmpattr = [];
        $counter = 1;
        foreach($nf3 as $relsema){
            foreach($relsema->getFuggeshalmaz() as $fugges){
                if (!empty(array_diff($relsema->getKulcsok(),$fugges->getbaloldal())) && count(array_diff($relsema->getKulcsok(),$fugges->getbaloldal())) < count($relsema->getKulcsok())){
                    $attrList = array_merge($fugges->getBaloldal(),$fugges->getJobboldal());
                    sort($attrList);
                    $Ry = new Relaciosema('R'.$counter,
                        $attrList,
                        $relsema->getFuggeshalmaz(),
                        $fugges->getBaloldal(),array($fugges->getBaloldal()));

                    $counter++;

                    foreach ($relsema->getAttributumlista() as $attr){
                        if(!in_array($attr,$Ry->getAttributumlista()) || in_array($attr,$Ry->getKulcsok())){
                            array_push($tmpattr, $attr);
                        }
                    }
                    $relsema->setAttributumlista(array_unique($tmpattr));
                    $tmpattr = [];

                    $Ry = self::removeUselessDependencies($Ry);
                    array_push($bcnf,$Ry);
                }
            }
            $relsema = self::removeUselessDependencies($relsema);
            $relsema->setNev('R'.$counter);
            $counter++;
            array_push($bcnf,$relsema);
        }
        return $bcnf;
    }

    //___________________ END SOLVER _____________________

    //_________________ BEGIN GENERATOR __________________

    /** L??trehoz egy halmazt, amelyben t??rol??dnak egy rel??ci??s??ma attrib??tumai.
     * @param int $pieces Rel??ci??s??ma attrib??tumainak sz??ma.
     * @return array Rel??ci??s??ma attrib??tumai.
     */
    private static function createAttrbuteList($pieces){
        $attrList = [];
        for ($i = 97; $i< $pieces + 97; $i++){
            array_push($attrList,chr($i));
        }
        return $attrList;
    }

    /** L??trehoz egy rel??ci??s??m??hoz egy kulcsot.
     * @param array $attributes Attrib??tumlista.
     * @return array A kulcsok halmaza.
     */
    private static  function createKeys($attributes){
        $randomAttributeKeys = array_rand($attributes,intdiv(count($attributes),3));
        $keyList=[];
        foreach($attributes as $key => $value){
            if (in_array($key,$randomAttributeKeys)){
                array_push($keyList,$value);
            }
        }
        return $keyList;
    }

    /** L??trehoz egy halmazb??l egy param??terben kapott elemsz??m?? v??letlenszer?? r??szhalmazt.
     * @param array $array A teljes halmaz.
     * @param int $num_req A r??szhalmaz elemsz??ma.
     * @return array A r??szhalmaz.
     */
    private static function createRandomArraySubset($array,$num_req){
        $arraySubset = [];
        $randomKeys = array_rand($array,$num_req);
        if(gettype($randomKeys) == "integer"){
            $randomKeys = array($randomKeys);
        }
        foreach($array as $key => $value){
            if (in_array($key,$randomKeys)){
                array_push($arraySubset,$value);
            }
        }
        return $arraySubset;
    }
    /** L??trehoz egy "Egyszer??" feladatot.
     * @return Relaciosema A feladat.
     */
    public static function createEasyTask(){
        $lowerBound = 7;
        $upperBound = 9;
        $attributeNumber = rand($lowerBound,$upperBound);
        $attributeList = self::createAttrbuteList($attributeNumber);
        $keyList=self::createKeys($attributeList);

        $f1LeftSide = self::createRandomArraySubset($keyList,count($keyList)-1);
        $f1Rightside = self::createRandomArraySubset(array_diff($attributeList,$keyList),count($attributeList)-5);

        $f1 = new Fugges($f1LeftSide,$f1Rightside);

        $amount = rand(1,count($f1Rightside)-1);
        $f2LeftSide = self::createRandomArraySubset($f1Rightside,$amount);

        $remainingElements = array_diff($f1Rightside,$f2LeftSide);
        $amount = rand(1,count($remainingElements));
        $f2RightSide = self::createRandomArraySubset($remainingElements,$amount);

        $f2 = new Fugges($f2LeftSide,$f2RightSide);
        $f3 = new Fugges($keyList,array_diff($attributeList,array_merge($keyList,$f2RightSide,$f1Rightside)));
        $dependencyList = [$f1,$f2,$f3];

        return new Relaciosema('R',$attributeList,$dependencyList,$keyList,array($keyList));
    }

    /** L??trehoz egy "K??zepes" vagy "Dupl??z??s" feladatot.
     * @return Relaciosema A feladat.
     */
    public static function createDoubleTask(){
        $attributeNumber = 9;
        $attributeList = self::createAttrbuteList($attributeNumber);
        $keyList=self::createKeys($attributeList);

        $f1LeftSide = self::createRandomArraySubset($keyList,count($keyList)-1);
        $f1RightAmount = rand($attributeNumber-6,$attributeNumber-5);
        $f1RightSide = self::createRandomArraySubset(array_diff($attributeList,$keyList),$f1RightAmount);
        $f1 = new Fugges($f1LeftSide,$f1RightSide);

        $f2LeftAmount = rand(1,count($f1RightSide)-1);
        $f2LeftSide = self::createRandomArraySubset($f1RightSide,$f2LeftAmount);
        $f2RightAmount = rand(1,$f1RightAmount-$f2LeftAmount);
        $f2RightSide = self::createRandomArraySubset(array_diff($f1RightSide,$f2LeftSide),$f2RightAmount);
        $f2 = new Fugges($f2LeftSide,$f2RightSide);

        $f3LeftSide = self::createRandomArraySubset(array_diff($keyList,$f1LeftSide),count(array_diff($keyList,$f1LeftSide)));
        $f3RightSide = self::createRandomArraySubset(array_diff($attributeList,array_merge($keyList,$f1RightSide)),count(array_diff($attributeList,array_merge($keyList,$f1RightSide))));
        $f3 = new Fugges($f3LeftSide,$f3RightSide);

        $f4LeftAmount = rand(1,count($f3RightSide)-1);
        $f4LeftSide = self::createRandomArraySubset($f3RightSide,$f4LeftAmount);
        $f4RightAmount = rand(1,$attributeNumber-count($keyList)-$f1RightAmount-$f4LeftAmount);
        $f4RightSide = self::createRandomArraySubset(array_diff($f3RightSide,$f4LeftSide),$f4RightAmount);
        $f4 = new Fugges($f4LeftSide,$f4RightSide);

        $dependencyList = [$f1,$f2,$f3,$f4];

        return new Relaciosema('R',$attributeList,$dependencyList,$keyList,array($keyList));
    }

    /** L??trehoz egy "Neh??z" vagy "??sszetett" feladatot.
     * @return Relaciosema A feladat.
     */
    public static function createComplexTask(){
        $lowerBound = 7;
        $upperBound = 8;
        $attributeNumber = rand($lowerBound,$upperBound);
        $attributeList = self::createAttrbuteList($attributeNumber);
        $keyList=self::createKeys($attributeList);

        $f1LeftSide = self::createRandomArraySubset($keyList,count($keyList)-1);
        $f1RightSide = self::createRandomArraySubset(array_diff($attributeList,$keyList),2);
        $f1 = new Fugges($f1LeftSide,$f1RightSide);

        $f2LeftSide = array_merge(self::createRandomArraySubset($f1RightSide,1),array_diff($keyList,$f1LeftSide));
        $f2RightSide = self::createRandomArraySubset(array_diff($attributeList,array_merge($f1RightSide,$keyList)),2);
        $f2 = new Fugges($f2LeftSide,$f2RightSide);

        $f3LeftSide = array_merge(array_diff($f1RightSide,$f2LeftSide),self::createRandomArraySubset($f2RightSide,1));
        $f3RightSide = array_diff($attributeList,array_merge($keyList,$f1RightSide,$f2RightSide));
        $f3 = new Fugges($f3LeftSide,$f3RightSide);

        $dependencyList = [$f1,$f2,$f3];

        return new Relaciosema('R',$attributeList,$dependencyList,$keyList,array($keyList));
    }

    /** L??trehoz egy "Neh??z" vagy "Z??rt" feladatot.
     * @return Relaciosema A feladat.
     */
    public static function createClosedTask(){
        $lowerBound = 7;
        $upperBound = 8;
        $attributeNumber = rand($lowerBound,$upperBound);
        $attributeList = self::createAttrbuteList($attributeNumber);
        $keyList=self::createKeys($attributeList);

        $candidateKeyList = [];
        array_push($candidateKeyList,$keyList);
        $anotherCandidateKeyElement = [];
        array_push($anotherCandidateKeyElement,self::createRandomArraySubset($keyList,1)[0]);
        $newElement = self::createRandomArraySubset(array_diff($attributeList,$keyList),1)[0];
        array_push($anotherCandidateKeyElement,$newElement);
        array_push($candidateKeyList,$anotherCandidateKeyElement);


        $f1LeftSide = self::createRandomArraySubset($keyList,count($keyList)-1);
        $f1RightAmount = intdiv($attributeNumber,4)+1;
        $f1RightSide = array_merge(array($newElement),self::createRandomArraySubset(array_diff($attributeList,array_merge($keyList,array($newElement))),$f1RightAmount));
        $f1 = new Fugges($f1LeftSide,$f1RightSide);

        $f2LeftSide = array($newElement);
        $f2RightSide = array_merge($f1LeftSide,self::createRandomArraySubset(array_diff($attributeList,array_merge($keyList,$f1RightSide)),1));
        $f2 = new Fugges($f2LeftSide,$f2RightSide);

        $f3LeftAmount = rand(1,$f1RightAmount-1);
        $f3LeftSide = self::createRandomArraySubset(array_diff($f1RightSide,array($newElement)),$f3LeftAmount);
        $f3RightSide = array_diff($f1RightSide,array_merge(array($newElement),$f3LeftSide));
        $f3 = new Fugges($f3LeftSide,$f3RightSide);

        $f4LeftSide = array_merge(array_diff($keyList,$f1LeftSide),self::createRandomArraySubset(array_diff(array_merge($f1RightSide,$f2RightSide),array_merge($f1LeftSide,$f2LeftSide)),1));
        $f4RightSide = array_diff($attributeList,array_merge($keyList,$f1RightSide,$f2RightSide));
        $f4 = new Fugges($f4LeftSide,$f4RightSide);

        $dependencyList = [$f1,$f2,$f3,$f4];

        return new Relaciosema('R',$attributeList,$dependencyList,$keyList,$candidateKeyList);

    }

    //__________________ END GENERATOR ___________________
    //________________ START ENCODDER ____________________


    /** ??talak??t egy rel??ci??s s??m??t megfelel?? alak?? stringg??, amelyet tov??bbk??ld egy m??sik oldalnak.
     * @param Relaciosema $relsema Az ??talak??tani k??v??nt rel??ci??s??ma
     * @return string A lek??dolt rel??ci??s??ma.
     */
    public static function encoder($relsema){
        $sol = $relsema->getNev();
        foreach($relsema->getAttributumlista() as $attr){
            $sol.=$attr;
        }
        foreach($relsema->getFuggeshalmaz() as $fugges){
            $sol.='%7C';
            foreach ($fugges->getBaloldal() as $battr){
                $sol.=$battr;
            }
            $sol.='%7C';
            $sol.='%7C';
            foreach ($fugges->getJobboldal() as $jattr){
                $sol.=$jattr;
            }
            $sol.='%7C';
        }
        foreach($relsema->getKulcsok() as $item){
            $sol.=$item;
        }
        return $sol;
    }

    /** ??talak??t egy lek??dolt stringet rel??ci??s??m??ra.
     * @param string $string A lek??dolt rel??ci??s??ma.
     * @return Relaciosema Az ??talak??tott rel??ci??s??ma.
     */
    public static function decoder($string){
        $exploded = array_values(array_filter(explode('%7C',$string), fn($value) => !is_null($value) && $value !== ''));

        $attri = $exploded[0];
        $relnev = $attri[0];
        $attrilist = [];
        for ($i = 1; $i < strlen($attri);$i++){
            array_push($attrilist,$attri[$i]);
        }
        $fuggesList= [];
        $baloldal = [];
        $jobboldal = [];
        for ($i = 1; $i < count($exploded)-1 ; $i++ ){
            if($i % 2 == 1){
                for($j = 0; $j < strlen($exploded[$i]) ; $j++){
                    array_push($baloldal,$exploded[$i][$j]);
                }
            }
            else{
                for($k = 0; $k < strlen($exploded[$i]) ; $k++){
                    array_push($jobboldal,$exploded[$i][$k]);
                }
                array_push($fuggesList,new Fugges($baloldal,$jobboldal));
                $baloldal = [];
                $jobboldal = [];
            }

        }
        $kulcsok = [];
        for($j = 0; $j < strlen($exploded[count($exploded)-1]) ; $j++){
            array_push($kulcsok,$exploded[count($exploded)-1][$j]);
        }
        return new Relaciosema($relnev,$attrilist,$fuggesList,$kulcsok,array($kulcsok));
    }
}

