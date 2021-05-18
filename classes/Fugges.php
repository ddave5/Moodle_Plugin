<?php

class Fugges
{
    public $baloldal;
    public $jobboldal;

    /*------------- CONSTRUCTOR, GETTER, SETTER, TOSTRING START ---------------*/

    /**
     * Fugges constructor.
     * @param $baloldal
     * @param $jobboldal
     */
    public function __construct($baloldal, $jobboldal)
    {
        $this->baloldal = $baloldal;
        $this->jobboldal = $jobboldal;
    }

    /**
     * @return array
     */
    public function getBaloldal()
    {
        return $this->baloldal;
    }

    /**
     * @param array $baloldal
     */
    public function setBaloldal($baloldal)
    {
        $this->baloldal = $baloldal;
    }

    /**
     * @return array
     */
    public function getJobboldal()
    {
        return $this->jobboldal;
    }

    /**
     * @param array $jobboldal
     */
    public function setJobboldal($jobboldal)
    {
        $this->jobboldal = $jobboldal;
    }
    public function __toString(){
        $sol = "{";
        foreach ($this->getBaloldal() as $bal){
            $sol.="$bal,";
        }
        $sol = substr($sol, 0, -1);
        $sol.="}->{";
        foreach ($this->getJobboldal() as $jobb){
            $sol.="$jobb,";
        }
        $sol = substr($sol, 0, -1);
        $sol.="}";
        return $sol;
    }

    /*------------- CONSTRUCTOR, GETTER, SETTER, TOSTRING END ---------------*/

}