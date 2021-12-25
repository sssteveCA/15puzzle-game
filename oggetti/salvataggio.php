<?php

define("SALVATAGGIOERR_IDNOTEXISTS","1");
define("SALVATAGGIOERR_QUERYERROR","2");
define("SALVATAGGIOERR_INFONOTGETTED","3");
define("SALVATAGGIOERR_DATANOTUPDATED","4");
define("SALVATAGGIOERR_INVALIDDATAFORMAT","5");
define("SALVATAGGIOERR_DATANOTINSERTED","6");

class Salvataggio{
    const HOST = 'localhost';
    const USERNAME = 'root';
    const PASSWORD = '';
    const DATABASE = 'stefano';
    const TABLE = 'salvataggi';
    const TABLE_PLAYERS = 'giocatori';

    //tutti gli id dei salvataggi di un determinato giocatore
    private static $idSalvataggi = array(); 
    //numero dei salvataggi effettuati da parte di un giocatore
    private static $nSalvataggi = 0;
    //ottengo gli id dei salvataggi di un determinato giocatore
    public static function getPlayerSaves($player){
        Salvataggio::$idSalvataggi = array();
        Salvataggio::$nSalvataggi = 0;
        $h = new mysqli(Salvataggio::HOST,Salvataggio::USERNAME,Salvataggio::PASSWORD,Salvataggio::DATABASE);
        if($h->connect_errno === 0){
            $h->set_charset("utf8mb4");
            $tabella = Salvataggio::TABLE;
            $tab_players = Salvataggio::TABLE_PLAYERS;
            $query = <<<SQL
SELECT `id` FROM `{$tabella}` WHERE `idg` = (
    SELECT `id` FROM `{$tab_players}` WHERE `username` = '$player' 
)
ORDER BY `slot`;
SQL;
            $r = $h->query($query);
            if($r){
                if($r->num_rows > 0){
                    while($idS = $r->fetch_array(MYSQLI_ASSOC)){
                        Salvataggio::$nSalvataggi++;
                        Salvataggio::$idSalvataggi[] = $idS['id'];
                    }
                }
                $r->free();
            }
            $h->close();
            return Salvataggio::$idSalvataggi;
        }//if($h->connect_errno === 0)
    }//public static function getPlayerSaves($player)
    //numero di salvataggi effettuati da parte di un determinato giocatore(dopo aver chiamato la funzione sopra)
    public static function nSalvataggi(){
        return Salvataggio::$nSalvataggi;
    }

    private $h; //handle connessione MySql
    private $connesso; //se l'oggetto ha stabilito la connessione con il database
    private $tabella; //tabella MySql dei salvataggi
    private $query; //ultima query inviata dall'oggetto
    private $queries; //lista di query SQL eseguite
    private $id;
    private $idg; //id del giocatore che ha creato il salvataggio
    private $data; //data in cui il salvataggio è stato creato
    private $slot; //posizione in cui è stata salvata la partita
    private $sequenza; //posizione delle tessere al momento del salvataggio
    private $tempo;
    private $spostamenti;
    private $errno; //codice errore 
    private $error; //messaggio di errore
    public static $regex = array(
        'id' => '/^[0-9]+$/',
        'slot' => '/^[1-5]$/',
        'tempo' => '/^([0-9]+)\s(2[0-3]|1[0-9]|[0-9])\s([0-5][0-9]|[0-9])\s([0-5][0-9]|[0-9])$/'
    );

    public function __construct($ingresso){
        $this->errno = 0;
        $this->connesso = false;
        $mysqlHost=isset($ingresso['mysqlHost'])? $ingresso['mysqlHost']:Salvataggio::HOST;
        $mysqlUser=isset($ingresso['mysqlUser'])? $ingresso['mysqlUser']:Salvataggio::USERNAME;
        $mysqlPass=isset($ingresso['mysqlPass'])? $ingresso['mysqlPass']:Salvataggio::PASSWORD;
        $mysqlDb=isset($ingresso['mysqlDb'])? $ingresso['mysqlDb']:Salvataggio::DATABASE;   
        $this->h = new mysqli($mysqlHost,$mysqlUser,$mysqlPass);
        $this->query = '';
        $this->queries = array();
        if($this->h->connect_errno !== 0){
            throw new Exception("Connessione a MySql fallita: ".$this->h->connect_error);
        }
        if(!$this->createDb($mysqlDb)){
            throw new Exception("Errore durante il controllo del database");
        }
        $this->h->select_db($mysqlDb);
        $this->tabella = isset($ingresso['tabella'])? $ingresso['tabella']:Salvataggio::TABLE;
        if(!$this->createTable()){
            throw new Exception("Errore durante il controllo della tabella");
        }    
        $this->error = null;
        $this->h->set_charset("utf8mb4");
        $this->connesso = true;
        $this->id=isset($ingresso['id'])? $ingresso['id']:null;
        //ottengo le informazioni sul salvataggio se l'id esiste
        if(isset($this->id)){
            $this->getSalvataggio();
        }//if(isset($this->id))
        else{
            $this->idg = isset($ingresso['idg'])? $ingresso['idg']:null;
            $this->data = isset($ingresso['data'])? $ingresso['data']:null;
            $this->slot = isset($ingresso['slot'])? $ingresso['slot']:null;
            //$this->sequenza = isset($ingresso['sequenza'])? $ingresso['sequenza']:null;
            if(isset($ingresso['sequenza'])){
                if(is_array($ingresso['sequenza']))$this->setSequenza($ingresso['sequenza']);
                else $this->sequenza = $ingresso['sequenza'];
            }
            $this->tempo = isset($ingresso['tempo'])? $ingresso['tempo']:null;
            $this->spostamenti = isset($ingresso['spostamenti'])? $ingresso['spostamenti']:null;
            if($this->valida()){
                $this->inserisci();
            }//if($this->valida())
            else throw new Exception("I dati forniti non sono validi");
        }
    }//public function __construct($ingresso)

