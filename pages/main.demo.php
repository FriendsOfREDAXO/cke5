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
                    <h1>😀 Space Shuttle Programm</h1>

                    <p>Diese Demo ist bewusst wie ein echter Inhaltsartikel aufgebaut: längerer Fliesstext, Bild, Zitat, Listen, Tabelle und ein hervorgehobener Abschnitt. Die Style-Bar ist dafür gedacht, solche Muster sauber und ohne Spezial-Block zu markieren.</p>

                    <section class="for-section-highlight panel panel-info">
                        <div class="panel-heading">
                            <h2 class="panel-title">Hinterlegter Abschnitt</h2>
                        </div>
                        <div class="panel-body">
                            <p>Ein Abschnitt mit Klasse ist in REDAXO meist die pragmatischste Lösung. Über Styles kann man ihn im Backend auswählbar machen und im Frontend konsistent rendern.</p>
                            <p><a class="btn btn-primary" href="#">Call-to-Action</a></p>
                        </div>
                    </section>

                    <h2>Die Story</h2>

                    <p>Nach dem letzten <a href="https://de.wikipedia.org/wiki/Apollo-Programm" title="Apollo-Programm">Apollo</a>-Flug 1975 war das Shuttle ab 1981 das Arbeitspferd der NASA. Der erste Flug des Systems fand am 12. April 1981 statt, seither wurden insgesamt 135 Flüge durchgeführt, wobei es zu zwei fatalen Unfällen kam.</p>
                    <p><strong>Zu den wichtigsten Erfolgen gehören</strong></p>
                    <ol>
                        <li>die Aussetzung diverser Raumsonden</li>
                        <li>sowie des <a href="https://de.wikipedia.org/wiki/Hubble-Weltraumteleskop">Hubble-Weltraumteleskops</a></li>
                        <li>diverse Flüge mit eingebauten Laboratorien sowie Flüge zur Mir und zur ISS</li>
                    </ol>

                    <h2>Missionen im Überblick (Tabelle)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Mission</th>
                                <th>Jahr</th>
                                <th>Schwerpunkt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>STS-1</td>
                                <td>1981</td>
                                <td>Erster Orbiter-Testflug</td>
                            </tr>
                            <tr>
                                <td>STS-31</td>
                                <td>1990</td>
                                <td>Aussetzen von Hubble</td>
                            </tr>
                            <tr>
                                <td>STS-88</td>
                                <td>1998</td>
                                <td>ISS-Aufbau (erste Bauteile)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h2>Text + Bild als flexibler Inhaltsblock</h2>
                    <figure class="image image-style-align-right" style="width:38%;">
                        <img src="/assets/addons/cke5/images/Space_Shuttle_Program_Commemorative_Patch.png" alt="Beispielbild">
                        <figcaption>Beispielbild mit rechts ausgerichteter Darstellung.</figcaption>
                    </figure>
                    <p>Der „Text+Bild“-Anwendungsfall braucht oft keinen eigenen technischen Block. Mit Bildausrichtung, Klassen und klaren Content-Regeln bleibt alles im Standard-Editor-Flow.</p>
                    <p>Genau hier spielen die Styles ihre Stärke aus: eine Klasse für den Abschnitt, bestehende Bild-Styles für das Layout und Link-Dekoratoren für Knöpfe oder Call-to-Actions.</p>

                    <blockquote>
                        <p><strong>Empfehlung:</strong> Erst Styles/Klassen nutzen. Eigene Block-Widgets nur dann bauen, wenn echte Struktur- oder Validierungslogik notwendig ist.</p>
                    </blockquote>

                    <h2>REDAXO-spezifische Funktionen</h2>
                    <p>Für REDAXO sind besonders die integrierten Elemente wichtig: interne Links, Medienpool-Links und Dateiverweise können direkt im Editor gesetzt werden. Dadurch bleiben Inhalte redaktionell pflegbar und technisch sauber.</p>
                    <ul>
                        <li><strong>Interne Verlinkung:</strong> Seiten aus der Struktur auswählen</li>
                        <li><strong>Medienpool:</strong> Dateien direkt einbinden</li>
                        <li><strong>Link-Dekoratoren:</strong> z. B. Buttons wie <code>btn btn-primary</code></li>
                    </ul>

                    <h2>Auto-Replace und Schreibfluss</h2>
                    <p>Beim Schreiben greifen hilfreiche Automatiken: Aus einfachen Eingaben entstehen saubere Formatierungen. Beispiele sind typografische Ersetzungen, automatische Listen oder das Umwandeln in Überschriften.</p>
                    <p>So bleibt der Fokus auf dem Inhalt statt auf HTML-Details.</p>

                    <h2>Nutzung des Orbiter</h2>
                    <p>Durch seine Bauart als Raumfähre bedingt war das Space Shuttle extrem flexibel einsetzbar. Es war das einzige Trägersystem, das mehrere Tonnen Nutzlast vom Weltraum zur Erde bringen konnte. Zudem konnten einige Komponenten der ISS aufgrund ihrer Abmessungen nur mit dem Shuttle ins All gebracht werden.</p>

                    <h2>Satellitentransport</h2>
                    <p>Zu Beginn des Shuttle-Programms lag die Hauptaufgabe des Orbiters darin, Satelliten ins All zu bringen. Daneben konnte man mit dem Shuttle auch Satelliten einfangen, um sie durch Astronauten reparieren zu lassen.</p>

                    <h2>Optional: Navigation mit Klasse</h2>
                    <nav class="for-toc">
                        <strong>Abschnitts-Navigation:</strong>
                        <ul>
                            <li><a href="#">Einleitung</a></li>
                            <li><a href="#">Hauptteil</a></li>
                            <li><a href="#">Fazit</a></li>
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
                            <li>linker Artikel mit echtem Inhaltsfluss</li>
                            <li>hervorgehobener Abschnitt mit Bootstrap 3</li>
                            <li>Tabelle mit Kopf- und Inhaltszeilen</li>
                            <li>Bild + Text als typischer Teaser</li>
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
                            <li>interne Seitenlinks direkt aus der Struktur</li>
                            <li>Medienpool-Verknüpfung ohne manuelle Pfade</li>
                            <li>Link-Dekoratoren für Buttons und Varianten</li>
                        </ul>

                        <h4>Auto-Replace im Alltag</h4>
                        <p class="small">Typische Schreibmuster werden automatisch verbessert: Listen, einfache Strukturzeichen und saubere Typografie. Das beschleunigt die redaktionelle Arbeit spürbar.</p>

                        <div class="well well-sm">
                            <strong>Tipp:</strong> Wenn ein Stil immer dieselbe Struktur braucht, lohnt er sich als Style-Gruppe oder Link-/Abschnitts-Klasse.
                        </div>
                        <p class="small text-muted">Die rechten Hinweise orientieren sich an der TinyMCE-Demo-Idee, bleiben aber bewusst Bootstrap-3-nah und REDAXO-typisch.</p>
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
