<?

class Schulferien extends IPSModule
{
 
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyString("Area", "oberoesterreich");
        $this->RegisterPropertyString("BaseURL", "http://www.schulferien.org/media/ical/oesterreich/ferien_");
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->RegisterVariableBoolean("IsSchoolHoliday", "Sind Ferien ?");
        $this->RegisterVariableString("SchoolHoliday", "Ferien");
        // 15 Minuten Timer
        $this->RegisterTimer("UpdateSchoolHolidays", 15 * 60, 'SCHOOL_Update($_IPS[\'TARGET\']);');
        // Nach übernahme der Einstellungen oder IPS-Neustart einmal Update durchführen.
        $this->Update();
    }

    private function GetFeiertag()
    {
        $jahr = date("Y") - 1;
        $link = $this->ReadPropertyString("BaseURL") . strtolower($this->ReadPropertyString("Area")) . "_" . $jahr . ".ics";
        $meldung = @file($link);
        if ($meldung === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);

        $jahr = date("Y");
        $link = $this->ReadPropertyString("BaseURL") . strtolower($this->ReadPropertyString("Area")) . "_" . $jahr . ".ics";
        $meldung2 = @file($link);
        if ($meldung2 === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);

        $meldung = array_merge($meldung, $meldung2);
        $ferien = "Keine Ferien";

        $anzahl = (count($meldung) - 1);

        for ($count = 0; $count < $anzahl; $count++)
        {
            if (strstr($meldung[$count], "SUMMARY:"))
            {
                $name = trim(substr($meldung[$count], 8));
                $start = trim(substr($meldung[$count + 1], 19));
                $ende = trim(substr($meldung[$count + 2], 17));
                $jetzt = date("Ymd") . "\n";
                if (($jetzt >= $start) and ( $jetzt <= $ende))
                {
                    $ferien = explode(' ', $name)[0];
                }
            }
        }
        return $ferien;
    }

    public function Update()
    {
        try
        {
            $holiday = $this->GetFeiertag();
        }
        catch (Exception $exc)
        {
            trigger_error($exc->getMessage(), $exc->getCode());
            return false;
        }


        $this->SetValueString("SchoolHoliday", $holiday);
        if ($holiday == "Keine Ferien")
        {
            $this->SetValueBoolean("IsSchoolHoliday", false);
        }
        else
        {
            $this->SetValueBoolean("IsSchoolHoliday", true);
        }
        return true;
    }

    protected function RegisterTimer($Name, $Interval, $Script)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            $id = 0;
        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception("Ident with name " . $Name . " is used for wrong object type", E_USER_WARNING);

            if (IPS_GetEvent($id)['EventType'] <> 1)
            {
                IPS_DeleteEvent($id);
                $id = 0;
            }
        }
        if ($id == 0)
        {
            $id = IPS_CreateEvent(1);
            IPS_SetParent($id, $this->InstanceID);
            IPS_SetIdent($id, $Name);
        }
        IPS_SetName($id, $Name);
        IPS_SetHidden($id, true);
        IPS_SetEventScript($id, $Script);
        if ($Interval > 0)
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);
            IPS_SetEventActive($id, true);
        }
        else
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, 1);
            IPS_SetEventActive($id, false);
        }
    }

    protected function UnregisterTimer($Name)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception('Timer not present', E_USER_WARNING);
            IPS_DeleteEvent($id);
        }
    }

    protected function SetTimerInterval($Name, $Interval)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            throw new Exception('Timer not present', E_USER_WARNING);
        if (!IPS_EventExists($id))
            throw new Exception('Timer not present', E_USER_WARNING);
        $Event = IPS_GetEvent($id);
        if ($Interval < 1)
        {
            if ($Event['EventActive'])
                IPS_SetEventActive($id, false);
        }
        else
        {
            if ($Event['CyclicTimeValue'] <> $Interval)
                IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);
            if (!$Event['EventActive'])
                IPS_SetEventActive($id, true);
        }
    }

    private function SetValueBoolean($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueBoolean($id) <> $value)
            SetValueBoolean($id, $value);
    }

    private function SetValueString($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueString($id) <> $value)
            SetValueString($id, $value);
    }

}

?>
