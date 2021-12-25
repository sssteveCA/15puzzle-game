<?php 

define("GIOCATOREERR_INCORRECTLOGINDATA", "1");
define("GIOCATOREERR_ACTIVEYOURACCOUNT", "2");
define("GIOCATOREERR_DATANOTUPDATED", "3");
define("GIOCATOREERR_DATANOTINSERTED", "4");
define("GIOCATOREERR_QUERYERROR", "5");
define("GIOCATOREERR_USERNAMEMAILEXIST", "6");
define("GIOCATOREERR_ACCOUNTNOTACTIVATED", "7");
define("GIOCATOREERR_MAILNOTSENT", "8");
define("GIOCATOREERR_INVALIDFIELD", "9");
define("GIOCATOREERR_DATANOTSET", "10");
define("GIOCATOREERR_ACCOUNTNOTRECOVERED", "11");
define("GIOCATOREERR_INVALIDDATAFORMAT", "12");

class Giocatore{
    private $h; //handle della connessione MySql
    private $connesso; //booleano che controlla se la connessione a MySql esiste
    private $tabella; //tabella MySql degli utenti registrati
    private $id;
    private $username; 
    private $email;
    private $password;
    private $record; //partita completata nel tempo minore in secondi
    private $codAut;
    private $cambioPwd;
    private $dataCambioPwd;
    private $loggato; //booleano che controlla se l'utente è loggato
    private $registrato; //booleano che controlla se l'utente è registrato
    private $query; //ultima query SQL inviata
    private $errno; //codice dell'errore rilevato
    private $error; //messaggio di errore
    public static $campi = array('id','email','username','codAut','cambioPwd');
    public static $regex = array(
        'id' => '/^[0-9]+$/',
        'email' => '/^[a-zA-Z-_0-9]{4,20}@([a-z]{3,15}\.){1,6}[a-z]{2,10}$/',
        'username' => '/^.+$/i',
        'codAut' => '/^[a-z0-9]{64}$/i',
        'cambioPwd' => '/^[a-z0-9]{64}$/i',
        'tempo' => '/^([0-9]+)\s(2[0-3]|1[0-9]|[0-9])\s([0-5][0-9]|[0-9])\s([0-5][0-9]|[0-9])$/'
    );

