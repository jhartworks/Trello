<?
// Klassendefinition
class TrelloReadCards extends IPSModule {
    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create() {
        // Diese Zeile nicht löschen.
        parent::Create();
        
        $this->RegisterPropertyString("TrelloBoardId","");
        $this->RegisterPropertyString("TrelloApiKey","");
        $this->RegisterPropertyString("TrelloApiToken","");

        $this->RegisterPropertyString("BgColor","#4CAF50");
        $this->RegisterPropertyString("FontColor","white");
        $this->RegisterPropertyString("FontSize","1.5rem");
        $this->RegisterPropertyString("HoverColor","lightblue");

        $this->RegisterTimer("Update", 0, 'TRC_Update('.$this->InstanceID.');');
        $this->RegisterPropertyInteger("Updateintervall",5);
    }

    // Überschreibt die intere IPS_ApplyChanges($id) Funktion
    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
        $this->SetTimerInterval("Update", $this->ReadPropertyInteger("Updateintervall") * 60 * 1000);
    }
    /**
    * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
    * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
    *
    * DWM_SendMessage($id);
    *
    */
    public function Update() {
   
            // Discord webhook URL
            $boardid = $this->ReadPropertyString("TrelloBoardId");
            $apikey = $this->ReadPropertyString("TrelloApiKey");
            $apitoken = $this->ReadPropertyString("TrelloApiToken");
            $bgcolor = $this->ReadPropertyString("BgColor");
            $fontcolor = $this->ReadPropertyString("FontColor");
            $fontsize = $this->ReadPropertyString("FontSize");
            $hovercolor = $this->ReadPropertyString("HoverColor");

            $url ='https://api.trello.com/1/boards/'.$boardid.'/lists?key='.$apikey.'&token='.$apitoken.'';             ///get all lists on board
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            $output = curl_exec($ch);
            curl_close($ch);  
            $content = json_decode($output, true);

            
            $lists=$content;
            foreach ($lists as $list) 
            {
                
            
            $url = 'https://api.trello.com/1/lists/'.$list['id'].'/cards?key='.$apikey.'&token='.$apitoken.'';             ///get all cards on list
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            $output = curl_exec($ch);
            curl_close($ch);  
            $content = json_decode($output, true);
            ///print_r($list['name']);
            $allcardsFull[$list['name']]=$content;
            }
            ////print_r($allcardsFull);
            
            
            
            
                $style = '<style> 
            
              th {
              background-color: '.$bgcolor.';
              color: '.$fontcolor .';
                }
                      
              td {
              border-bottom: 1px solid grey;
            font-size:'.$fontsize.';
                }
     
              tr:hover {background-color: '.$hovercolor.';}
   
              </style>

            ';
            $keyindex =0;
            $keys = array_keys($allcardsFull);
            foreach ($allcardsFull as $cardfull){

                $tdhtml ='';
                

                foreach($cardfull as $card) {
                
                    $tdhtml .= '<tr><td>'.$card['name'].'</td></tr>';
                     //print_r ($name);
                
                 }
                
                 $html = $style.'<table width=100%>'.$tdhtml.'</table>';
                 $variablenID = $this->RegisterVariableString("card".$keyindex, $keys[$keyindex], "~HTMLBox",$keyindex);
                 SetValueString($variablenID,$html);

                 $keyindex ++;
            }


            
    }
}
?>