    public function __destruct(){
        if($this->connesso){
            $this->h->close();
        }
    }

    public function getId(){return $this->id;}
    public function getIdg(){return $this->idg;}
    public function getData(){return $this->data;}
    public function getSlot(){return $this->slot;}
    public function getSequenza(){
        if(isset($this->sequenza)){
            $sequenza = explode(" ", $this->sequenza);
            return $sequenza;
        }
        else return null;
        
    }
    public function getTempo(){
        if(isset($this->tempo)){
            $tempo = explode(" ",$this->tempo);
            return $tempo;
        }
        else return null;
    }
    public function getSpostamenti(){return $this->spostamenti;}
    public function getQuery(){return $this->query;}
    public function getQueries(){return $this->queries;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case 0:
                $this->error = null;
                break;
            case SALVATAGGIOERR_IDNOTEXISTS:
                $this->error = "Id non presente nell'oggetto Salvataggio";
                break;
            case SALVATAGGIOERR_QUERYERROR:
                $this->error = "Query errata";
                break;
            case SALVATAGGIOERR_INFONOTGETTED:
                $this->error = "Impossibile ottenere le informazioni sul salvataggio dal database MySql";
                break;
            case SALVATAGGIOERR_DATANOTUPDATED:
                $this->error = "Dati non aggiornati";
                break;
            case SALVATAGGIOERR_INVALIDDATAFORMAT:
                $this->error = "Uno o più parametri non sono nel formato corretto";
                break;
            case SALVATAGGIOERR_DATANOTINSERTED:
                $this->error = "Dati registrazione non inseriti nel database";
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }//public function getError()

    public function isConnesso(){return $this->connesso;}

    public function setSequenza($sequenza){
        $this->sequenza = implode(" ",$sequenza);
    }

    //crea il database se non esiste
    private function createDb($db){
        $ok = false;
        $this->query = <<<SQL
CREATE DATABASE IF NOT EXISTS {$db};
SQL;
        $this->queries[] = $this->query;
        $create = $this->h->query($this->query);
        if($create !== false)
            $ok = true;
        return $ok;
    }

    //crea la tabella se non esiste
    private function createTable(){
        $ok = false;
        $this->query = <<<SQL
SHOW TABLES LIKE '{$this->tabella}';
SQL;
        $this->queries[] = $this->query;
        $show = $this->h->query($this->query);
        if($show !== false){
            if($show->num_rows == 0){
                $this->query = <<<SQL
CREATE TABLE `{$this->tabella}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idg` int(11) NOT NULL COMMENT 'id del giocatore a cui appartiene il salvataggio',
  `data` datetime NOT NULL COMMENT 'data in cui il salvataggio è stato creato',
  `slot` int(11) NOT NULL COMMENT 'dove il giocatore vuole salvare la partita',
  `sequenza` varchar(100) NOT NULL COMMENT 'posizione di ciascuna tessera',
  `tempo` varchar(30) NOT NULL COMMENT 'tempo passato pfino al momento in cui  stato creato il salvataggio',
  `spostamenti` int(11) NOT NULL COMMENT 'numero di tessere spostate fino al momento in cui è stato creato il salvataggio',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4
SQL;
            $this->queries[] = $this->query;
            $create = $this->h->query($this->query);
            if($create !== false)
                $ok = true;
            }//if($show->num_rows == 0){
            else
                $ok = true;
        }//if($show !== false){        
        return $ok;
    }

    //ottengo le informazioni del salvataggio
    private function getSalvataggio(){
        $this->errno = 0;
        $ok = false;
        if(isset($this->id)){
            $this->query = <<<SQL
SELECT * FROM `{$this->tabella}` WHERE `id` = '{$this->id}';
SQL;
            $r = $this->h->query($this->query);
            if($r){
                if($r->num_rows == 1){
                    $salvataggio = $r->fetch_array(MYSQLI_ASSOC);
                    $this->idg = $salvataggio['idg'];
                    $this->data = $salvataggio['data'];
                    $this->slot = $salvataggio['slot'];
                    $this->sequenza = $salvataggio['sequenza'];
                    $this->tempo = $salvataggio['tempo'];
                    $this->spostamenti = $salvataggio['spostamenti'];
                    $ok = true;
                }//if($r->num_rows == 1) 
                else $this->errno = SALVATAGGIOERR_INFONOTGETTED;
                $r->free();  
            }//if($r)    
            else $this->errno = SALVATAGGIOERR_QUERYERROR; //Query errata
        }//if(isset($this->id))
        else $this->errno = SALVATAGGIOERR_IDNOTEXISTS; //Id nell'oggetto Ordine non presente
        return $ok;
    }//private function getSalvataggio()

