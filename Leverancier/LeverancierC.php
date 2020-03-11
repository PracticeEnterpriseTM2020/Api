<?php 
    // Klasse voor de leverancier.
    class Leverancier
    {
        Private $companyName;
        Private $btwNr;
        Private $korting;
        Private $email;
        Private $adres;
        Private $telefoonNr;
        Private $conn;


        //construct maken
        Public function __construct($connection, $companyName,$btwNr, $korting = 0, $email, $adres, $telefoonNr)
        {
            if ($connection = " ")
            {
                return "error geen verbinding";
            })
            else 
            {
                $this->conn = $connection;
                $this->companyName = mysqli_escape_string($this->conn,$companyName);
                $this->btwNr = mysqli_escape_string($this->conn,$btwNr);
                $this->korting = mysqli_escape_string($this->conn,$korting);
                $this->email = mysqli_escape_string($this->conn,$email);
                $this->adres = mysqli_escape_string($this->conn,$adres);
                $this->telefoonNr = mysqli_escape_string($this->con, $telefoonNr);
            }
        }

        // zorgen dat ze alleen de companyname kunnen aanpassen.
        Public function __setCompanyName($companyName)
        {
            $this->companyName = mysqli_escape_string($this->conn,$companyName);
        }

    
        Public function __setbtwNr($btwNr)
        {
            $this->btwNr = mysqli_escape_string($this->conn,$btwNr);
        }

        Public function __setkorting($korting)
        {
            $this->korting = mysqli_escape_string($this->conn,$korting);
        }

        Public function __setemail($email)
        {
            $this->email = mysqli_escape_string($this->conn,$email);
        }

        Public function __setadres($adres)
        {
            $this->adres = mysqli_escape_string($this->conn,$adres);
        }

        Public function __settelefoonNr($telefoonNr)
        {
            $this->telefoonNr = mysqli_escape_string($this->conn,$telefoonNr);
        }


        Public function __getbtwNr($btwNr)
        {
            return $this->btwNr;
        }

        Public function __getkorting($korting)
        {
            return $this->korting;
        }

        Public function __getemail($email)
        {
            return $this->email;
        }

        Public function __getadres($adres)
        {
            return $this->adres;
        }

        Public function __gettelefoonNr($telefoonNr)
        {
            return $this->telefoonNr;
        }

        
    }




?>