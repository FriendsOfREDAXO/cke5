<?php
/** @var rex_addon $this */

use Cke5\Provider\Cke5NavigationProvider;
use Cke5\Utils\Cke5Lang;

$content = '<div class="document-outline-container-not-provided"></div>';
$content .= Cke5NavigationProvider::getMainSubNavigationHeader() .
           Cke5NavigationProvider::getSubNavigation('main') . '
<div class="cke5-demo">
    <div name="content" id="editor" class="cke5-editor" data-profile="default" data-lang="' . Cke5Lang::getUserLang() . '">
        <h1>😀 Space Shuttle Programm</h1>

        <figure class="media"><oembed url="https://vimeo.com/5080397"></oembed></figure>
    
        <h2>Inhalt</h2>
        <ol>
            <li>Die Story<ol style="list-style-type:upper-roman;">
                    <li>Zu den wichtigsten Erfolgen gehören</li>
                    <li>Der letzte Shuttle-Flug</li>
                </ol>
            </li>
            <li>Nutzung des Orbiter</li>
            <li>Satellitentransport</li>
            <li>Wissenschaft</li>
            <li>Betrieb von Raumstationen</li>
            <li>Wartung und Aufrüstung</li>
            <li>Missionen<br>&nbsp;</li>
        </ol>

        <h2>Die Story</h2>

        <p>Nach dem letzten <a href="https://de.wikipedia.org/wiki/Apollo-Programm" title="Apollo-Programm">Apollo</a>-Flug 1975 war das Shuttle ab 1981 das Arbeitspferd der NASA. Der erste Flug des Systems fand am 12. April 1981 statt, seither wurden insgesamt 135 Flüge durchgeführt, wobei es zu zwei fatalen Unfällen kam; bei beiden starben die jeweils sieben Besatzungsmitglieder und die Raumfähren gingen verloren.</p>
        
        <p><strong>Zu den wichtigsten Erfolgen gehören&nbsp;</strong></p>
        <ol style="list-style-type:lower-latin;">
            <li>die Aussetzung diverser Raumsonden</li>
            <li>sowie des <a href="https://de.wikipedia.org/wiki/Hubble-Weltraumteleskop">Hubble-Weltraumteleskops</a>,</li>
            <li>diverse Flüge mit eingebauten Laboratorien sowie Flüge zur Mir und zur ISS.</li>
            <li>Insgesamt wurden fünf raumflugfähige Orbiter gebaut,</li>
            <li>sowie ein weiterer nicht weltraumtauglicher für <a
                    href="https://de.wikipedia.org/wiki/Approach_and_Landing_Tests">atmosphärische Flugtests</a>.</li>
        </ol>
        
        <p><strong>Der letzte Shuttle-Flug&nbsp;</strong></p>
        <p>startete am 8. Juli 2011; mit der Landung der <a
                href="https://de.wikipedia.org/wiki/Atlantis_(Raumf%C3%A4hre)">Atlantis</a> am 21. Juli 2011 ging die Ära
            der Space Shuttles zu Ende.</p>
        
        <h2>Nutzung des Orbiter</h2>
        
        <blockquote>
            <p><strong>Das einzige Trägersystem für mehrere Tonnen Nutzlast</strong></p>
        </blockquote>

        <p>Durch seine Bauart als Raumfähre bedingt war das Space Shuttle extrem flexibel einsetzbar. Es war das einzige Trägersystem, das in der Lage war, mehrere Tonnen Nutzlast vom Weltraum zur Erde zu bringen. Zudem konnten einige Komponenten der Raumstation ISS aufgrund ihrer Abmessungen nur mit dem Shuttle ins All gebracht werden. Dieser Umstand sowie die sich daraus ergebenden Verträge mit den Partnerländern waren auch einer der Hauptgründe, warum das Space-Shuttle-Programm trotz massiven Kostenüberschreitungen unterhalten wurde. Im Verlauf des Shuttleprogramms haben sich die Aufgaben des Systems recht stark gewandelt. Im Folgenden wird eine Übersicht über die wichtigsten Aufgaben des Shuttle gegeben.</p>
        
        <figure class="image ck-widget image-style-align-right" style="width:50%;" contenteditable="false"><img src="/assets/addons/cke5/images/Space_Shuttle_Program_Commemorative_Patch.png"><figcaption class="ck-editor__editable ck-editor__nested-editable" contenteditable="true" data-placeholder="Bildunterschrift eingeben">Das Emblem der NASA zur Erinnerung an das Space-Shuttle-Programm | NASA/Blake Dumesnil | From Wikimedia Commons, the free media repository | To celebrate the upcoming 30th anniversary and retirement of the Space Shuttle Program, the design of this patch aims to capture the visual essence and spirit of the program in an iconic and triumphant manner. As the Space Shuttle Program has been an innovative, iconic gem in the history of American spaceflight, the overall shape of the patch and its faceted panels are reminiscent of a diamond or other fine jewel. The shape of the patch fans out from a fine point at the bottom to a wide array across the top, to evoke the vastness of space and our aim to explore it, as the Shuttle has done successfully for decades. The outlined blue circle represents the Shuttle\'s exploration within low Earth orbit, but also creates a dynamic fluidity from the bottom right around to the top left to allude to the smoothness of the Shuttle orbiting the earth. The diagonal lines cascading down into the top right corner of the design form the American Flag as the Shuttle has been one of the most recognizable icons in American history over the last three decades. In the top left and right panels of the design, there are seven prominent stars on each side which represent the 14 crew members that were lost on shuttles Challenger and Columbia. Inside of the middle panel to the right of the Shuttle, there are five larger, more prominent stars that signify the five Space Shuttle vehicles NASA has had in its fleet throughout the program. Most importantly though, this patch is as an overall celebration of the much-beloved program and vehicle that so many people have dedicated themselves to in so many capacities over the years with a sense of vibrancy and mysticism that the Space Shuttle Program will always be remembered by. This patch was designed by Aerospace Engineer Blake Dumesnil, who has supported the Space Shuttle Program with his work in the Avionics and Energy Systems Divisions of the NASA Johnson Space Center Engineering Directorate. It is the winning entry in a commemorative patch design contest sponsored by the Space Shuttle Program.</figcaption></figure>
        
        <h2>Satellitentransport</h2>
        
        <p>Zu Beginn des Shuttle-Programms lag die Hauptaufgabe des Orbiters darin, Satelliten ins All zu bringen. Durch die Wiederverwendbarkeit hatte man sich enorme Einsparungen erhofft. So waren auch die ersten operationellen Flüge des Space Shuttle dieser Aufgabe gewidmet. Während der Mission <a href="https://de.wikipedia.org/wiki/STS-5" title="STS-5">STS-5</a> wurden etwa die beiden Nachrichtensatelliten <a href="https://de.wikipedia.org/wiki/Anik_(Satellit)" title="Anik (Satellit)">Anik C-3</a> und SBS-C ins All gebracht. Auch die drei nachfolgenden Missionen wurden für den Satellitentransport eingesetzt.</p>
        
        <p>Daneben hatte das Shuttle die einzigartige Fähigkeit, auch Satelliten vom All zur Erde zurückbringen zu können. Das geschah erstmals auf der Mission <a href="https://de.wikipedia.org/wiki/STS-51-A" title="STS-51-A">STS-51-A</a>, als zwei Satelliten, die zuvor auf zu niedriger Umlaufbahn ausgesetzt worden waren, wieder eingefangen wurden. Zudem konnte man mit dem Shuttle auch Satelliten einfangen, um sie durch Astronauten reparieren zu lassen. Das wurde zum Beispiel während der Mission <a href="https://de.wikipedia.org/wiki/STS-49" title="STS-49">STS-49</a> durchgeführt, als die Oberstufe des Intelsat-IV-Satelliten ausgetauscht wurde.</p>
        
        <p>Ein anderes Beispiel war das <a href="https://de.wikipedia.org/wiki/Hubble-Weltraumteleskop" title="Hubble-Weltraumteleskop">Hubble-Weltraumteleskop</a>, das fünfmal von einem Space Shuttle zwecks Reparatur angeflogen wurde. Den letzten Besuch hat das Teleskop im Jahr 2009 von der Mission <a href="https://de.wikipedia.org/wiki/STS-125" title="STS-125">STS-125</a> erhalten.</p>
        
        <p>Seit dem <a href="https://de.wikipedia.org/wiki/STS-51-L" title="STS-51-L">Challenger-Unglück</a> im Jahre 1986 wurde das Shuttle aus dem kommerziellen Satellitengeschäft zurückgezogen. Seither wurden damit nur noch militärische, wissenschaftliche oder staatliche Nutzlasten in den Orbit gebracht. Die letzte Shuttle-Mission, die in erster Linie dem Transport eines Satelliten gewidmet war, war <a href="https://de.wikipedia.org/wiki/STS-93" title="STS-93">STS-93</a> im Sommer 1999. Während dieser Mission wurde das Röntgen-Teleskop <a href="https://de.wikipedia.org/wiki/Chandra_(Teleskop)" title="Chandra (Teleskop)">Chandra</a> ins All gebracht.</p>
        
        <figure class="image ck-widget image-style-align-right" style="width:50%;" contenteditable="false"><img src="/assets/addons/cke5/images/STS-103_Hubble_EVA.jpg"><figcaption class="ck-editor__editable ck-editor__nested-editable" contenteditable="true" data-placeholder="Bildunterschrift eingeben">Arbeiten am Hubble-Teleskop während STS-103 | NASA | From Wikimedia Commons, the free media repository | Astronauts Steven L. Smith, and John M. Grunsfeld, appear as small figures in this wide scene photographed during extravehicular activity (EVA). On this space walk they are replacing gyroscopes, contained in rate sensor units (RSU), inside the Hubble Space Telescope. A wide expanse of waters, partially covered by clouds, provides the backdrop for the photograph.</figcaption></figure>
        
        <h2>Wissenschaft</h2>
        
        <p>Ein weiteres wichtiges Einsatzgebiet des Shuttle war die Wissenschaft in der Schwerelosigkeit. Die Raumfähre bot eine sehr flexible Plattform für Experimente aller Art. Zunächst ist das <a href="https://de.wikipedia.org/wiki/Spacelab" title="Spacelab">Spacelab</a> zu nennen, ein Labor, das in der Nutzlastbucht mitgeführt werden konnte. Der erste Spacelab-Flug war <a href="https://de.wikipedia.org/wiki/STS-9" title="STS-9">STS-9</a> im November 1983. Bis zum letzten Flug im Jahr 1998 an Bord des Fluges <a href="https://de.wikipedia.org/wiki/STS-90" title="STS-90">STS-90</a>, wurden 22 Spacelabflüge durchgeführt.</p>
        
        <p>Nachfolger des Spacelab war das <a href="https://de.wikipedia.org/wiki/Spacehab" title="Spacehab">Spacehab</a>. Dieses konnte vielseitiger eingesetzt werden als das Spacelab – so konnte man damit beispielsweise auch Fracht zur ISS bringen, wie es etwa auf dem Flug <a href="https://de.wikipedia.org/wiki/STS-105" title="STS-105">STS-105</a> der Fall war. Die letzte reine Forschungsmission des Shuttleprogramms war <a href="https://de.wikipedia.org/wiki/STS-107" title="STS-107">STS-107</a> der Columbia, die dann beim Wiedereintritt in die Atmosphäre auseinanderbrach und teilweise verglühte, wobei die sieben Astronauten an Bord ums Leben kamen. Der letzte Flug eines Spacehab-Logistikmoduls war die Mission <a href="https://de.wikipedia.org/wiki/STS-118" title="STS-118">STS-118</a>.</p>
        
        <p>Auf anderen Missionen, zum Beispiel während <a href="https://de.wikipedia.org/wiki/STS-7" title="STS-7">STS-7</a>, wurden Forschungsplattformen in der Nutzlastbucht mitgetragen, die dann während der Mission für mehrere Stunden in den Weltraum entlassen wurden, um danach mit dem Roboterarm wieder eingefangen zu werden. Wieder andere solcher Plattformen blieben gleich für mehrere Monate oder Jahre im All und wurden von einer späteren Shuttle-Mission wieder eingeholt.</p>
        
        <p>Grundsätzlich hatten die meisten Shuttle-Missionen zu einem Teil wissenschaftliche Missionsziele. Oft wurden in der Nutzlastbucht sogenannte Get-Away-Behälter mit automatisch ablaufenden Experimenten mitgeführt, oder man hatte sogenannte <i>Middeck Payloads</i>, also Mitteldeck-Nutzlast dabei, die von der Shuttle-Crew nebenbei betreut wurde. Das war auch bei ISS-Flügen teilweise noch der Fall.</p>
        
        <h2>Betrieb von Raumstationen</h2>
        
        <p>Aufgrund seiner unvergleichlichen Flexibilität war das Shuttle ein ideales Arbeitspferd für den Aufbau und die Wartung einer großen Raumstation. Viele Module der ISS waren so groß, dass sie nicht mit anderen Trägern ins All gebracht werden konnten. Zudem bot das Shuttle mit seinem Roboterarm die Möglichkeit, die Module direkt an die Station zu montieren. Das war unumgänglich, da die meisten ISS-Module keine eigenen Antriebs- und Lageregelungssysteme haben und so ein autonomes Andocken nicht möglich war. Auch der Crew-Transport wurde mit dem Shuttle vereinfacht; theoretisch konnten bis zu 5 Besatzungsmitglieder pro Flug ausgetauscht werden.</p>
        
        <p>Wegen dieser kritischen Rolle des Shuttle wurde das ISS-Programm dann auch um mehrere Jahre zurückgeworfen, als die Shuttle-Flotte nach der <a href="https://de.wikipedia.org/wiki/STS-107" title="STS-107">Columbiakatastrophe</a> im Februar 2003 mit einem Flugverbot belegt wurde. Einige Experimente mussten deshalb sogar gestrichen werden.</p>
        
        <p>Vor der Zeit der ISS wurde das Shuttle auch auf mehreren Flügen zur russischen Raumstation <a href="https://de.wikipedia.org/wiki/Mir_(Raumstation)" title="Mir (Raumstation)">Mir</a> eingesetzt. Zwischen 1995 und 1998 dockte insgesamt neunmal eine Raumfähre an der Station an. Dabei ging es auch um ein politisches Zeichen – es war die erste nennenswerte gemeinsame Operation der beiden Supermächte im Weltraum seit dem <a href="https://de.wikipedia.org/wiki/Apollo-Sojus-Test-Projekt" title="Apollo-Sojus-Test-Projekt">Apollo-Sojus-Testprojekt</a> im Jahre 1975. Der erste derartige Flug war <a href="https://de.wikipedia.org/wiki/STS-71" title="STS-71">STS-71</a> im Sommer 1995.</p>
        
        <h2>Wartung und Aufrüstung</h2>
        
        <p>Aus sicherheits- und flugtechnischen Gründen wurden alle Orbiter mehrmals für umfangreiche Verbesserungen monatelang außer Dienst gestellt. Während dieser sogenannten <i>Orbiter Maintenance Down Period</i> (OMDP), die nach etwa 13 Flügen anstanden, wurden umfangreiche Tests und Wartungsarbeiten an der Raumfähre durchgeführt. Zusätzlich wurden jeweils größere Verbesserungen vorgenommen. Während der letzten derartigen Revision wurden die Orbiter mit einem sogenannten <a href="https://de.wikipedia.org/wiki/EFIS" class="mw-redirect" title="EFIS">Glascockpit</a> auf LCD-Basis ausgerüstet, das die alten Röhrenbildschirme und analogen Instrumente ersetzte. Weitere Verbesserungen waren unter anderem ein Bremsschirm, der bei der Landung zum Einsatz kam, und das <i>Station-to-Shuttle-Power-Transfer-System</i>, das es dem Shuttle erlaubte, bei einem Aufenthalt an der ISS Strom von der Station zu beziehen. Solche Modifikationen fanden zunächst im Herstellerwerk im kalifornischen Pasadena statt, wurden aber Ende der 1990er Jahre in die <a href="https://de.wikipedia.org/wiki/Orbiter_Processing_Facility" title="Orbiter Processing Facility">Orbiter Processing Facility</a> (OPF) verlegt, in der auch die Wartung und Vorbereitung der Raumfähren durchgeführt wurde.</p>
        
        <p>Auch nach dem Challenger-Unglück wurden diverse Verbesserungen vorgenommen, bei denen in erster Linie die Boosterverbindungen zum Außentank verstärkt wurden. Die Änderungen nach der Columbia-Katastrophe betrafen hauptsächlich die Schaumstoffisolierung des externen Tanks. Diese sollte dadurch nicht mehr so leicht abplatzen und den Hitzeschutzschild des Shuttle beschädigen können. Darüber hinaus wurden Sicherheitsbedingungen und Startkriterien verschärft.</p>

        <h2>Missionen</h2>
        <p>Seit dem Beginn der Shuttle-Flüge im Jahr 1981 waren insgesamt fünf verschiedene Space
            Shuttles ins All geflogen. Davon waren bis zur Einstellung des Programms im Jahre 2011 noch drei (<a
                href="https://de.wikipedia.org/wiki/Discovery_(Raumf%C3%A4hre)">Discovery</a>, <a
                href="https://de.wikipedia.org/wiki/Atlantis_(Raumf%C3%A4hre)">Atlantis</a> und <a
                href="https://de.wikipedia.org/wiki/Endeavour_(Raumf%C3%A4hre)">Endeavour</a>) im Einsatz. Zwei Space
            Shuttles (<a href="https://de.wikipedia.org/wiki/Challenger_(Raumf%C3%A4hre)">Challenger</a>und <a
                href="https://de.wikipedia.org/wiki/Columbia_(Raumf%C3%A4hre)">Columbia</a>) wurden bei Unglücken in den
            Jahren 1986 und 2003 zerstört.</p>

        <figure class="table">
            <table>
                <thead>
                    <tr>
                        <th
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;text-align:center;">
                            Name</th>
                        <th
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;text-align:center;width:100px;">
                            OV-Nr.</th>
                        <th
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;text-align:center;">
                            Erster<br>Start/Mission</th>
                        <th
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;text-align:center;">
                            Letzter<br>Start/Mission</th>
                        <th
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;text-align:center;">
                            Anzahl<br>Miss.</th>
                        <th
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;text-align:center;">
                            Bemerkung</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            <a href="https://de.wikipedia.org/wiki/Columbia_(Raumf%C3%A4hre)">Columbia</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            OV-102</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            12.&nbsp;Apr.&nbsp;1981<br><a href="https://de.wikipedia.org/wiki/STS-1">STS-1</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            16. Jan. 2003<br><a href="https://de.wikipedia.org/wiki/STS-107">STS-107</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            28</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            erster raumflugfähiger Orbiter, am 1. Februar 2003 beim Wiedereintritt durch defekte
                            Hitzeschutzverkleidung zerstört. Alle 7 Besatzungsmitglieder kamen dabei ums Leben.</td>
                    </tr>
                    <tr>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            <a href="https://de.wikipedia.org/wiki/Challenger_(Raumf%C3%A4hre)">Challenger</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            OV-099</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            4. Apr. 1983<br><a href="https://de.wikipedia.org/wiki/STS-6">STS-6</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            28. Jan. 1986<br><a href="https://de.wikipedia.org/wiki/STS-51-L">STS-51-L</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            10</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            am 28. Januar 1986 kurz nach dem Start durch einen Defekt an einem Feststoffbooster zerstört.
                            Alle 7 Besatzungsmitglieder kamen dabei ums Leben.</td>
                    </tr>
                    <tr>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            <a href="https://de.wikipedia.org/wiki/Discovery_(Raumf%C3%A4hre)">Discovery</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            OV-103</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            30.&nbsp;Aug.&nbsp;1984<br><a href="https://de.wikipedia.org/wiki/STS-41-D">STS-41-D</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            24.&nbsp;Feb.&nbsp;2011<br><a href="https://de.wikipedia.org/wiki/STS-133">STS-133</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            39</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            letzte Landung am 9. März 2011,<br><a href="https://de.wikipedia.org/wiki/Exponat">Exponat</a>
                            im <a href="https://de.wikipedia.org/wiki/Steven_F._Udvar-Hazy_Center">Steven F. Udvar-Hazy
                                Center</a> seit dem 19. April 2012</td>
                    </tr>
                    <tr>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            <a href="https://de.wikipedia.org/wiki/Atlantis_(Raumf%C3%A4hre)">Atlantis</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            OV-104</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            3. Okt. 1985<br><a href="https://de.wikipedia.org/wiki/STS-51-J">STS-51-J</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            8. Jul. 2011<br><a href="https://de.wikipedia.org/wiki/STS-135">STS-135</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            33</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            letzte Landung am 21. Juli 2011,<br>Exponat im <a
                                href="https://de.wikipedia.org/wiki/Kennedy_Space_Center">Kennedy Space Center</a></td>
                    </tr>
                    <tr>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            <a href="https://de.wikipedia.org/wiki/Endeavour_(Raumf%C3%A4hre)">Endeavour</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            OV-105</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            7. Mai 1992<br><a href="https://de.wikipedia.org/wiki/STS-49">STS-49</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            16. Mai 2011<br><a href="https://de.wikipedia.org/wiki/STS-134">STS-134</a></td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            25</td>
                        <td
                            style="border-bottom:1px solid rgb(162, 169, 177);border-left:1px solid rgb(162, 169, 177);border-right:1px solid rgb(162, 169, 177);border-top:1px solid rgb(162, 169, 177);padding:0.2em 0.4em;">
                            letzte Landung am 1. Juni 2011, Ersatzorbiter für Challenger, Exponat im <a
                                href="https://de.wikipedia.org/wiki/California_Science_Center">California Science Center</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </figure>
        
        <p>Basiert auf: <a href="https://de.wikipedia.org/wiki/Space_Shuttle">Wikipedia | Space Shuttle</a></p>
        
        <blockquote><p>Der Text ist unter der Lizenz <a href="https://de.wikipedia.org/wiki/Wikipedia:Lizenzbestimmungen_Commons_Attribution-ShareAlike_3.0_Unported">„Creative Commons Attribution/Share Alike“</a> verfügbar</p></blockquote>
    
    </div>
</div>
';

$fragment = new rex_fragment();
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
