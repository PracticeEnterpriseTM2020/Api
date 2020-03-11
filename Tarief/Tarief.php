<?php
//class tarief
class Tarief {
    // Properties
    
    private $tariefID;
    private $tariefNaam;
    private $tariefBeschrijving;
    private $tariefWaarde;
    private $tariefActief;
    
    // Methods
    // Methode to create a Tarief (takes 4 values)
    function create_Tarief($tariefNaam, $tariefBeschrijving, $tariefWaarde, $tariefActief ) {
        $this->tariefID = //Hier komt een random generated ID ;
        $this->tariefNaam = $tariefNaam;
        $this->tariefBeschrijving = $tariefBeschrijving;
        $this->tariefWaarde = $tariefWaarde;
        $this->tariefActief = $tariefActief;

  }
    
    function delete_Tarief($tariefID) {
    $this->tariefID = $tariefID;
  }
    function update_Tarief() {
    
  }
    function get_Tarief() {
    
  }
    function get_Alle_Tarief() {

    }


}
?>