    public function __construct($dati){
        $this->errno = 0;
        $this->connesso = false;
        $mysqlHost=isset($dati['mysqlHost'])? $dati['mysqlHost']:'localhost';
        $mysqlUser=isset($dati['mysqlUser'])? $dati['mysqlUser']:'root';
        $mysqlPass=isset($dati['mysqlPass'])? $dati['mysqlPass']:'';
        $mysqlDb=isset($dati['mysqlDb'])? $dati['mysqlDb']:'stefano';
        $this->tabella=isset($dati['tabella'])? $dati['tabella']:'giocatori';
        $this->h = new mysqli($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDb);
        if($this->h->connect_errno !== 0){
            throw new Exception("Connessione a MySql fallita: ".$this->h->connect_error);
        }
        $this->h->set_charset("utf8mb4");
        $this->connesso = true;
        $this->loggato = false;
        $this->email = isset($dati['email'])? $dati['email']:null;
        $this->username = isset($dati['username'])? $dati['username']:null;
        $this->password = isset($dati['password'])? password_hash($dati['password'],PASSWORD_DEFAULT):null;
        $this->record = isset($dati['record'])? $dati['record']:null;
        $this->codAut=isset($dati['codAut'])? $dati['codAut']:null;
        $this->cambioPwd=isset($dati['cambioPwd'])? $dati['cambioPwd']:null;
        $this->dataCambioPwd=isset($dati['dataCambioPwd'])? $dati['dataCambioPwd']:null;
        $this->registrato=isset($dati['registrato'])? $dati['registrato']:false;

        //se i dati passati non sono in un formato valido
        if($this->valida() === false){
            throw new Exception("Uno o più dati inseriti non sono validi");
        }
        
        //l'utente è già registrato
        if($this->registrato){
            //il campo usato per individuare l'utente è quello 'id'
            if(!isset($dati['campo']))$dati['campo'] = Giocatore::$campi[0];
            if(in_array($dati['campo'],Giocatore::$campi) && isset($this->{$dati['campo']})){
                //se la password è stata dimenticata
                if(isset($dati['dimenticata']) && $dati['dimenticata']){
                    //reimpostazione della password
                    if(isset($this->cambioPwd,$this->dataCambioPwd)){
                        $this->recupera($dati);
                    }
                     //se non esiste già il codice di recupero password, viene creato e poi in una mail il link per il ripristino
                    else{
                        $this->cambioPwd = $this->codAutGen('1');
                        $this->dataCambioPwd = time();
                    }
                }//if(isset($dati['dimenticata']) && $dati['dimenticata'])
                //il giocatore deve completare la registrazione attivando l'account (attiva.php)
                else if(isset($dati['codAut']) && preg_match(Giocatore::$regex['codAut'],$dati['codAut'])){
                    $this->setCodAut($dati['codAut']);
                    if($this->attiva()){
                        $this->setCodAut(null);
                    }
                }
                //tentativo di login del giocatore
                else{
                    $valori = array();
                     //il giocatore vuole accedere alla sua area personale
                     if(isset($dati[$dati['campo']]) && preg_match(Giocatore::$regex[$dati['campo']],$dati[$dati['campo']])){
                         $valori[$dati['campo']] = $dati[$dati['campo']];
                         $player = $this->getData($valori);
                         //lo username esiste
                         if($player !== FALSE){
                             //evita di criptare una password già criptata
                             unset($player['password']);
                             //inserisco i valori ottenuti dalla riga MySql nell'oggetto
                            $this->setValues($player);
                            //se la password è corretta
                            if(isset($dati['password']) && password_verify($dati['password'],$this->password)){
                                $codiceAut = $this->getCodAut();
                                //se l'account è già stato attivato
                                if(is_null($codiceAut) || empty($codiceAut)){
                                    $this->loggato = true;
                                }
                                //l'account non è ancora stato attivato
                                else $this->errno = GIOCATOREERR_ACTIVEYOURACCOUNT; //il giocatore deve prima attivare l'account

                            }// if(isset($dati['password']) && password_verify($dati['password'],$player['password']))
                            else{
                                $this->errno = GIOCATOREERR_INCORRECTLOGINDATA; //username o password non corretti
                            }
                            //aggiorno il record personale del giocatore
                            if(isset($this->record)){

                            }
                        }//if($player !== FALSE)
                        else{
                            $this->errno = GIOCATOREERR_INCORRECTLOGINDATA; //username o password non corretti
                        }
                     }//if(isset($dati[$dati['campo']]) && preg_match(Giocatore::$regex[$dati['campo']],$dati[$dati['campo']]))
                     else{
                        $this->errno = GIOCATOREERR_DATANOTSET; //uno o più dati richiesti non sono stati settati
                    }
                }
            }//if(in_array($dati['campo'],Utente::$campi) && isset($this->{$dati['campo']}))
            //errore, uno o più campi richiesti non sono stati impostati
            else {
                $this->errno = GIOCATOREERR_INVALIDFIELD; //non è stato specificato un campo per fare la selezione dei dati oppure non è un campo valido
            }
        }//if($this->registrato)
        //utente non registrato
        else{
            //utente che si registra
            if(isset($dati['email'],$dati['username'],$dati['password'])){
                if($this->valida($dati)){
                    $where = "`email` = '{$dati['email']}'";
                    $mailexists = $this->esiste($where);
                    //controllo che il nome utente non esista già
                    $where = "`username` = '{$dati['username']}'";
                    //controlla che la mail non esista già
                    $userexists = $this->esiste($where);
                    if($mailexists == 0 && $userexists == 0){
                        //codice di attivazione
                        $this->setCodAut($this->codAutGen('0'));
                        $dati['codAut'] = $this->getCodAut();
                        //$dati['registrato'] non deve essere inserito nel database
                        unset($dati['registrato']);
                        //se i dati sono stati inseriti nel database
                        $dati['password'] = $this->password;
                        if($this->inserisci($dati)){
                            $this->setValues($dati);
                        }
                    }
                    //(registrazione) lo username o la mail inserita esistono già
                    else $this->errno = GIOCATOREERR_USERNAMEMAILEXIST;
                }
                //Uno o più parametri non sono nel formato corretto
                else $this->errno = GIOCATOREERR_INVALIDDATAFORMAT;
            }//if(isset($dati['email'],$dati['username']))
            else{
                $this->errno = GIOCATOREERR_DATANOTSET; //uno o più dati richiesti non sono stati settati
            }
        } //else (utente non registrato)
    }//public function __construct