    //crea una nuova riga con i dati passati all'oggetto
    private function inserisci(){
        $this->errno = 0;
        $ok = false;
        //controlla che lo slot non sia già occupato da un altro salvataggio
        $presente = false;
        $this->query = <<<SQL
SELECT `slot` FROM `{$this->tabella}` WHERE `idg` = '{$this->idg}' AND `slot` = '{$this->slot}';
SQL;
        $r = $this->h->query($this->query);
        if($r){
            if($r->num_rows > 0){
                $presente = true;
            }
            $r->free();
        }//if($r)
        else $this->errno = SALVATAGGIOERR_QUERYERROR; //Query errata
        if($this->errno == 0){
            //se lo slot specificato non esiste la query è una INSERT
            if(!$presente){
                $this->query = <<<SQL
INSERT INTO `{$this->tabella}` (`idg`,`data`,`slot`,`sequenza`,`tempo`,`spostamenti`)
VALUES ('{$this->idg}','{$this->data}','{$this->slot}','{$this->sequenza}','{$this->tempo}','{$this->spostamenti}');
SQL;
            }
            //se lo slot specificato esiste la query è di UPDATE
            else{
                $this->query = <<<SQL
UPDATE `{$this->tabella}`
SET `data` = '{$this->data}', `sequenza` = '{$this->sequenza}', `tempo` = '{$this->tempo}', `spostamenti` = '{$this->spostamenti}'
WHERE `slot` = '{$this->slot}' AND `idg` = '{$this->idg}';
SQL;
            }
        }
        if($this->h->query($this->query) === TRUE){
            if($this->h->affected_rows > 0){
                $ok = true;
            }
            else{
                if(!$presente)$this->errno = SALVATAGGIOERR_DATANOTINSERTED; //Dati registrazione non inseriti onel database
                else $this->errno = SALVATAGGIOERR_DATANOTUPDATED; //Dati non aggiornati
            } 
        }
        else $this->errno = SALVATAGGIOERR_QUERYERROR; //Query errata
        
    }//private function inserisci()

     //inserisce i valori contenuti nell'array $ingresso in ciascuna proprietà
     private function setValues($dati){
        $this->id=isset($dati['id'])? $dati['id']:$this->id;
        $this->idg=isset($dati['idg'])? $dati['idg']:$this->idg;
        $this->data=isset($dati['data'])? $dati['data']:$this->data;
        $this->slot=isset($dati['slot'])? $dati['slot']:$this->slot;
        $this->sequenza=isset($dati['sequenza'])? $dati['sequenza']:$this->sequenza;
        $this->tempo=isset($dati['tempo'])? $dati['tempo']:$this->tempo;
        $this->spostamenti=isset($dati['spostamenti'])? $dati['spostamenti']:$this->spostamenti;
    }

    //aggiorna uno o più valori del database
    public function update($valori,$where,$operatore = 'AND'){
        $this->errno = 0;
        $ok = false;
        if(is_array($valori) && is_array($where)){
            $query = <<<SQL
UPDATE `{$this->tabella}` SET 
SQL;
            foreach($valori as $k => $v){
                $valE = $this->h->real_escape_string($v);
                $query .= " `{$k}` = '{$valE}'";
                if($k !== array_key_last($valori)){
                    $query .= ", ";
                }
            }
            $query .= " WHERE";
            foreach($where as $k => $v){
                $valE = $this->h->real_escape_string($v);
                $query .= " `{$k}` = '{$valE}'";
                if($k !== array_key_last($where)){
                    $query .= " {$operatore} ";
                }
            }
            $query .= ";";
            $this->query = $query;
            if($this->h->query($this->query) === TRUE){
                $ok = true;
                $this->setValues($valori);
                
            }
            else $this->errno = SALVATAGGIOERR_DATANOTUPDATED; //dati non aggiornati
        }//if(is_array($valori) && is_array($where))
        else $this->errno = SALVATAGGIOERR_INVALIDDATAFORMAT; //Uno o più parametri non sono nel formato corretto
        return $ok;
    }//public function update

    /*controlla che i dati passati all'oggetto siano corretti, per essere inseriti nel database*/
    public function valida(){
        $ok = true;
        if(isset($this->id)){
            if(!preg_match(Salvataggio::$regex['id'],$this->id))$ok = false;  
        }
        if(isset($this->slot)){
            if(!preg_match(Salvataggio::$regex['slot'],$this->slot))$ok = false;
        }
        if(isset($this->tempo)){
            if(!preg_match(Salvataggio::$regex['tempo'],$this->tempo))$ok = false;
        }
        return $ok;
    }
}
?>