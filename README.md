**Projekt: RoyalFree — Auth Hinweise**

- **Zweck:** Kurze Hinweise, wie Admin‑Passwort sicher zu handhaben ist, wie Remember‑Tokens funktionieren und wie man das Verhalten lokal testet.

- **Wichtige Pfade:**
  - `logic/auth.php` : zentrale Auth‑Funktionen (User + Admin), Token‑Store Helper (verschoben von `includes/auth.php`)
  - `logic/remember_tokens.json` : Token‑Store (JSON, Demo). Für Produktion DB verwenden.

- **Admin‑Passwort sicher setzen**
  - Ersetze den Demo‑Hash in `logic/auth.php` durch einen selbst erzeugten Hash oder lade ihn aus einer Konfigurationsdatei / ENV.
  - Beispiel (einmalig, lokal) um einen Hash zu erzeugen (Bash / PHP):

```bash
php -r "echo password_hash('DEIN_ADMIN_PASSWORT', PASSWORD_BCRYPT) . PHP_EOL;"
```

  - Kopiere das Ergebnis und setze es in `logic/auth.php` als `ADMIN_PASS_HASH` oder (besser) lege eine `logic/config.php` an und lade dort `ADMIN_PASS_HASH` aus einer Umgebungsvariable.

- **Remember‑Tokens (sicherer Umgang)**
  - Tokens werden nun zufällig erzeugt (`bin2hex(random_bytes(32))`) und in `logic/remember_tokens.json` als Hash (`sha256`) mit Ablaufzeit gespeichert.
  - Datei‑Speicher ist nur eine Demo‑Lösung. In echten Projekten: persistente DB + Möglichkeit Token zu widerrufen.
  - Stelle sicher, dass der Webserver Schreibrechte für `logic/remember_tokens.json` hat, aber die Datei nicht öffentlich im Webroot liegt.

- **Cookies / Sicherheit**
  - Cookies werden mit `Secure` (wenn HTTPS), `HttpOnly` und `SameSite=Strict` gesetzt.
  - Teste Cookies lokal (ohne HTTPS sind `Secure`-Cookies nicht gesetzt). Verwende HTTPS in Produktion.

- **Token‑Aufräum/Cron**
  - Entferne abgelaufene Tokens regelmäßig (z. B. täglich). Ein einfaches Bash‑Script kann die Datei öffnen und abgelaufene Einträge löschen.
  - Beispiel (PHP‑Einzeiler):

```bash
php -r "\$f='logic/remember_tokens.json';\$d=file_exists(\$f)?json_decode(file_get_contents(\$f),true):[];foreach(\$d as \$k=>\$v){if(\$v['expires']<time())unset(\$d[\$k]);}file_put_contents(\$f,json_encode(\$d));"
```

- **Testanleitung**
  1. Lokale Registrierung eines Users über `register.php` (oder manuell in `$_SESSION['users']` hinzufügen).
  2. Login über `login.php` mit Haken bei "Remember Me" → `logic/remember_tokens.json` sollte neuen Token anlegen.
  3. Browser schließen/öffnen oder Session leeren → Nutzer sollte automatisch per Cookie eingeloggt werden.
  4. Admin: `admin_login.php` mit `admin@technikum-wien.at` und dem in `logic/auth.php` gesetzten Passwort testen (Demo: `admin123`).

- **Nächste Schritte / Empfehlungen**
  - Auslagern sensibler Konfigurationen in `includes/config.php` oder Umgebungsvariablen.
  - Tokens in einer DB/Tabelle speichern (mit Möglichkeit zur Invalidierung per UI).
  - Optional: Token‑Rotation bei Nutzung, Bindung an User‑Agent/IP (mit Vorsicht) und Rate‑Limiting für Loginversuche.

Wenn du möchtest, kann ich:
- eine `includes/config.php` anlegen und den Admin‑Hash dort aus einer Umgebungsvariable laden, oder
- ein kleines Cleanup‑Script (`bin/cleanup_tokens.php`) hinzufügen und einen Cron‑Eintrag vorschlagen.