    public function __destruct(){
        if($this->connesso)$this->h->close();
    }

    public function __serialize(){
        return[
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'sequenza' => $this->sequenza,
            'tempo' => $this->tempo,
            'spostamenti' => $this->spostamenti,
            'record' => $this->record,
            'codAut' => $this->codAut,
            'cambioPwd' => $this->cambioPwd,
            'dataCambioPwd' => $this->dataCambioPwd
        ];
    }

    public function __unserialize($data){
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->sequenza = $data['sequenza'];
        $this->tempo = $data['tempo'];
        $this->spostamenti = $data['spostamenti'];
        $this->record = $data['record'];
        $this->codAut = $data['codAut'];
        $this->cambioPwd = $data['cambioPwd'];
        $this->dataCambioPwd = $data['dataCambioPwd'];  
    }

    public function getId(){return $this->id;}
    public function getEmail(){return $this->email;}
    public function getUsername(){return $this->username;}
    public function getPassword(){return $this->password;}
    public function getRecord(){return $this->record;}
    public function getCodAut(){return $this->codAut;}
    public function getCambioPwd(){return $this->cambioPwd;}
    public function getDataCambioPwd(){return $this->dataCambioPwd;}
    public function getQuery(){return $this->query;}
    public function getTabella(){return $this->tabella;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case 0:
                $this->error = null;
                break;
            case GIOCATOREERR_INCORRECTLOGINDATA:
                $this->error = "email o password non corretti";
                break;
            case GIOCATOREERR_ACTIVEYOURACCOUNT:
                $this->error = "Devi prima attivare l'account";
                break;
            case GIOCATOREERR_DATANOTUPDATED:
                $this->error = "dati non aggiornati";
                break;
            case GIOCATOREERR_DATANOTINSERTED:
                $this->error = "dati registrazione non inseriti nel database";
                break;
            case GIOCATOREERR_QUERYERROR:
                $this->error = "query errata";
                break;
            case GIOCATOREERR_USERNAMEMAILEXIST:
                $this->error = "lo username o la mail inserita esistono già";
                break;
            case GIOCATOREERR_ACCOUNTNOTACTIVATED:
                $this->error = "attivazione account non riuscita";
                break;
            case GIOCATOREERR_MAILNOTSENT:
                $this->error = "email non inviata";
                break;
            case GIOCATOREERR_INVALIDFIELD:
                $this->error = "non è stato specificato un campo per fare la selezione dei dati oppure non è un campo valido";
                break;
            case GIOCATOREERR_DATANOTSET:
                $this->error = "uno o più dati richiesti non sono stati settati";
                break;
            case GIOCATOREERR_ACCOUNTNOTRECOVERED:
                $this->error = "impossibile recuperare l'account";
                break;
            case GIOCATOREERR_INVALIDDATAFORMAT:
                $this->error = "Uno o più parametri non sono nel formato corretto";
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }//public function getError

    public function isConnesso(){return $this->connesso;}
    public function isLogged(){return $this->loggato;}

    //inserisce il record personale del giocatore
    public function setRecord($record){
        $ok = false;
        $this->errno = 0;
        if(is_numeric($record)){
            $query = <<<SQL
UPDATE `{$this->tabella}` SET `record` = '{$record}' 
WHERE `username` = '{$this->username}' 
AND (`record` IS NULL OR `record` > '{$record}');
SQL;
            $this->query = $query;
            if($this->h->query($query) !== FALSE){
                $ok = true;
                if($this->h->affected_rows == 1){
                    $this->record = $record;
                }
                else $this->errno = GIOCATOREERR_DATANOTUPDATED; //dati non aggiornati
            }
            else $this->errno = GIOCATOREERR_QUERYERROR; //query errata 
        }
        else $this->errno = GIOCATOREERR_INVALIDDATAFORMAT; //Uno o più parametri non sono nel formato corretto
        return $ok;
    }

