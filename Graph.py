class Vonal(object):
    def __init__(self, x1, y1, x2, y2, arrowX, id=0):
        self.x1 = x1
        self.y1 = y1
        self.x2 = x2
        self.y2 = y2
        self.arrowX = arrowX
        self.id = id

    def __str__(self):
        return "Vonal{" + "x1=" + \
               str(self.x1) + \
               ", y1=" + str(self.y1) + \
               ", x2=" + str(self.x2) + \
               ", y2=" + str(self.y2) + \
               ", arrowX=" + str(self.arrowX) + \
               ", id=" + str(self.id) + '}'


class Attributum(object):
    def __init__(self, id, nev, kulcs, gyengekulcs, szarmaztatott, tobberteku, x1=0, y1=0, x2=0, y2=0):
        self.id = id
        self.nev = nev
        self.kulcs = kulcs
        self.gyengekulcs = gyengekulcs
        self.szarmaztatott = szarmaztatott
        self.tobberteku = tobberteku
        self.x1 = x1
        self.y1 = y1
        self.x2 = x2
        self.y2 = y2
        self.attributumok = list()

    def __str__(self):
        return "Attributum{" + \
               "id='" + str(self.id) + \
               ", nev='" + str(self.nev) + \
               ", kulcs=" + str(self.kulcs) + \
               ", gyengekulcs=" + str(self.gyengekulcs) + \
               ", szarmaztatott=" + str(self.szarmaztatott) + \
               ", tobberteku=" + str(self.tobberteku) + \
               ", x1=" + str(self.x1) + \
               ", y1=" + str(self.y1) + \
               ", x2=" + str(self.x2) + \
               ", y2=" + str(self.y2) + \
               '}'

    def __eq__(self, other):
        return self.nev == other.nev and self.kulcs == other.kulcs and self.szarmaztatott == other.szarmaztatott and self.tobberteku == other.tobberteku

    def ujAttributum(self, attr):
        if isinstance(attr, Attributum):
            self.attributumok.append(attr)


class Egyed(object):
    def __init__(self, id, nev, gyenge, tarsitott, x1, y1, x2, y2):
        self.id = id
        self.nev = nev
        self.gyenge = gyenge
        self.tarsitott = tarsitott
        self.x1 = x1
        self.y1 = y1
        self.x2 = x2
        self.y2 = y2
        self.attributumok = list()

    def ujAttributum(self, attr):
        if isinstance(attr, Attributum):
            self.attributumok.append(attr)

    def getAttributumok(self):
        print("\n", self.nev)
        for attr in self.attributumok:
            print(attr.nev)

    def __str__(self):
        return "Egyed{" + \
               "id=" + str(self.id) + \
               ", nev='" + self.nev + "'" + \
               ", gyenge=" + str(self.gyenge) + \
               ", tarsitott=" + str(self.tarsitott) + \
               ", attributumok=" + str(self.attributumok) + \
               ", x1=" + str(self.x1) + \
               ", y1=" + str(self.y1) + \
               ", x2=" + str(self.x2) + \
               ", y2=" + str(self.y2) + \
               '}'

    def __eq__(self, other):
        return self.nev == other.nev and self.gyenge == other.gyenge and self.tarsitott == other.tarsitott


# RelationDbScheme.py -hoz:
class Sema(Egyed):
    def __init__(self, id, nev, hivatkozik=False):
        self.hivatkozik = hivatkozik
        super().__init__(id, nev, False, False, 0, 0, 0, 0)

    def __eq__(self, other):
        return self.nev == other.nev


class RelaciosAttributum(Attributum):
    def __init__(self, id, nev, kulcs, kulsokulcs):
        self.kulsokulcs = kulsokulcs
        super().__init__(id, nev, kulcs, kulsokulcs, 0, 0, 0, 0)

    def __eq__(self, other):
        return self.nev == other.nev and self.kulcs == other.kulcs and self.kulsokulcs == other.kulsokulcs


class Kapcsolat(object):
    def __init__(self, id, nev, x1, y1, x2, y2):
        self.id = id
        self.nev = nev
        self.x1 = x1
        self.y1 = y1
        self.x2 = x2
        self.y2 = y2
        self.egyedek = list()
        self.attributumok = list()

    def ujEgyed(self, e):
        if isinstance(e, Egyed):
            self.egyedek.append(e)

    def ujAttributum(self, a):
        if isinstance(a, Attributum):
            self.attributumok.append(a)

    def __str__(self):
        return "Kapcsolat{" + \
               "nev='" + self.nev + "'" + \
               ", egyedek=" + str(self.egyedek) + \
               ", attributumok=" + str(self.attributumok) + \
               ", x1=" + str(self.x1) + \
               ", y1=" + str(self.y1) + \
               ", x2=" + str(self.x2) + \
               ", y2=" + str(self.y2) + \
               '}'


class SpecializacioKapcsolat(Kapcsolat):
    def __init__(self, id, nev, x1, y1, x2, y2):
        super().__init__(id, nev, x1, y1, x2, y2)


class FullGraph(object):
    def __init__(self, egyedek, attributumok, kapcsolatok):
        self.egyedek = egyedek
        self.attributumok = attributumok
        self.kapcsolatok = kapcsolatok

    def ujEgyed(self, e):
        #if isinstance(e, Egyed):
        self.egyedek.append(e)

    def ujAttributum(self, a):
        if isinstance(a, Attributum):
            self.attributumok.append(a)

    def ujKapcsolat(self, k):
        if isinstance(k, Kapcsolat):
            self.kapcsolatok.append(k)

    def getNodeDictionary(self):
        dictionary = dict()
        for e in self.egyedek:
            dictionary[e.id] = e.nev
        for a in self.attributumok:
            dictionary[a.id] = a.nev
        for k in self.kapcsolatok:
            dictionary[k.id] = k.nev
        return dictionary


class SqlParancs:
    def __init__(self, id, nev):
        self.id = id
        self.nev = nev
        self.attributumok = list()

    def ujAttributum(self, a):
        if isinstance(a, SqlParancs):
            self.attributumok.append(a)


"""def nodeMatch(a, b):
    if isinstance(a['object'], Attributum) and isinstance(b['object'], Attributum):
        return a['object'] == b['object']
    elif isinstance(a['object'], Egyed) and isinstance(b['object'], Egyed):
        return a['object'] == b['object']
    return False"""


def nodeMatch(a, b):
    return a['label'] == b['label']


def edgeMatch(e1, e2):
    if "d" in e1:  # ha irányított
        return e1['e'] == e2['e']

    e1s = e1['e'].split("-")
    e1fs = e1s[1] + "-" + e1s[0]
    return e1['e'] == e2['e'] or e1fs == e2['e']


def nodeInsCost(a):
    return 0.25


def edgeInsCost(a):
    return 0.25


def nodeDelCost(a):
    return 0.25


def edgeDelCost(a):
    return 0.25