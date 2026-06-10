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

                    <figure class="image">
                        <img src="/assets/addons/cke5/images/frankfurt_skyline.jpg" alt="Frankfurt am Main Skyline">
                        <figcaption>Frankfurt am Main – Foto von <a href="https://pixabay.com/users/leonhard_niederwimmer-1131094/" target="_blank" rel="noopener">Leonhard_Niederwimmer</a> auf <a href="https://pixabay.com/photos/building-horizon-city-skyscraper-7092747/" target="_blank" rel="noopener">Pixabay</a> (Pixabay Content License)</figcaption>
                    </figure>

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

                    <h2>Frankfurt – Finanzplatz und Kulturstadt</h2>

                    <p>Frankfurt am Main ist mit rund 760.000 Einwohnern die fünftgrößte Stadt Deutschlands und das bedeutendste Finanzzentrum des Euroraums. Die charakteristische Skyline mit ihren Hochhäusern – im Volksmund auch „Mainhattan" genannt – ist das unverwechselbare Gesicht der Stadt.</p>

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
                        </tbody>
                    </table>

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