    private function setCodAut($codAut){$this->codAut = $codAut;}

     //attivazione dell'account
     private function attiva(){
        $ok = false;
        $this->errno = 0;
        $codAut = $this->codAut;
        //apro la connessione al server MySQL
        $query = <<<SQL
UPDATE `{$this->tabella}` SET `codAut` = NULL WHERE `codAut` = '{$codAut}';
SQL;
        //echo "{$query}<br>";
        $this->query = $query;
        if($this->h->query($query) !== FALSE){
            if($this->h->affected_rows == 1){
                $ok = true;
            }
            else $this->errno = GIOCATOREERR_ACCOUNTNOTACTIVATED; //attivazione account non riuscita
        }
        else $this->errno = GIOCATOREERR_QUERYERROR; //query errata    
        return $ok;
    }

    //crea il codice di attivazione o di recupero password dell'account 
    public function codAutGen($ordine){
        $codAut = str_replace('.','a',microtime());
        $codAut = str_replace(' ','b',$codAut);
        $lCod = strlen($codAut);
        $lCas = 64 - $lCod;
        $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYzabcdefghijklmnopqrstuvwxyz0123456789';
        $lc = strlen($c) - 1;
        $s = '';
        for($i = 0; $i < $lCas; $i++)
        {
            $j = mt_rand(0,$lc);
            $s .= $c[$j];
        }
        if($ordine == '0') return $codAut.$s;
        else return $s.$codAut;
    }

    /*true se il valore indicato si trova in un determinato campo
    1 = il campo ha già quel valore
    0 = il campo non ha quel valore
    -1 = errore */
    public function esiste($where){
        $this->errno = 0;
        $query = <<<SQL
SELECT * FROM `{$this->tabella}` WHERE {$where};
SQL;
        $this->query = $query;
        $r = $this->h->query($query);
        if($r){
            if($r->num_rows > 0){
                $ret = 1; //il valore indicato nel campo specificato esiste già
            }
            else $ret = 0;
        }
        else{
            $ret = -1;
            $this->errno = GIOCATOREERR_QUERYERROR; //query errata
        }
        return $ret;
    }//public function Exists

    /*ottengo tutti i dati dell'utente
    $where: array che contiene i campi(le chiavi) e i valori da sottoporre alla clausola WHERE */
    private function getData($where){
        $this->errno = 0;
        $giocatore = false;
        //nessun errore
        $query = <<<SQL
SELECT * FROM `{$this->tabella}` WHERE 
SQL;
        foreach($where as $k => $v){
            $valE = $this->h->real_escape_string($v);
            $query .= " `{$k}` = '{$valE}'";
            //se non è l'ultima chiave dell'array aggiungo l'operatore AND
            if($k !== array_key_last($where))$query .= " AND";
        }
        $query .= ";";
        $this->query = $query;
        //echo "{$query}<br>";
        $r = $this->h->query($query);
        //echo $this->h->error.'<br>';
        if($r){
            if($r->num_rows == 1){
                $giocatore = $r->fetch_array(MYSQLI_ASSOC);
            }
            $r->free();
        }
        else{
            $this->errno = GIOCATOREERR_QUERYERROR; //query errata
        }
        return $giocatore;
    }//public function getData

    //inserisce i valori passati nella tabella mySql dei giocatori
    public function inserisci($dati){
        $this->errno = 0;
        $ok = false;
        //nessun errore
        $campi = '';
        $valori = '';
        foreach($dati as $k => $v){
            $campi .= "`{$k}`";
            $valE = $this->h->real_escape_string($v);
            $valori .= "'{$valE}'";
            if($k !== array_key_last($dati)){
                $campi .= ",";
                $valori .= ",";
            }
        }
        $query = <<<SQL
INSERT INTO `{$this->tabella}` ({$campi}) VALUES ({$valori});
SQL;
        $this->query = $query;
        if($this->h->query($query) === TRUE){
            if($this->h->affected_rows > 0){
                $ok = true;
            }
            else $this->errno = GIOCATOREERR_DATANOTINSERTED; //dati registrazione non inseriti nel database
        }
        else $this->errno = GIOCATOREERR_QUERYERROR; //query errata
        return $ok;
    }

