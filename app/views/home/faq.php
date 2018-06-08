<?php
require_once '../app/views/header.php';
?>

<h3>FAQ: Vaak gestelde vragen</h3>
Eerst en vooral: mijnscore.be is een webdevelopment oefening en daarom geen echte website, game of tool.
Omdat mijnscore.be nog volop in ontwikkeling is, kan het zijn dat er hier en daar iets niet werkt.<br />
<br />
<h3>Wat is mijnscore.be?</h3>
Op mijnscore.be kan je je pronostiek invullen voor wedstrijden van het Belgische eersteklassevoetbal (en andere interessante wedstrijden/toernooien). <br />
<br />
Je krijgt punten door juiste pronostieken in te vullen. Hoe meer punten je verzamelt, hoe hoger je eindigt in het klassement. Er is een klassement per speeldag en een eindklassement per competitie/toernooi.<br />
<br />
<h3>Hoe kan ik een fout melden of verbetering voorstellen?</h3>
Door een <a href="https://github.com/MaxVMH/mijnscore/">issue te openen op github</a> of een berichtje achter te laten in <a href="http://www.9lives.be/forum/webdesign-webdevelopment/1050659-voetbalpronostiek.html">deze thread</a> of in <a href="https://www.9lives.be/forum/voetbal/1088017-prono-en-nabesprekingsthread-2017-2018-a.html">de prono- en nabesprekingthread</a> op 9lives.<br />
<br />

<hr />

<h3>Hoe vul ik mijn pronostiek in?</h3>
Maak <a href="register/form">een nieuw account</a> (mocht je dat nog niet hebben), <a href="login/form">log in</a>, ga naar pronostiek in het menu en selecteer de gewenste competitie.<br />
<br />
<h3>Tot wanneer kan ik mijn pronostiek invullen?</h3>
Tot wanneer de wedstrijd aanvangt (zie het aangegeven tijdstip naast de wedstrijd).
Vul je pronostiek op tijd in om teleurstellingen te voorkomen.<br />
Het huidige tijdstip volgens onze server kan je zien op je profielpagina (naast "laatst online").<br />
<br />
<h3>Kan ik mijn pronostiek wijzigen?</h3>
Ja, tot wanneer de wedstrijd aanvangt (het aangegeven tijdstip naast de wedstrijd).<br />
<br />
<h3>Hoe worden de punten berekend?</h3>
<ul>
	<li>0 punten voor een pronostiek met verkeerde uitslag.</li>
	<li>1 punt voor een pronostiek met verkeerde uitslag maar met juiste winnaar/verliezer/gelijkspel.</li>
	<li>3 punten voor een pronostiek met juiste uitslag: 1-0, 0-1, 2-0, 0-2, 2-1, 1-2 of 1-1</li>
	<li>4 punten voor een pronostiek met juiste uitslag: 3-0, 0-3, 3-1, 1-3 of 0-0</li>
	<li>5 punten voor een pronostiek met juiste uitslag: 2-2, 3-2 of 2-3</li>
	<li>6 punten voor een pronostiek met juiste uitslag: 4-0, 0-4, 4-1 of 1-4</li>
	<li>7 punten voor een pronostiek met juiste uitslag die hierboven niet vermeld staat.</li>
</ul>
De kolom "punten" naast je pronostiek geeft het aantal punten weer van zodra we de wedstrijdresultaten binnen hebben.<br />
<br />
<h3>Kan ik een andere gebruiker zijn pronostiek bekijken?</h3>
Ja. Via de gebruiker zijn/haar profiel (je kan de <a href="users/search">gebruikersnaam opzoeken</a>), of door in <a href="predictions/score">het scorebord</a> op de gebruiker op zijn/haar score te klikken.<br />
<br />

<hr />

<h3>Waarom is mijn account geblokkeerd?</h3>
Wanneer er meerdere keren na elkaar een fout wachtwoord wordt ingegeven, blokkeren we je account tijdelijk om te voorkomen dat iemand je wachtwoord probeert te raden.
Wanneer een account geblokkeerd is, tonen we hoeveel pogingen er geweest zijn en hoe lang we je account nog blokkeren.<br />
<br />
<h3>Waarom is mijn account verwijderd?</h3>
Om het testen van mijnscore in goede banen te leiden, komen de volgende accounts in aanmerking voor verwijdering: <br />
<ul>
	<li>Accounts die de eerste 2 dagen (48 uur) na registratie niet inloggen.</li>
	<li>Accounts die 90 opeenvolgende dagen niet inloggen.</li>
	<li>Accounts waarvan de gebruiker niet deelneemt aan de 9lives voetbalpronostiek.</li>
</ul>
Werd je account verwijderd en denk je dat het om een vergissing gaat, neem dan contact op via <a href="http://www.9lives.be/forum/webdesign-webdevelopment/1050659-voetbalpronostiek.html">deze thread</a> of in <a href="https://www.9lives.be/forum/voetbal/1088017-prono-en-nabesprekingsthread-2017-2018-a.html">de prono- en nabesprekingthread</a> op 9lives.<br />
<br />

<hr />

<h3>Welke data houdt mijnscore bij?</h3>
Van bezoekers die niet ingelogd zijn, wordt er geen data bijgehouden en worden er geen cookies geplaatst. <br />
<br />
Van bezoekers die zich registreren/inloggen wordt de volgende data bijgehouden:
<ul>
	<li>Gebruikersnaam en hash van het wachtwoord (om u te kunnen inloggen).</li>
	<li>Er wordt een cookie op uw computer geplaatst met een ID en token (om u te herkennen en ingelogd te houden voor een beperkte tijd).</li>
	<li>E-mail adres (om, indien u daarvoor kiest in uw accountinstellingen, e-mail herinneringen te kunnen sturen).</li>
	<li>Het tijdstip waarop u het laatst inlogde (om inactieve accounts te kunnen verwijderen).</li>
	<li>IP-adres van de verbinding waarmee u registreert (om misbruik tegen te gaan).</li>
	<li>Het tijdstip waarop iemand een fout wachtwoord voor uw account ingeeft, en het aantal keren dat dit voorvalt (om misbruik tegen te gaan).</li>
</ul>
Indien u uw gegevens wilt laten aanpassen of laten verwijderen, stuur dan een berichtje naar Destel op 9lives of mijnscore. <br />
<h4>U zal geen e-mails ontvangen van mijnscore.be, tenzij u daarvoor kiest in Account -> Bewerk e-mailadres.</h4>

<?php
require_once '../app/views/footer.php';
?>
