# Normalizálást gyakorló plugin 
## A plugin célja
A plugin arra szolgál, hogy a hallgatóknak segítsen a 2NF, 3NF és BCNF elsajátításában példafeladatokon való gyakorláson keresztül. A pluginben 3 különféle nehézségű feladat közül tudnak választani, és a megadott válasz után láthatják a jó megoldást és a pontszámot amit kaptak. A tanár pedig egy adatbázison keresztül láthatja, hogy a hallgató milyen eredményt ért el és milyen válaszokat adott az adott feladatra.

## A plugin beüzemelése
A plugint a Moodle rendszeren keresztül lehet használni. A Moodle egy ingyenes open-source oktatási platform, így bárki fel tudja telepíteni és használni. A Moodle-höz egy PHP nyelvet támogató szerverre (például Apache) és egy MySQL adatbázisra lesz szükség.

## A plugin egyéb módosításai
A pluginen belül kevés módosítást lehet végrehajtani a feladat komplexitásából adódóan, viszont a Solution.php file-ban a 14. sorban a maxpontszám változó értékét érdemes módosítani, amely az egyes alfeladatok maximálisan elérhető pontszámát érti.
Egy pontszám kiszámítása gráfon belüli műveletek számával történik. A lehetséges műveletek:
- Csúcs beillesztés
- Csúcs törlés
- Csúcs módosítás
- Él beillesztés
- Él törlés
- Él módosítás
 
Ha a megoldás gráf a diáktól adott választól x művelettel tér el, akkor a pontszám:

`Összpontszám - (x1 * w1) + ... + (x6 * w6) `

,ahol az `xi` a műveletek száma, a `wi` pedig a művelethez tartozó hibasúly.
A súlyokat pedig a Graph.py-on belül a *nodeInsCost*, *edgeInsCost*, *nodeDelCost* és *edgeDelCost* függvényekkel lehet változtatni.