    //recupera l'account reimpostando la password
    private function recupera($dati){
        $this->errno = 0;
        $ok = false;
        if(isset($dati['nuovaP']) && $dati['nuovaP'] != ''){
            $nuovaC = password_hash($dati['nuovaP'],PASSWORD_DEFAULT);
            $query = <<<SQL
UPDATE `{$this->tabella}` SET `dataCambioPwd` = NULL, `cambioPwd` = NULL, `password` = '{$nuovaC}'
WHERE `cambioPwd` = '{$this->cambioPwd}' AND `dataCambioPwd` >= '{$this->dataCambioPwd}';
SQL;
            $this->query = $query;
            if($this->h->query($query) !== FALSE){
                if($this->h->affected_rows == 1){
                    $ok = true;
                }
                else $this->errno = GIOCATOREERR_ACCOUNTNOTRECOVERED; //impossibile recuperare l'account
            }
            else $this->errno = GIOCATOREERR_QUERYERROR; //query errata
        }
        else $this->errno = GIOCATOREERR_DATANOTSET; //uno o più dati richiesti non sono stati settati
        return $ok;
    }

    //il giocatore invia una mail
    public function sendEmail($to,$subject,$body,$headers){
        $this->errno = 0;
        $from = $this->getEmail();
        $send = @mail($to,$subject,$body,$headers);
        if(!$send) $this->errno = GIOCATOREERR_MAILNOTSENT; //email non inviata
        return $send;
    }

    //inserisce i valori contenuti nell'array $ingresso in ciascuna proprietà
    private function setValues($dati){
        $this->id=isset($dati['id'])? $dati['id']:$this->id;
        $this->email=isset($dati['email'])? $dati['email']:$this->email;
        $this->username=isset($dati['username'])? $dati['username']:$this->username;
        $this->password=isset($dati['password'])? password_hash($dati['password'],PASSWORD_DEFAULT):$this->password;
        $this->record= isset($dati['record'])? $dati['record']:$this->record;
        $this->codAut=isset($dati['codAut'])? $dati['codAut']: $this->codAut;
        $this->cambioPwd=isset($dati['cambioPwd'])? $dati['cambioPwd']: $this->cambioPwd;
        $this->dataCambioPwd=isset($dati['dataCambioPwd'])? $dati['dataCambioPwd']:$this->dataCambioPwd;
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
                if($k == 'password'){
                    $v = password_hash($v,PASSWORD_DEFAULT);
                }
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
            if($this->h->query($query) === TRUE){
                $ok = true;
                $this->setValues($valori);
                
            }
            else $this->errno = GIOCATOREERR_DATANOTUPDATED; //dati non aggiornati
        }//if(is_array($valori) && is_array($where))
        return $ok;
    }//public function update

    /*controlla che i dati passati all'oggetto siano corretti, per essere inseriti nel database*/
    public function valida(){
        $ok = true;
        if(isset($this->id)){
            if(!preg_match(Giocatore::$regex['id'],$this->id))$ok = false;  
        }
        if(isset($this->email)){
            if(!preg_match(Giocatore::$regex['email'],$this->email))$ok = false;
        }
        if(isset($this->username)){
            if(!preg_match(Giocatore::$regex['username'],$this->username))$ok = false;
        }
        if(isset($this->record)){
            if(!is_numeric($this->record))$ok = false;
        }
        if(isset($this->codAut)){
            if(!preg_match(Giocatore::$regex['codAut'],$this->codAut))$ok = false;
        }
        if(isset($this->cambioPwd)){
            if(!preg_match(Giocatore::$regex['cambioPwd'],$this->cambioPwd))$ok = false;
        }
        return $ok;
    }

}

?>