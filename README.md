# Schulferien

Dieses Modul zeigt an, ob es sich heute um einen Ferientag handelt.
Im Konfigurationsformular muss das Bundesland ausgewählt werden.
Es handelt sich hier um eine Umsetzung von dem Schulferien-Script von kronos aus dem IP-Symcon-Forum.  
[IP-Symcon Forum: Schulferien](https://www.symcon.de/forum/threads/20398-Schulferien)  

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Funktionsreferenz](#4-funktionsreferenz) 
5. [Anhang](#5-anhang)

## 1. Funktionsumfang

 Über die Webseite [http://www.schulferien.org](http://www.schulferien.org) wird ermittelt ob heute Schulferien sind.  
 Entsprechend werden die beiden Statusvariablen mit Werten gefüllt.  

## 2. Voraussetzungen

 - IPS 4.x
 
## 3. Installation

   - IPS 3.x  
        Kein Modul, aber das Original-Script aus dem Forum kann genutzt werden.
        [IP-Symcon Forum: Schulferien](https://www.symcon.de/forum/threads/20398-Schulferien)

   - IPS 4.x  
        Über das 'Modul Control' folgende URL hinzufügen:  
        `git://github.com/Nall-chan/IPSSchulferien.git`  


## 4. Funktionsreferenz

```php
SCHOOL_Update( integer $InstanceID );
```
 Startet eine neue Prüfung ob Ferien sind.  

## 5. Anhang

**GUID's:**  
 `{3B2628A3-AA47-431F-BF65-074C7002174B}`

**Changelog:**  
 Version 1.0:
  - Erstes Release

**Danksagung:**  
 An kronos aus dem IPS-Forum für das Original-Script.  
[IP-Symcon Forum: Schulferien](https://www.symcon.de/forum/threads/20398-Schulferien)
