<?php
/** @var rex_addon $this */

use Cke5\Provider\Cke5NavigationProvider;
use Cke5\Utils\Cke5Lang;

$content = '<div class="document-outline-container-not-provided"></div>';
$content .= Cke5NavigationProvider::getMainSubNavigationHeader() .
           Cke5NavigationProvider::getSubNavigation('main') . '
<div class="cke5-demo">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div name="content" id="editor" class="cke5-editor" data-profile="demo_default" data-lang="' . Cke5Lang::getUserLang() . '">
                    <h1>🏙️ Frankfurt am Main – Skyline einer Metropole</h1>

                    <p>Diese Demo ist bewusst wie ein echter Inhaltsartikel aufgebaut: längerer Fließtext, Bild mit Credits, Zitat, Listen, Tabelle und ein hervorgehobener Abschnitt. Die Style-Bar ist dafür gedacht, solche Muster sauber und ohne Spezial-Block zu markieren.</p>

                    <section class="for-section-highlight panel panel-info">
                        <div class="panel-heading">
                            <h2 class="panel-title">Über Friends of REDAXO</h2>
                        </div>
                        <div class="panel-body">
                            <figure class="image image-style-align-right" style="width:180px; margin: 0 0 1rem 1.5rem;">
                                <img src="/assets/addons/cke5/images/for_logo.png" alt="Friends of REDAXO Logo">
                            </figure>
                            <p><strong>Friends of REDAXO</strong> ist eine offene Community von Entwicklerinnen und Entwicklern, die gemeinsam Addons, Demos und Werkzeuge rund um das REDAXO CMS pflegen und weiterentwickeln.</p>
                            <p>Aktuell umfasst die Community über 69 Mitglieder und 255 Projekte auf GitHub – von kleinen Helfern bis zu vollständigen Website-Demos.</p>
                            <p>
                                <a class="btn btn-primary" href="https://friendsofredaxo.github.io" target="_blank" rel="noopener">Website besuchen</a>
                                <a class="btn btn-default" href="https://friendsofredaxo.github.io/info" target="_blank" rel="noopener">Über das Projekt</a>
                            </p>
                        </div>
                    </section>

                    <figure class="image">
                        <img src="/assets/addons/cke5/images/frankfurt_skyline.jpg" alt="Frankfurt am Main Skyline">
                        <figcaption>Frankfurt am Main – Foto von <a href="https://pixabay.com/users/leonhard_niederwimmer-1131094/" target="_blank" rel="noopener">Leonhard_Niederwimmer</a> auf <a href="https://pixabay.com/photos/building-horizon-city-skyscraper-7092747/" target="_blank" rel="noopener">Pixabay</a> (Pixabay Content License)</figcaption>
                    </figure>

                    <h2>Frankfurt – Finanzplatz, Kulturstadt und REDAXO-Heimat</h2>

                    <p>Frankfurt am Main ist mit rund 760.000 Einwohnern die fünftgrößte Stadt Deutschlands und das bedeutendste Finanzzentrum des Euroraums. Die charakteristische Skyline mit ihren Hochhäusern – im Volksmund auch „Mainhattan" genannt – ist das unverwechselbare Gesicht der Stadt.</p>

                    <p>Frankfurt ist außerdem die Heimat von <a href="https://www.yakamara.de" target="_blank" rel="noopener"><strong>Yakamara</strong></a> – der Agentur hinter REDAXO. Aus Frankfurt heraus wird das Open-Source-CMS entwickelt und gepflegt, das heute von tausenden Websites und Agenturen weltweit eingesetzt wird.</p>

                    <p><strong>Die wichtigsten Institutionen auf einen Blick:</strong></p>
                    <ul>
                        <li>Europäische Zentralbank (EZB)</li>
                        <li>Deutsche Bundesbank</li>
                        <li>Frankfurter Wertpapierbörse (XETRA)</li>
                        <li>Flughafen Frankfurt – einer der meistfrequentierten in Europa</li>
                    </ul>

                    <h2>Stadtteile im Überblick (Tabelle)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Stadtteil</th>
                                <th>Charakter</th>
                                <th>Bekannt für</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sachsenhausen</td>
                                <td>Altstadt &amp; Kultur</td>
                                <td>Äppelwoi, Museumsufer</td>
                            </tr>
                            <tr>
                                <td>Innenstadt / Bankenviertel</td>
                                <td>Geschäft &amp; Finanzen</td>
                                <td>EZB, Skyline, Zeil</td>
                            </tr>
                            <tr>
                                <td>Nordend / Bornheim</td>
                                <td>Wohnen &amp; Szene</td>
                                <td>Berger Straße, Cafés</td>
                            </tr>
                            <tr>
                                <td>Römerberg</td>
                                <td>Geschichte &amp; Tourismus</td>
                                <td>Historisches Rathaus, Paulskirche</td>
                            </tr>
                            <tr>
                                <td>Schwanheim</td>
                                <td>Wohnen &amp; Natur</td>
                                <td>Niddaauen, Mainufer, Streuobstwiesen – Lieblingsort von <a href="https://github.com/skerbis" target="_blank" rel="noopener">Thomas Skerbis</a></td>
                            </tr>
                        </tbody>
                    </table>

                    <p>
                        <figure class="image image-style-align-left" style="width:80px; margin: 0 1.2rem 0.5rem 0;">
                            <img src="/assets/addons/cke5/images/skerbis.jpg" alt="Thomas Skerbis" style="border-radius:50%;">
                        </figure>
                        <a href="https://github.com/skerbis" target="_blank" rel="noopener"><strong>Thomas Skerbis</strong></a> hat einen Lieblingsort in Schwanheim und ist einer der aktivsten Köpfe hinter Friends of REDAXO. Als langjähriger Core-Contributor pflegt er Addons, schreibt Dokumentation und treibt das Ökosystem rund um REDAXO voran.
                    </p>

                    <blockquote>
                        <p><strong>Empfehlung:</strong> Erst Styles/Klassen nutzen. Eigene Block-Widgets nur dann bauen, wenn echte Struktur- oder Validierungslogik notwendig ist.</p>
                    </blockquote>

                    <h2>REDAXO-spezifische Funktionen</h2>
                    <p>Für REDAXO sind besonders die integrierten Elemente wichtig: interne Links, Medienpool-Links und Dateiverweise können direkt im Editor gesetzt werden. Dadurch bleiben Inhalte redaktionell pflegbar und technisch sauber.</p>
                    <ul>
                        <li><strong>Interne Verlinkung:</strong> Seiten aus der Struktur auswählen</li>
                        <li><strong>Medienpool:</strong> Dateien direkt einbinden</li>
                        <li><strong>Link-Dekoratoren:</strong> z.\u00a0B. Buttons wie <code>btn btn-primary</code></li>
                    </ul>

                    <h2>Auto-Replace und Schreibfluss</h2>
                    <p>Beim Schreiben greifen hilfreiche Automatiken: Aus einfachen Eingaben entstehen saubere Formatierungen. Beispiele sind typografische Ersetzungen, automatische Listen oder das Umwandeln in Überschriften.</p>
                    <p>So bleibt der Fokus auf dem Inhalt statt auf HTML-Details.</p>

                    <h2>Optional: Navigation mit Klasse</h2>
                    <nav class="for-toc">
                        <strong>Abschnitts-Navigation:</strong>
                        <ul>
                            <li><a href="#">Einleitung</a></li>
                            <li><a href="#">Friends of REDAXO</a></li>
                            <li><a href="#">Frankfurt</a></li>
                        </ul>
                    </nav>

                    <h2>Dank &amp; Credits</h2>

                    <p>Ein herzliches Dankeschön geht an <a href="https://github.com/joachimdoerr" target="_blank" rel="noopener"><strong>Joachim Dörr</strong></a>, der das CKEditor 5 AddOn für REDAXO entwickelt und über viele Jahre gepflegt hat. Ohne seinen Einsatz und sein Durchhaltevermögen würde dieses Addon nicht existieren.</p>

                    <p>Ebenso herzlichen Dank an alle, die durch Issues, Pull Requests und Feedback zur Verbesserung des Addons beigetragen haben:</p>

                    <ul>
                        <li><a href="https://github.com/crydotsnake" target="_blank" rel="noopener">crydotsnake</a></li>
                        <li><a href="https://github.com/interweave-media" target="_blank" rel="noopener">interweave-media</a></li>
                        <li><a href="https://github.com/staabm" target="_blank" rel="noopener">staabm</a></li>
                        <li><a href="https://github.com/nandes2062" target="_blank" rel="noopener">nandes2062</a></li>
                        <li><a href="https://github.com/TobiasKrais" target="_blank" rel="noopener">TobiasKrais</a></li>
                        <li><a href="https://github.com/schuer" target="_blank" rel="noopener">schuer</a></li>
                        <li><a href="https://github.com/marcohanke" target="_blank" rel="noopener">marcohanke</a></li>
                        <li><a href="https://github.com/eaCe" target="_blank" rel="noopener">eaCe</a></li>
                        <li><a href="https://github.com/aeberhard" target="_blank" rel="noopener">aeberhard</a></li>
                        <li><a href="https://github.com/Bio-GitHub" target="_blank" rel="noopener">Bio-GitHub</a></li>
                        <li><a href="https://github.com/dergel" target="_blank" rel="noopener">dergel</a></li>
                        <li><a href="https://github.com/V-Simos" target="_blank" rel="noopener">V-Simos</a></li>
                        <li><a href="https://github.com/VIEWSION" target="_blank" rel="noopener">VIEWSION</a></li>
                        <li><a href="https://github.com/ynamite" target="_blank" rel="noopener">ynamite</a></li>
                        <li><a href="https://github.com/ischfr" target="_blank" rel="noopener">ischfr</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default cke5-demo-sidebar">
                    <div class="panel-heading">
                        <h3 class="panel-title">Hinweise zur Demo</h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <strong>Styles statt eigener Blocks:</strong> Für Text+Bild, Callouts und Hintergrundabschnitte sind Klassen meist die beste Lösung.
                        </div>
                        <p>Was hier demonstriert wird:</p>
                        <ul>
                            <li>Artikel mit echtem Inhaltsfluss</li>
                            <li>Vollbreites Bild mit Bildunterschrift &amp; Credits</li>
                            <li>Hervorgehobener Abschnitt mit FoR-Logo (Bootstrap 3)</li>
                            <li>Tabelle mit Kopf- und Inhaltszeilen</li>
                            <li>Navigation/Anker als klassischer Stil-Anwendungsfall</li>
                        </ul>

                        <h4>CKEditor 5 Highlights</h4>
                        <ul>
                            <li>Tabellen-Tooling mit strukturiertem Markup</li>
                            <li>Find-and-Replace für schnelle Korrekturen</li>
                            <li>Sonderzeichen und saubere Zwischenablage</li>
                            <li>Styles für wiederkehrende Inhaltsmuster</li>
                        </ul>

                        <h4>REDAXO Features</h4>
                        <ul>
                            <li>Interne Seitenlinks direkt aus der Struktur</li>
                            <li>Medienpool-Verknüpfung ohne manuelle Pfade</li>
                            <li>Link-Dekoratoren für Buttons und Varianten</li>
                        </ul>

                        <h4>Friends of REDAXO</h4>
                        <p class="small">69 Mitglieder &bull; 255 Projekte &bull; 6342 Sterne auf GitHub</p>
                        <p class="small">
                            <a href="https://friendsofredaxo.github.io" target="_blank" rel="noopener">friendsofredaxo.github.io</a><br>
                            <a href="https://github.com/FriendsOfREDAXO" target="_blank" rel="noopener">github.com/FriendsOfREDAXO</a>
                        </p>

                        <div class="well well-sm">
                            <strong>Tipp:</strong> Wenn ein Stil immer dieselbe Struktur braucht, lohnt er sich als Style-Gruppe oder Link-/Abschnitts-Klasse.
                        </div>
                        <p class="small text-muted">
                            Foto: <a href="https://pixabay.com/users/leonhard_niederwimmer-1131094/" target="_blank" rel="noopener">Leonhard_Niederwimmer</a> /
                            <a href="https://pixabay.com/photos/building-horizon-city-skyscraper-7092747/" target="_blank" rel="noopener">Pixabay</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
